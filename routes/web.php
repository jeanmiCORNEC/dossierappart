<?php

use App\Models\Pays;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DossierController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'pays' => Pays::orderBy('nom')->get(),
    ]);
});

// Routes publiques pour le flow DossierAppart
Route::post('/dossiers', [DossierController::class, 'store'])->name('dossiers.store');
Route::get('/dossiers/{dossier}/upload', [DossierController::class, 'upload'])->name('dossiers.upload');
Route::post('/dossiers/{dossier}/documents', [DossierController::class, 'uploadDocument'])->name('dossiers.uploadDocument');
Route::get('/dossiers/{dossier}/documents/{document}', [DossierController::class, 'viewDocument'])->name('dossiers.viewDocument');
Route::delete('/dossiers/{dossier}/documents/{document}', [DossierController::class, 'deleteDocument'])->name('dossiers.deleteDocument');
Route::post('/dossiers/{dossier}/submit', [DossierController::class, 'submit'])->name('dossiers.submit');
Route::get('/dossiers/{dossier}/status', [DossierController::class, 'status'])->name('dossiers.status');
Route::get('/dossiers/{dossier}/download', [DossierController::class, 'download'])->name('dossiers.download');
