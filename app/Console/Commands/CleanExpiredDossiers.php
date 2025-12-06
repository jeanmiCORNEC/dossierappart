<?php

namespace App\Console\Commands;

use App\Models\Dossier;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CleanExpiredDossiers extends Command
{
    protected $signature = 'dossier:clean';
    protected $description = 'Supprime les dossiers expirés (24h après paiement) et les brouillons abandonnés (>48h)';

    public function handle()
    {
        $now = Carbon::now();
        $count = 0;

        // 1. Dossiers expirés (Basé sur la date d'expiration définie au paiement)
        $expiredDossiers = Dossier::whereNotNull('expires_at')
            ->where('expires_at', '<', $now)
            ->get();

        // 2. Brouillons abandonnés (Créés il y a plus de 48h)
        $abandonedDrafts = Dossier::where('status', 'draft')
            ->where('created_at', '<', $now->subHours(48))
            ->get();

        $allToDelete = $expiredDossiers->merge($abandonedDrafts);

        foreach ($allToDelete as $dossier) {
            try {
                // Suppression physique du dossier complet (Raw + Final)
                // Le dossier est stocké dans storage/app/private/dossiers/{uuid}
                if (Storage::disk('local')->exists("dossiers/{$dossier->id}")) {
                    Storage::disk('local')->deleteDirectory("dossiers/{$dossier->id}");
                }

                // Suppression BDD (suppression définitive pour les tests et nettoyage physique)
                // On utilise forceDelete() pour effacer la ligne (le modèle utilise SoftDeletes)
                $dossier->forceDelete();
                $count++;
            } catch (\Exception $e) {
                Log::error("Erreur suppression dossier {$dossier->id} : " . $e->getMessage());
            }
        }

        $this->info("Nettoyage terminé : {$count} dossiers supprimés.");
    }
}