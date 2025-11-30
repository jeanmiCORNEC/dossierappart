<?php

namespace App\Http\Controllers;

use App\Enums\DossierStatus;
use App\Models\Document;
use App\Models\Dossier;
use App\Models\Pays;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

    /**
     * Uploader un document pour un dossier
     */
    public function uploadDocument(Request $request, Dossier $dossier): JsonResponse
    {
        // Validation
        $validated = $request->validate([
            'type_document_pays_id' => ['required', 'exists:types_documents_pays,id'],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'], // 10MB max
        ]);

        // Stockage du fichier
        $file = $request->file('file');
        $filename = $file->getClientOriginalName();

        // Chemin: dossiers/{uuid}/nom-du-fichier
        $path = $file->store("dossiers/{$dossier->id}", 'local');

        // Créer l'enregistrement en base
        $document = Document::create([
            'dossier_id' => $dossier->id,
            'type_document_pays_id' => $validated['type_document_pays_id'],
            'original_filename' => $filename,
            'storage_path' => $path,
            'sort_order' => $dossier->documents()->count() + 1, // Incrémenter l'ordre
        ]);

        return response()->json([
            'message' => 'Document uploadé avec succès',
            'document' => $document,
        ], 201);
    }
}
