<?php

namespace App\Jobs;

use App\Enums\DossierStatus;
use App\Models\Dossier;
use App\Services\PdfMergerService;
use App\Services\PdfSanitizerService;
use App\Services\SommaireGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ProcessDossierJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 600; // 10 minutes max pour les gros dossiers

    public function __construct(
        public Dossier $dossier
    ) {}

    /**
     * Limiter le nombre de jobs simultanés pour protéger le CPU
     */
    public function middleware()
    {
        // Autorise 2 jobs par minute (ajustable selon serveur)
        return [new RateLimited('pdf-processing')];
    }

    public function handle(
        SommaireGeneratorService $sommaireGenerator,
        PdfSanitizerService $pdfSanitizer,
        PdfMergerService $pdfMerger
    ): void {
        try {
            // 1. Marquer comme en cours (si pas déjà fait)
            $this->dossier->update(['status' => DossierStatus::PROCESSING]);

            // 2. Calculer les numéros de page pour chaque type de document
            $pageNumbers = $this->calculatePageNumbers();

            // 3. Générer le sommaire avec les numéros de page
            $sommairePath = $sommaireGenerator->generate($this->dossier, $pageNumbers);

            // 4. Sanitizer chaque document
            $sanitizedDocs = [];

            // On traite les documents un par un pour économiser la RAM
            foreach ($this->dossier->documents as $doc) {
                $sanitizedDocs[] = $pdfSanitizer->sanitizeDocument($doc);

                // Petite pause pour laisser le CPU respirer
                usleep(100000); // 100ms
            }

            // 5. Fusionner tout
            $finalPdfPath = $pdfMerger->merge($sommairePath, $sanitizedDocs);

            // 6. Sauvegarder et mettre à jour le dossier
            $this->dossier->update([
                'status' => DossierStatus::COMPLETED,
                'final_pdf_path' => $finalPdfPath,
                'processed_at' => now(),
            ]);

            // TODO: Envoyer email de notification

        } catch (\Exception $e) {
            \Log::error("Erreur traitement dossier {$this->dossier->id}: " . $e->getMessage());
            \Log::error($e->getTraceAsString());

            $this->dossier->update(['status' => DossierStatus::FAILED]);

            // On relance l'exception pour que le job soit marqué comme failed dans Horizon/Queue
            throw $e;
        }
    }

    /**
     * Calcule le numéro de page pour chaque type de document
     */
    private function calculatePageNumbers(): array
    {
        // Page 1 = sommaire (on suppose 1 page)
        $currentPage = 2;
        $pageNumbers = [];

        // Grouper par type
        $documentsByType = $this->dossier->documents()
            ->orderBy('sort_order')
            ->get()
            ->groupBy('type_document_pays_id');

        foreach ($documentsByType as $typeId => $docs) {
            $pageNumbers[$typeId] = $currentPage;
            // Chaque document = 1 page minimum (approximation)
            $currentPage += $docs->count();
        }

        return $pageNumbers;
    }
}
