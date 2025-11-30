<?php

namespace App\Http\Controllers;

use App\Enums\DossierStatus;
use App\Models\Dossier;
use App\Models\Pays;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DossierController extends Controller
{
    /**
     * Créer un nouveau dossier (brouillon)
     */
    public function store(Request $request): RedirectResponse
    {
        // Validation basique
        $validated = $request->validate([
            'pays_id' => ['required', 'exists:pays,id'],
        ]);

        // Création du dossier
        $dossier = Dossier::create([
            'pays_id' => $validated['pays_id'],
            'status' => DossierStatus::DRAFT,
            // download_token est généré automatiquement par le boot() du modèle
        ]);

        // Redirection vers la page d'upload
        return redirect()->route('dossiers.upload', ['dossier' => $dossier->id]);
    }

    /**
     * Afficher la page d'upload
     */
    public function upload(Dossier $dossier)
    {
        return "Page d'upload pour le dossier " . $dossier->id;
    }
}
