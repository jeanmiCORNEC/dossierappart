<?php

namespace App\Jobs;

use App\Enums\DossierStatus;
use App\Mail\DossierCompleted;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
     * Très important pour Ghostscript/ImageMagick
     */
    public function middleware()
    {
        return [new RateLimited('pdf-processing')];
    }

    public function handle(
        SommaireGeneratorService $sommaireGenerator,
        PdfSanitizerService $pdfSanitizer,
        PdfMergerService $pdfMerger
    ): void {
        Log::info("🏁 JOB START : Traitement dossier {$this->dossier->id}");

        try {
            // 1. Marquer comme en cours
            $this->dossier->update(['status' => DossierStatus::PROCESSING]);

            // 2. Calculer les numéros de page pour le sommaire
            // (Basé sur l'ordre des documents en BDD)
            $pageNumbers = $this->calculatePageNumbers();

            // 3. Générer le sommaire (Page de garde)
            $sommairePath = $sommaireGenerator->generate($this->dossier, $pageNumbers);

            // 4. Nettoyage & Filigrane des documents
            $sanitizedDocs = [];
            
            // On récupère les documents dans le bon ordre
            foreach ($this->dossier->documents()->orderBy('sort_order')->get() as $doc) {
                // Traitement lourd (ImageMagick)
                $sanitizedDocs[] = $pdfSanitizer->sanitizeDocument($doc);

                // Petite pause pour laisser le CPU respirer entre deux gros fichiers
                usleep(100000); // 100ms
            }

            // 5. Fusion Finale (Sommaire + Docs sécurisés)
            $finalPdfPath = $pdfMerger->merge($sommairePath, $sanitizedDocs);

            // 6. Enregistrement en BDD
            $this->dossier->update([
                'status' => DossierStatus::COMPLETED,
                'final_pdf_path' => $finalPdfPath,
                'processed_at' => now(), // Utile pour les stats
            ]);

            // 7. ENVOI DE L'EMAIL (La nouveauté)
            if ($this->dossier->email) {
                Mail::to($this->dossier->email)->send(new DossierCompleted($this->dossier));
                Log::info("📧 EMAIL SENT : Dossier {$this->dossier->id} envoyé à {$this->dossier->email}");
                // --- LOG JURIDIQUE : PREUVE DE LIVRAISON ---
                $this->dossier->logs()->create([
                    'action_type' => 'email_sent',
                    'details' => "Lien de téléchargement envoyé à : " . $this->dossier->email
                ]);
            } else {
                Log::warning("⚠️ EMAIL MISSING : Pas d'email pour le dossier {$this->dossier->id}, impossible d'envoyer.");
            }

        } catch (\Exception $e) {
            Log::error("🔥 JOB FAILED Dossier {$this->dossier->id}: " . $e->getMessage());
            Log::error($e->getTraceAsString());

            $this->dossier->update(['status' => DossierStatus::FAILED]);

            // On relance l'exception pour que le job soit marqué comme failed dans Horizon/Queue
            throw $e;
        }
    }

    /**
     * Calcule une estimation des numéros de page pour le sommaire
     */
    private function calculatePageNumbers(): array
    {
        // Page 1 = Sommaire (on assume qu'il fait 1 page pour l'instant)
        $currentPage = 2;
        $pageNumbers = [];

        // Grouper par type pour l'affichage dans le sommaire
        $documentsByType = $this->dossier->documents()
            ->orderBy('sort_order')
            ->get()
            ->groupBy('type_document_pays_id');

        foreach ($documentsByType as $typeId => $docs) {
            // Le chapitre commence à la page courante
            $pageNumbers[$typeId] = $currentPage;
            
            // On incrémente le compteur.
            // Note : Si un PDF contient plusieurs pages, ce compteur sera approximatif
            // Pour être exact, il faudrait compter les pages réelles des PDF uploadés,
            // mais cela demande de les ouvrir avant le traitement. Pour le MVP, c'est acceptable.
            $currentPage += $docs->count();
        }

        return $pageNumbers;
    }
}