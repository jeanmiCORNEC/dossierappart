<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon; // <--- Importation essentielle

class CleanSystemFiles extends Command
{
    protected $signature = 'system:clean';
    protected $description = 'Nettoie les fichiers temporaires (>24h) et les vieux logs (>30 jours)';

    public function handle()
    {
        // --- 1. Nettoyage du dossier TEMP ---
        $tempPath = storage_path('app/temp');
        $filesDeleted = 0;

        if (File::exists($tempPath)) {
            $files = File::allFiles($tempPath);
            
            foreach ($files as $file) {
                // On récupère le timestamp et on le convertit en Carbon
                $fileDate = Carbon::createFromTimestamp(File::lastModified($file));

                // Si le fichier a plus de 24h
                if (now()->diffInHours($fileDate) > 24) {
                    
                    // Protection du cache watermark
                    if (str_contains($file->getFilename(), 'watermark_opt')) {
                        continue;
                    }
                    
                    File::delete($file);
                    $filesDeleted++;
                }
            }
        }
        $this->info("Temp : {$filesDeleted} fichiers supprimés.");

        // --- 2. Nettoyage des LOGS Laravel ---
        $logPath = storage_path('logs');
        $logsDeleted = 0;

        if (File::exists($logPath)) {
            $logFiles = File::allFiles($logPath);
            foreach ($logFiles as $file) {
                if ($file->getExtension() === 'log') {
                    // Conversion timestamp -> Carbon
                    $fileDate = Carbon::createFromTimestamp(File::lastModified($file));

                    // Si plus vieux que 30 jours
                    if (now()->diffInDays($fileDate) > 30) {
                        File::delete($file);
                        $logsDeleted++;
                    }
                }
            }
        }
        $this->info("Logs : {$logsDeleted} vieux fichiers supprimés.");
    }
}