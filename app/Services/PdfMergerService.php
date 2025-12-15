<?php

namespace App\Services;

use setasign\Fpdi\Fpdi;
use Illuminate\Support\FacadesLog;
use Illuminate\Support\Facades\Log;

class PdfMergerService
{
    public function merge(string $sommairePath, array $documentPaths): string
    {
        $pdf = new Fpdi();

        // 1. Calculer le mapping : type_document_pays_id => numéro de page de début
        $pageMap = $this->calculatePageMap($sommairePath, $documentPaths);

        // 2. Liste de tous les PDFs à fusionner
        $allPdfs = array_merge([$sommairePath], $documentPaths);
        $tempFilesToDelete = [];

        Log::info("[PdfMerger] Starting merge with " . count($allPdfs) . " PDFs");
        Log::info("[PdfMerger] Sommaire: {$sommairePath}");
        foreach ($documentPaths as $i => $path) {
            Log::info("[PdfMerger] Doc {$i}: {$path} exists=" . (file_exists($path) ? 'YES' : 'NO'));
        }

        foreach ($allPdfs as $index => $pdfPath) {
            if (!file_exists($pdfPath)) {
                Log::warning("[PdfMerger] File not found: {$pdfPath}");
                continue;
            }

            Log::info("[PdfMerger] Processing PDF #{$index}: {$pdfPath}");

            try {
                $pageCount = $pdf->setSourceFile($pdfPath);
                Log::info("[PdfMerger] PDF #{$index} has {$pageCount} pages");

                for ($i = 1; $i <= $pageCount; $i++) {
                    $tplId = $pdf->importPage($i);
                    $size = $pdf->getTemplateSize($tplId);

                    // Standardiser en A4 tout en gardant l'orientation
                    $orientation = $size['width'] > $size['height'] ? 'L' : 'P';

                    $pdf->AddPage($orientation, 'A4');

                    // Dimensions A4 en mm (FPDF par défaut)
                    $fullWidth = 210;
                    $fullHeight = 297;

                    if ($orientation === 'L') {
                        $pWidth = $fullHeight; // 297
                        $pHeight = $fullWidth; // 210
                    } else {
                        $pWidth = $fullWidth;
                        $pHeight = $fullHeight;
                    }

                    // On force le contenu à remplir la page A4 (image ou PDF source)
                    $pdf->useTemplate($tplId, 0, 0, $pWidth, $pHeight);
                }

                Log::info("[PdfMerger] Successfully merged PDF #{$index}");
            } catch (\Exception $e) {
                Log::error("Erreur fusion PDF {$pdfPath}: " . $e->getMessage());
                Log::error("Stack trace: " . $e->getTraceAsString());
                // On continue avec les autres fichiers
            }

            // Collecter les fichiers temporaires à nettoyer plus tard
            if (strpos($pdfPath, '/temp/') !== false) {
                $tempFilesToDelete[] = $pdfPath;
            }
        }

        // Créer le dossier final si besoin
        $outputDir = storage_path('app/dossiers');
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $outputPath = $outputDir . '/final_' . uniqid() . '.pdf';

        try {
            $pdf->Output('F', $outputPath);
            Log::info("[PdfMerger] Final PDF generated at: {$outputPath}");
        } catch (\Exception $e) {
            Log::error("[PdfMerger] Error generating final PDF: " . $e->getMessage());
            throw $e;
        }

        // Nettoyer les fichiers temporaires APRÈS la génération réussie
        foreach ($tempFilesToDelete as $tempFile) {
            if (file_exists($tempFile)) {
                @unlink($tempFile);
                Log::info("[PdfMerger] Cleaned temp file: {$tempFile}");
            }
        }

        return $outputPath;
    }

    /**
     * Calcule le numéro de page de début pour chaque document
     */
    private function calculatePageMap(string $sommairePath, array $documentPaths): array
    {
        $pageMap = [];
        $currentPage = 1;

        // Le sommaire prend 1 page (on suppose)
        $summaryPageCount = $this->countPages($sommairePath);
        $currentPage += $summaryPageCount;

        // Pour chaque document
        foreach ($documentPaths as $index => $docPath) {
            if (!file_exists($docPath)) continue;

            $pageCount = $this->countPages($docPath);
            // On pourrait stocker type_document_pays_id ici si on l'avait
            // Pour l'instant on utilise l'index
            $pageMap[$index] = $currentPage;
            $currentPage += $pageCount;
        }

        return $pageMap;
    }

    /**
     * Compte les pages d'un PDF
     */
    private function countPages(string $pdfPath): int
    {
        try {
            $pdf = new Fpdi();
            return $pdf->setSourceFile($pdfPath);
        } catch (\Exception $e) {
            return 0;
        }
    }
}
