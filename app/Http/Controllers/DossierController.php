<?php

namespace App\Http\Controllers;

use App\Enums\DossierStatus;
use App\Models\Document;
use App\Models\Dossier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DossierController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(['pays_id' => ['required', 'exists:pays,id']]);

        $dossier = Dossier::create([
            'pays_id' => $validated['pays_id'],
            'status' => DossierStatus::DRAFT,
        ]);

        return redirect()->route('dossiers.upload', ['dossier' => $dossier->id]);
    }

    public function upload(Dossier $dossier)
    {
        $dossier->load('pays');
        return Inertia::render('Upload', [
            'dossier' => $dossier,
            'documents' => $dossier->documents,
            'documentTypes' => $dossier->pays->typesDocumentsPays()->orderBy('ordre')->get(),
        ]);
    }

    public function uploadDocument(Request $request, Dossier $dossier): RedirectResponse
    {
        $validated = $request->validate([
            'type_document_pays_id' => ['required', 'exists:types_documents_pays,id'],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        $path = $file->store("dossiers/{$dossier->id}", 'local');

        Document::create([
            'dossier_id' => $dossier->id,
            'type_document_pays_id' => $validated['type_document_pays_id'],
            'original_filename' => $filename,
            'storage_path' => $path,
            'sort_order' => $dossier->documents()->count() + 1,
        ]);

        return redirect()->back()->with('success', 'Document ajouté.');
    }

    public function deleteDocument(Dossier $dossier, Document $document): RedirectResponse
    {
        if ($document->dossier_id !== $dossier->id) abort(403);

        if (Storage::disk('local')->exists($document->storage_path)) {
            Storage::disk('local')->delete($document->storage_path);
        }
        $document->delete();

        return redirect()->back()->with('success', 'Document supprimé.');
    }
    /**
     * Visualiser un document (Sert pour les miniatures sécurisées)
     */
    public function viewDocument(Dossier $dossier, Document $document)
    {
        // 1. Sécurité : Vérifier que le document appartient bien au dossier demandé
        if ($document->dossier_id !== $dossier->id) {
            abort(403, 'Accès interdit.');
        }

        // 2. Vérifier l'existence physique
        if (!Storage::disk('local')->exists($document->storage_path)) {
            abort(404, 'Fichier introuvable.');
        }

        // 3. Servir le fichier (Inline pour affichage navigateur)
        $path = Storage::disk('local')->path($document->storage_path);

        return response()->file($path, [
            'Content-Disposition' => 'inline; filename="' . $document->original_filename . '"'
        ]);
    }

    public function submit(Dossier $dossier): RedirectResponse
    {
        if ($dossier->documents()->count() === 0) {
            return redirect()->back()->withErrors(['error' => 'Dossier vide.']);
        }

        // Passage en statut PAYÉ
        $dossier->update(['status' => DossierStatus::PAID]);

        // Déclenchement du traitement asynchrone
        \App\Jobs\ProcessDossierJob::dispatch($dossier);

        // Pas de redirection, Inertia gère le onSuccess côté front pour afficher l'écran final
        return redirect()->back()->with('success', 'Validation enregistrée.');
    }
    /**
     * Télécharger le dossier final sécurisé
     */
    public function download(Request $request, Dossier $dossier)
    {
        // DEBUG : On arrête tout et on affiche les infos
        // Si tu ne vois pas cet écran noir, c'est que la route web.php est mauvaise.
        
        // 1. Vérif Token
        if ($request->query('token') !== $dossier->download_token) {
            dd("ERREUR TOKEN : Reçu [{$request->query('token')}] vs Attendu [{$dossier->download_token}]");
        }

        // 2. Vérif Statut
        if ($dossier->status !== DossierStatus::COMPLETED) {
            dd("ERREUR STATUT : Le dossier est [{$dossier->status}] au lieu de COMPLETED");
        }

        // 3. Vérif Chemin BDD
        if (!$dossier->final_pdf_path) {
            dd("ERREUR BDD : Le chemin du PDF final est NULL en base de données.");
        }

        // 4. Vérif Fichier Physique
        if (!file_exists($dossier->final_pdf_path)) {
            dd("ERREUR FICHIER : Le fichier n'existe pas sur le disque.", [
                'Chemin cherché' => $dossier->final_pdf_path,
                'Dossier existe ?' => is_dir(dirname($dossier->final_pdf_path)),
                'Permissions' => substr(sprintf('%o', fileperms(dirname($dossier->final_pdf_path))), -4)
            ]);
        }

        // Si tout est OK, on lance le téléchargement
        return response()->download($dossier->final_pdf_path, 'Mon_Dossier_Location_Securise.pdf');
    }
}
