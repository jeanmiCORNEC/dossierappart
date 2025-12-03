<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use setasign\Fpdi\Fpdi;
use Spatie\PdfToImage\Pdf;

class PdfSanitizerService
{
    private const SETTINGS = [
        'resolution' => 150,           // 150 DPI (Suffisant pour lecture écran/print standard, gain CPU important)
        'jpeg_quality' => 85,          // 85% qualité (Bon compromis taille/qualité)
        'watermark_opacity' => 0.15,   // 15% opacité (OCR-friendly)
        'watermark_angle' => 45,       // Diagonal
        'font_size_ratio' => 25,       // 1/25 de la largeur
    ];

    public function sanitizeDocument(Document $doc): string
    {
        $originalPath = Storage::disk('local')->path($doc->storage_path);
        $extension = strtolower(pathinfo($doc->original_filename, PATHINFO_EXTENSION));
        $isPdf = $extension === 'pdf';

        // Créer dossier temp si n'existe pas
        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        if ($isPdf) {
            return $this->sanitizePdf($originalPath, $doc);
        } else {
            return $this->sanitizeImage($originalPath, $doc);
        }
    }

    public function sanitizePdf(string $pdfPath, Document $doc): string
    {
        Log::info("Sanitizing PDF with CLI (convert): " . $pdfPath);

        try {
            // 1. EXPLOSION : PDF -> Images JPG (une par page)
            $tempDir = storage_path("app/temp/pdf_{$doc->id}");
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $pagePattern = $tempDir . '/page-%d.jpg';

            // Commande convert : CRITIQUE de mettre -density AVANT le PDF
            // -colorspace sRGB -depth 8 : Force RGB 8-bit pour GD (sinon grayscale 12-bit non lisible)
            // -auto-orient : Corrige l'orientation selon les métadonnées EXIF (photos mobile)
            $cmd = sprintf(
                "convert -density %d -quality %d -colorspace sRGB -depth 8 -auto-orient %s %s 2>&1",
                self::SETTINGS['resolution'],
                self::SETTINGS['jpeg_quality'],
                escapeshellarg($pdfPath),
                escapeshellarg($pagePattern)
            );

            exec($cmd, $output, $returnCode);

            if ($returnCode !== 0) {
                Log::error("Erreur convert PDF->JPG: " . implode("\n", $output));
                throw new \Exception("Conversion PDF échouée: " . implode("\n", $output));
            }

            // 2. Récupérer les pages générées et trier naturellement
            $pages = glob($tempDir . '/page-*.jpg');
            if (empty($pages)) {
                throw new \Exception("Aucune page générée depuis le PDF");
            }
            natsort($pages);

            Log::info("PDF explosé en " . count($pages) . " pages");

            // 3. WATERMARK : Appliquer le filigrane sur chaque page (via Intervention Image)
            $watermarkedPages = [];
            foreach ($pages as $index => $pagePath) {

                // TRAITEMENT GÉOMÉTRIQUE SI CARTE D'IDENTITÉ
                // Appliquer la "Golden Geometry" (950px sur A4) même pour les pages extraites du PDF
                if ($this->isCardDocument($doc)) {
                    $pagePath = $this->applyCardGeometry($pagePath, $doc);
                }

                $watermarkedPath = $this->addWatermarkAndFooter($pagePath);
                $watermarkedPages[] = $watermarkedPath;

                // Nettoyer la page originale (si différente du watermarked)
                if (file_exists($pagePath) && $pagePath !== $watermarkedPath) {
                    unlink($pagePath);
                }
            }

            // 4. REBUILD : Réassembler en PDF
            $finalPdf = $this->imagesToPdf($watermarkedPages);

            // Nettoyage du dossier temp
            if (is_dir($tempDir)) {
                // Itérer et supprimer les fichiers avant de supprimer le dossier
                foreach (glob($tempDir . '/*') as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
                rmdir($tempDir);
            }

            return $finalPdf;
        } catch (\Exception $e) {
            Log::error("Erreur sanitization PDF (CLI) {$doc->id}: " . $e->getMessage());
            // En cas d'échec, on renvoie le PDF original
            return $pdfPath;
        }
    }

    private function sanitizeImage(string $imagePath, Document $doc): string
    {
        // 1. TRAITEMENT GÉOMÉTRIQUE
        if ($this->isCardDocument($doc)) {
            // Mode "GOLDEN GEOMETRY" : 950px centré sur A4
            $processedPath = $this->applyCardGeometry($imagePath, $doc);
        } else {
            // Mode "PLEINE PAGE" : Max 1000px
            $processedPath = $this->resizeFullPageDocument($imagePath, $doc);
        }

        // 2. WATERMARK
        $watermarkedPath = $this->addWatermarkAndFooter($processedPath);

        // Nettoyage intermédiaire
        if ($processedPath !== $imagePath && file_exists($processedPath)) {
            unlink($processedPath);
        }

        // 3. CONVERSION PDF
        return $this->imagesToPdf([$watermarkedPath]);
    }

    /**
     * Applique la "Golden Geometry" pour les cartes d'identité
     * - Trim (rogner les marges blanches)
     * - Resize à 950px de large (fixe)
     * - Centrer sur page A4 (1240x1754)
     */
    private function applyCardGeometry(string $imagePath, Document $doc): string
    {
        $a4Width = 1240;  // 210mm à 150 DPI
        $a4Height = 1754; // 297mm à 150 DPI
        $targetWidth = 950; // Largeur cible "Golden Geometry"

        $outputPath = storage_path("app/temp/geometry_{$doc->id}_" . uniqid() . ".jpg");

        Log::info("[applyCardGeometry] Starting for doc {$doc->id}: {$imagePath}");

        // 1. Analyser les dimensions de l'image ORIENTÉE
        $sizeCmd = "magick " . escapeshellarg($imagePath) . " -auto-orient -format '%w %h' info:";
        $sizeOutput = trim(shell_exec($sizeCmd));
        list($width, $height) = explode(' ', $sizeOutput);

        $isLandscape = $width > $height;
        Log::info("[applyCardGeometry] Oriented Dimensions: {$width}x{$height} (" . ($isLandscape ? 'Landscape' : 'Portrait') . ")");

        $cropOption = "";

        if ($isLandscape) {
            // =================================================================
            // STRATÉGIE 1 : PAYSAGE (PHOTOS CLOSE-UP)
            // =================================================================
            // Pour les photos de près, le "Connected Components" est trop agressif (risque de ne garder qu'une ligne).
            // On utilise un "Gentle Trim" classique qui marche très bien sur les photos.

            Log::info("[applyCardGeometry] Mode: Landscape (Close-up) -> Gentle Trim (10%)");

            $cmdDetect = sprintf(
                "magick %s -auto-orient -fuzz 10%% -trim -format '%%wx%%h%%X%%Y' info:",
                escapeshellarg($imagePath)
            );

            $cropGeometry = trim(shell_exec($cmdDetect));
            Log::info("[applyCardGeometry] Detected crop geometry (Trim): '{$cropGeometry}'");

            if (!empty($cropGeometry) && $cropGeometry != '0x0+0+0') {
                $cropOption = "-crop " . escapeshellarg($cropGeometry) . " +repage";
            }
        } else {
            // =================================================================
            // STRATÉGIE 2 : PORTRAIT (SCANS A4)
            // =================================================================
            // Pour les scans A4 bruyants, le Trim échoue. On utilise "Connected Components".

            Log::info("[applyCardGeometry] Mode: Portrait (Scan) -> Connected Components");

            // OPTIMISATION CPU : On travaille sur une version réduite pour la détection
            $detectWidth = 800;
            $scaleFactor = $width > $detectWidth ? $width / $detectWidth : 1;

            // AJOUT : -morphology Close Rectangle:20x20
            // "Close" = Dilate puis Erode.
            // Cela permet de boucher les trous (entre les lignes de texte) pour faire un bloc solide
            // SANS trop agrandir la forme globale (contrairement au Dilate seul).
            // Rectangle:20x20 est optimisé pour fusionner les lignes de texte horizontales.
            $cmdDetect = sprintf(
                "magick %s -auto-orient -resize %dx -colorspace gray -threshold 85%% -type bilevel " .
                    "-morphology Close Rectangle:20x20 " .
                    "-define connected-components:verbose=true " .
                    "-define connected-components:area-threshold=1000 " .
                    "-connected-components 4 /dev/null",
                escapeshellarg($imagePath),
                $detectWidth
            );

            exec($cmdDetect . " 2>&1", $outputDetect);

            $bestCrop = null;
            $maxArea = 0;
            $fullPageArea = ($width / $scaleFactor) * ($height / $scaleFactor);

            foreach ($outputDetect as $line) {
                // Parsing de la ligne (srgb ou gray)
                $w = $h = $x = $y = $r = 0;
                $matched = false;

                if (preg_match('/^\s*\d+:\s*(\d+)x(\d+)\+(\d+)\+(\d+).*?srgb\((\d+),(\d+),(\d+)\)/', $line, $matches)) {
                    $w = (int)$matches[1];
                    $h = (int)$matches[2];
                    $x = (int)$matches[3];
                    $y = (int)$matches[4];
                    $r = (int)$matches[5];
                    $matched = true;
                } elseif (preg_match('/^\s*\d+:\s*(\d+)x(\d+)\+(\d+)\+(\d+).*?gray\((\d+)\)/', $line, $matches)) {
                    $w = (int)$matches[1];
                    $h = (int)$matches[2];
                    $x = (int)$matches[3];
                    $y = (int)$matches[4];
                    $r = (int)$matches[5];
                    $matched = true;
                }

                if ($matched) {
                    $area = $w * $h;
                    $isDark = $r < 128;

                    // Critère 1 : Ignorer si touche les bords (fond scanner)
                    $touchesTop = $y <= 1;
                    $touchesLeft = $x <= 1;
                    $touchesBottom = ($y + $h) >= ($height / $scaleFactor) - 1;
                    $touchesRight = ($x + $w) >= ($width / $scaleFactor) - 1;

                    if ($touchesTop || $touchesLeft || $touchesBottom || $touchesRight) continue;

                    // Critère 2 : Si c'est > 70% de la page (ET ne touche pas les bords), c'est un document pleine page
                    if ($area > $fullPageArea * 0.70) {
                        Log::info("[applyCardGeometry] Detected large content (>70%). Treating as full page (No Crop).");
                        $bestCrop = null; // On annule tout crop
                        break; // On sort, on garde l'image entière
                    }

                    if ($isDark && $area > $maxArea) {
                        $maxArea = $area;
                        $bestCrop = ['w' => $w, 'h' => $h, 'x' => $x, 'y' => $y];
                    }
                }
            }

            if ($bestCrop) {
                $finalW = round($bestCrop['w'] * $scaleFactor);
                $finalH = round($bestCrop['h'] * $scaleFactor);
                $finalX = round($bestCrop['x'] * $scaleFactor);
                $finalY = round($bestCrop['y'] * $scaleFactor);

                // AJOUT : Padding de sécurité pour inclure les bords de la carte
                // On passe à 10% pour être sûr à 100% de tout avoir.
                $padding = round($width * 0.10);

                $finalX = max(0, $finalX - $padding);
                $finalY = max(0, $finalY - $padding);
                $finalW = min($width - $finalX, $finalW + ($padding * 2));
                $finalH = min($height - $finalY, $finalH + ($padding * 2));

                Log::info("[applyCardGeometry] Found Card Blob: {$finalW}x{$finalH}+{$finalX}+{$finalY} (with padding {$padding}px)");
                $cropOption = "-crop {$finalW}x{$finalH}+{$finalX}+{$finalY} +repage";
            } else {
                Log::warning("[applyCardGeometry] No distinct card blob found. Using full image.");
            }
        }

        // APPLICATION FINALE
        $cmdProcess = sprintf(
            "magick %s -auto-orient %s -resize %dx -gravity center -background white -extent %dx%d %s 2>&1",
            escapeshellarg($imagePath),
            $cropOption,
            $targetWidth,
            $a4Width,
            $a4Height,
            escapeshellarg($outputPath)
        );

        Log::info("[applyCardGeometry] Process command: " . $cmdProcess);

        exec($cmdProcess, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($outputPath)) {
            Log::error("Erreur geometry carte: " . implode("\n", $output));
            return $imagePath; // Fallback sur l'original
        }

        Log::info("[applyCardGeometry] Success for doc {$doc->id}: {$outputPath}");

        return $outputPath;
    }

    /**
     * Redimensionne les documents pleine page (contrats, avis...)
     * Ajoute une marge et centre sur A4 (1240x1754)
     */
    private function resizeFullPageDocument(string $imagePath, Document $doc): string
    {
        $outputPath = storage_path("app/temp/resized_{$doc->id}_" . uniqid() . ".jpg");

        // A4 = 1240x1754
        // On veut une marge tout autour.
        // On redimensionne l'image pour qu'elle rentre dans 1100x1650 (marge d'environ 70px / 4-5%)
        // -resize 1100x1650> : Redimensionne seulement si plus grand (ou on peut forcer avec juste 1100x1650)
        // -gravity center -extent 1240x1754 : Pose sur un fond A4 blanc centré

        $cmd = sprintf(
            "magick %s -auto-orient -resize 1100x1650 -gravity center -background white -extent 1240x1754 %s 2>&1",
            escapeshellarg($imagePath),
            escapeshellarg($outputPath)
        );

        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0) {
            Log::error("Erreur resizeFullPageDocument: " . implode("\n", $output));
            return $imagePath; // Fallback original
        }

        return $outputPath;
    }

    /**
     * Vérifie si c'est un document de type carte
     */
    private function isCardDocument(Document $doc): bool
    {
        if (!$doc->relationLoaded('typeDocumentPays')) {
            $doc->load('typeDocumentPays');
        }

        $typeCode = strtolower($doc->typeDocumentPays->code ?? '');
        $isCard = str_contains($typeCode, 'identite');

        Log::info("[isCardDocument] Doc {$doc->id}: type code = '{$typeCode}', isCard = " . ($isCard ? 'TRUE' : 'FALSE'));

        return $isCard;
    }

    private function addWatermarkAndFooter(string $imagePath): string
    {
        $manager = new ImageManager(new Driver());
        $img = $manager->read($imagePath);
        $width = $img->width();
        $height = $img->height();

        // 1. FILIGRANE IMAGE STATIQUE (Fournie par l'utilisateur)
        $watermarkPath = storage_path('app/watermark_master.png');

        if (file_exists($watermarkPath)) {
            $watermark = $manager->read($watermarkPath);

            // Redimensionner le filigrane pour qu'il prenne 80% de la largeur de l'image cible
            $targetWidth = $width * 0.8;
            $watermark->scale(width: $targetWidth);

            // Placer au centre avec 12% d'opacité
            $img->place($watermark, 'center', 0, 0, 12);
        } else {
            Log::error("Watermark master file not found at: $watermarkPath");
            // Fallback texte discret au cas où
            $img->text("POUR LOCATION UNIQUEMENT", $width / 2, $height / 2, function ($font) use ($width) {
                $font->size($width / 10);
                $font->color('rgba(128, 128, 128, 0.3)');
                $font->align('center');
                $font->valign('middle');
                $font->angle(45);
            });
        }

        // 2. FOOTER TEXTE (Désactivé - seulement dans le sommaire maintenant)
        // $footerText = "Dossier réalisé sur dossierappart.fr";
        // $footerFontSize = max(16, $width / 40);
        // $img->text($footerText, $width / 2, $height - ($footerFontSize * 2), function ($font) use ($footerFontSize) {
        //     $font->size($footerFontSize);
        //     $font->color('333333');
        //     $font->align('center');
        //     $font->valign('bottom');
        // });

        $outputPath = storage_path("app/temp/watermarked_" . basename($imagePath));
        $img->save($outputPath, quality: self::SETTINGS['jpeg_quality']);
        return $outputPath;
    }



    private function imagesToPdf(array $imagePaths): string
    {
        $pdf = new Fpdi();

        foreach ($imagePaths as $imagePath) {
            $size = getimagesize($imagePath);
            $width = $size[0];
            $height = $size[1];

            // Convertir pixels en mm (approx 72 DPI pour FPDF par défaut, mais on ajuste)
            // FPDF travaille en mm. A4 = 210x297mm.
            // On va adapter l'image à la page A4

            $orientation = $width > $height ? 'L' : 'P';
            $pageWidth = $orientation === 'L' ? 297 : 210;
            $pageHeight = $orientation === 'L' ? 210 : 297;

            $pdf->AddPage($orientation, [$pageWidth, $pageHeight]);

            // Placer l'image pleine page
            $pdf->Image($imagePath, 0, 0, $pageWidth, $pageHeight);

            // AJOUTER LE LIEN CLIQUABLE SUR LE FOOTER
            // Zone en bas de page, centrée
            $linkX = $pageWidth / 2 - 40; // Approx centré
            $linkY = $pageHeight - 15;    // En bas
            $linkW = 80;
            $linkH = 10;

            $pdf->Link($linkX, $linkY, $linkW, $linkH, "https://dossierappart.fr");

            // Nettoyer immédiatement
            if (file_exists($imagePath)) unlink($imagePath);
        }

        $outputPath = storage_path("app/temp/sanitized_" . uniqid() . ".pdf");
        $pdf->Output('F', $outputPath);

        return $outputPath;
    }
}
