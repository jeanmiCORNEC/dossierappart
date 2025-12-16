<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Process;

class PdfSanitizerService
{
    // A4 @ 200 DPI = 1654 x 2339 pixels
    private int $a4Width = 1654;
    private int $a4Height = 2339;

    protected string $masterWatermarkPath;
    protected string $optimizedWatermarkPath;
    protected string $tempPath;

    // Commande ImageMagick v7
    protected string $binary = 'magick';

    public function __construct()
    {
        // Source Master (2000px)
        $this->masterWatermarkPath = storage_path('app/watermark_master.png');

        // Cache Optimisé pour le tuilage (sera généré automatiquement)
        $this->optimizedWatermarkPath = storage_path('app/watermark_opt_600.png');

        $this->tempPath = storage_path('app/temp');

        if (!file_exists($this->tempPath)) {
            mkdir($this->tempPath, 0755, true);
        }

        // PRÉPARATION INTELLIGENTE DU WATERMARK
        // On le redimensionne une seule fois pour économiser le CPU sur chaque job
        $this->prepareOptimizedWatermark();
    }

    /**
     * Crée une version réduite du watermark pour qu'il se répète bien sur la page
     */
    protected function prepareOptimizedWatermark(): void
    {
        if (file_exists($this->masterWatermarkPath) && !file_exists($this->optimizedWatermarkPath)) {
            try {
                // On réduit le master à 600px de large (pour avoir ~2.5 répétitions en largeur sur A4)
                $cmd = [
                    $this->binary,
                    $this->masterWatermarkPath,
                    '-resize',
                    '600',
                    $this->optimizedWatermarkPath
                ];

                Process::run($cmd);
                Log::info("Watermark optimisé généré : {$this->optimizedWatermarkPath}");
            } catch (\Exception $e) {
                Log::error("Impossible de générer le watermark optimisé: " . $e->getMessage());
            }
        }
    }

    public function sanitizeDocument(Document $doc): string
    {
        $inputPath = Storage::disk('local')->path($doc->storage_path);
        $outputPath = $this->tempPath . "/sanitized_{$doc->id}_" . uniqid() . ".pdf";

        if (!$doc->relationLoaded('typeDocumentPays')) {
            $doc->load('typeDocumentPays');
        }
        $typeCode = strtolower($doc->typeDocumentPays->code ?? 'autre');
        $extension = strtolower(pathinfo($inputPath, PATHINFO_EXTENSION));

        Log::info("START Sanitizing Doc ID: {$doc->id} Type: {$typeCode}");

        if ($extension === 'pdf') {
            $this->processPdf($inputPath, $outputPath, $typeCode);
        } else {
            $this->processImage($inputPath, $outputPath, $typeCode);
        }

        return $outputPath;
    }

    protected function processImage(string $input, string $output, string $type, bool $allowUpscale = true): void
    {
        $isIdentity = str_contains($type, 'identite');
        $tempA4 = $this->tempPath . '/norm_' . uniqid() . '.jpg';

        try {
            // --- ÉTAPE 1 : NORMALISATION ---
            // (Identique à ton code actuel)
            $cmd = "{$this->binary} " . escapeshellarg($input) . " -auto-orient ";

            if ($isIdentity) {
                $cmd .= "-fuzz 10% -trim +repage -resize 1200 ";
            } else {
                $w = $this->a4Width - 50;
                $h = $this->a4Height - 50;
                $resizeFlag = $allowUpscale ? '' : '>';
                $cmd .= "-resize " . escapeshellarg("{$w}x{$h}{$resizeFlag}") . " ";
            }

            $cmd .= "-gravity center -background white ";
            $cmd .= "-extent {$this->a4Width}x{$this->a4Height} ";
            $cmd .= "-quality 90 -density 200 ";
            $cmd .= escapeshellarg($tempA4);

            $this->executeCommand($cmd);

            // --- ÉTAPE 2 : WATERMARKING (CORRIGÉ POUR SERVEUR) ---
            
            $watermarkToUse = file_exists($this->optimizedWatermarkPath)
                ? $this->optimizedWatermarkPath
                : $this->masterWatermarkPath;

            if (file_exists($watermarkToUse)) {
                // ⚠️ CORRECTION MAJEURE ICI :
                // On utilise 'convert' (via l'alias magick) au lieu de 'composite'
                // Syntaxe : convert BASE WATERMARK -compose dissolve -define compose:args=12 -composite OUTPUT
                
                // Pour le tuilage (-tile), c'est plus complexe en v6 pure.
                // On va utiliser une approche plus robuste : on crée une image tuilée de la taille de la page d'abord.
                
                $tiledWatermark = $this->tempPath . '/tiled_' . uniqid() . '.png';
                
                // 1. Créer le calque de filigrane tuilé (taille A4)
                // "convert -size WxH tile:watermark.png tiled.png"
                $cmdTile = "{$this->binary} -size {$this->a4Width}x{$this->a4Height} tile:" . 
                           escapeshellarg($watermarkToUse) . " " . 
                           escapeshellarg($tiledWatermark);
                $this->executeCommand($cmdTile);

                // 2. Fusionner (Composition)
                $cmdMerge = "{$this->binary} " . 
                            escapeshellarg($tempA4) . " " . 
                            escapeshellarg($tiledWatermark) . " " . 
                            "-compose dissolve -define compose:args=12 -composite " . 
                            escapeshellarg($output);
                
                $this->executeCommand($cmdMerge);
                
                // Nettoyage du calque temporaire
                if (file_exists($tiledWatermark)) unlink($tiledWatermark);

            } else {
                Log::warning("Aucun watermark trouvé (Master ou Opt).");
                // Copie simple si pas de watermark
                copy($tempA4, $output);
            }
        } finally {
            if (file_exists($tempA4)) unlink($tempA4);
        }
    }

    protected function processPdf(string $input, string $output, string $type): void
    {
        $jobDir = $this->tempPath . '/proc_' . uniqid();
        if (!is_dir($jobDir)) mkdir($jobDir, 0755, true);

        try {
            // 1. Explosion PDF -> JPG
            $gsCmd = "gs -dNOPAUSE -dBATCH -sDEVICE=jpeg -r200 -sOutputFile=" .
                escapeshellarg($jobDir . '/page-%03d.jpg') . " " .
                escapeshellarg($input);

            $this->executeCommand($gsCmd);

            // 2. Traitement Pages
            $files = glob($jobDir . '/*.jpg');
            sort($files);
            $processedPdfs = [];

            if (empty($files)) {
                // Fallback : essai traitement direct
                $this->processImage($input, $output, $type);
                return;
            }

            foreach ($files as $pagePath) {
                $pageOutputPdf = str_replace('.jpg', '_w.pdf', $pagePath);
                // Pour les PDFs, on respecte la taille physique (pas d'upscale)
                $this->processImage($pagePath, $pageOutputPdf, $type, false);
                $processedPdfs[] = $pageOutputPdf;
            }

            // 3. Fusion (PDF 1.4 pour compatibilité)
            $mergeList = implode(' ', array_map('escapeshellarg', $processedPdfs));
            $mergeCmd = "gs -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -sOutputFile=" .
                escapeshellarg($output) . " " . $mergeList;

            $this->executeCommand($mergeCmd);
        } finally {
            if (is_dir($jobDir)) {
                array_map('unlink', glob("$jobDir/*"));
                rmdir($jobDir);
            }
        }
    }

    protected function executeCommand(string $command): void
    {
        $result = Process::timeout(300)->run($command);

        if ($result->failed()) {
            Log::error("CMD FAILED: " . $result->errorOutput());
            throw new \Exception("Command failed: " . $result->errorOutput());
        }
    }
}
