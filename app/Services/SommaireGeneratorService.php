<?php

namespace App\Services;

use App\Models\Dossier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class SommaireGeneratorService
{
    public function generate(Dossier $dossier, array $pageNumbers = []): string
    {
        // Grouper documents par type
        $documentsByType = $dossier->documents()
            ->with('typeDocumentPays')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('type_document_pays_id');

        // Générer HTML
        $pdf = Pdf::loadView('pdf.sommaire', [
            'dossier' => $dossier,
            'documentsByType' => $documentsByType,
            'generatedAt' => now(),
            'pageNumbers' => $pageNumbers,
        ]);

        // Configuration DomPDF
        $pdf->setPaper('a4', 'portrait');

        // Sauvegarder temporairement
        $filename = "sommaire_{$dossier->id}.pdf";
        $path = storage_path("app/temp/{$filename}");

        // S'assurer que le dossier temp existe
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $pdf->save($path);

        return $path;
    }
}
