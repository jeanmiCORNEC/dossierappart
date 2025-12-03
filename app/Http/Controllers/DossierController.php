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
use Inertia\Inertia;
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
    /**
     * Afficher la page d'upload
     */
    public function upload(Dossier $dossier)
    {
        $dossier->load('pays');

        return Inertia::render('Upload', [
            'dossier' => $dossier,
            'documents' => $dossier->documents,
            'documentTypes' => $dossier->pays->typesDocumentsPays()->orderBy('ordre')->get(),
        ]);
    }

    /**
     * Uploader un document pour un dossier
     */
    /**
     * Uploader un document pour un dossier
     */
    public function uploadDocument(Request $request, Dossier $dossier): RedirectResponse
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

        return redirect()->back()->with('success', 'Document ajouté avec succès');
    }

    /**
     * Visualiser un document
     */
    public function viewDocument(Dossier $dossier, Document $document)
    {
        // Vérifier que le document appartient bien au dossier
        if ($document->dossier_id !== $dossier->id) {
            abort(403, 'Ce document n\'appartient pas à ce dossier');
        }

        // Vérifier que le fichier existe
        if (!Storage::disk('local')->exists($document->storage_path)) {
            abort(404, 'Fichier non trouvé');
        }

        // Retourner le fichier pour visualisation dans le navigateur
        $path = Storage::disk('local')->path($document->storage_path);
        return response()->file($path, [
            'Content-Disposition' => 'inline; filename="' . $document->original_filename . '"'
        ]);
    }

    /**
     * Supprimer un document
     */
    public function deleteDocument(Dossier $dossier, Document $document): RedirectResponse
    {
        // Vérifier que le document appartient bien au dossier
        if ($document->dossier_id !== $dossier->id) {
            abort(403, 'Ce document n\'appartient pas à ce dossier');
        }

        // Supprimer le fichier du stockage
        if (Storage::disk('local')->exists($document->storage_path)) {
            Storage::disk('local')->delete($document->storage_path);
        }

        // Supprimer l'enregistrement en base
        $document->delete();

        return redirect()->back()->with('success', 'Document supprimé avec succès');
    }

    /**
     * Soumettre le dossier pour traitement
     */
    /**
     * Soumettre le dossier pour traitement
     */
    public function submit(Dossier $dossier): RedirectResponse
    {
        // Vérifier qu'il y a des documents
        if ($dossier->documents()->count() === 0) {
            return redirect()->back()->withErrors(['error' => 'Veuillez ajouter au moins un document.']);
        }

        // Marquer comme payé (simulation pour l'instant)
        // TODO: Intégration Stripe réelle
        $dossier->update(['status' => DossierStatus::PAID]);

        // Lancer le job de traitement en arrière-plan
        \App\Jobs\ProcessDossierJob::dispatch($dossier);

        // Retourner la main immédiatement à l'utilisateur
        return redirect()->route('dossiers.upload', $dossier)
            ->with('success', 'Dossier en cours de création. Vous recevrez un email avec le lien de téléchargement dès qu\'il sera prêt.');
    }

    /**
     * Vérifier le statut du dossier (polling)
     */
    public function status(Dossier $dossier): JsonResponse
    {
        return response()->json([
            'status' => $dossier->status,
            'download_url' => $dossier->status === DossierStatus::COMPLETED
                ? route('dossiers.download', ['dossier' => $dossier->id, 'token' => $dossier->download_token])
                : null
        ]);
    }

    /**
     * Télécharger le dossier final
     */
    public function download(Request $request, Dossier $dossier)
    {
        // Vérifier le token de sécurité
        if ($request->query('token') !== $dossier->download_token) {
            abort(403, 'Lien de téléchargement invalide ou expiré.');
        }

        if ($dossier->status !== DossierStatus::COMPLETED || !$dossier->final_pdf_path) {
            abort(404, 'Le dossier n\'est pas encore prêt.');
        }

        if (!file_exists($dossier->final_pdf_path)) {
            abort(404, 'Fichier introuvable sur le serveur.');
        }

        return response()->download($dossier->final_pdf_path, 'Mon_Dossier_Location.pdf');
    }
}
