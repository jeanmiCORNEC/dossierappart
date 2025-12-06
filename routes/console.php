<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Nettoyage des dossiers (RGPD & Espace disque)
// Toutes les heures, on vérifie ce qui a expiré
Schedule::command('dossier:clean')->hourly();

// Nettoyage système (Temp files & Logs)
// Une fois par jour suffit largement (ex: à 04h00 du matin)
// Pas besoin de le faire toutes les heures, ça économise des I/O disque
Schedule::command('system:clean')->dailyAt('04:00');

// Optionnel : Nettoyage des tokens sanctum expirés, etc. si tu en as
// Schedule::command('sanctum:prune-expired')->daily();