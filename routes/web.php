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
