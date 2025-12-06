<?php

namespace Tests\Feature\Console;

use App\Enums\DossierStatus;
use App\Models\Dossier;
use App\Models\Pays;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CleanUpCommandsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // 1. Setup Base de données
        $this->seed();
        
        // 2. Setup Disque
        Storage::fake('local');
        
        // 3. Setup Dossier Temp réel
        if (!File::exists(storage_path('app/temp'))) {
            File::makeDirectory(storage_path('app/temp'), 0755, true);
        }
    }

    public function test_dossier_clean_command_removes_correct_records(): void
    {
        $pays = Pays::first();

        // --- CAS 1 : Brouillon abandonné (DOIT ÊTRE SUPPRIMÉ) ---
        $abandonedDraft = Dossier::factory()->create([
            'status' => DossierStatus::DRAFT,
            'pays_id' => $pays->id
        ]);
        // Méthode Nucléaire : On force la date via SQL direct
        // On le met il y a 100 heures
        DB::table('dossiers')
            ->where('id', $abandonedDraft->id)
            ->update(['created_at' => now()->subHours(100)]);
            
        Storage::disk('local')->makeDirectory("dossiers/{$abandonedDraft->id}");

        // --- CAS 2 : Dossier Payé Expiré (DOIT ÊTRE SUPPRIMÉ) ---
        $expiredPaid = Dossier::factory()->create([
            'status' => DossierStatus::PAID,
            'pays_id' => $pays->id,
            'expires_at' => now()->subHours(2) // Expiré il y a 2h
        ]);
        Storage::disk('local')->makeDirectory("dossiers/{$expiredPaid->id}");

        // --- CAS 3 : Brouillon Récent (DOIT RESTER) ---
        $recentDraft = Dossier::factory()->create([
            'status' => DossierStatus::DRAFT,
            'pays_id' => $pays->id
        ]);
        // created_at est "maintenant" par défaut

        // --- CAS 4 : Dossier Payé Valide (DOIT RESTER) ---
        $validPaid = Dossier::factory()->create([
            'status' => DossierStatus::PAID,
            'pays_id' => $pays->id,
            'expires_at' => now()->addDay() // Expire demain
        ]);

        // --- EXÉCUTION ---
        $this->artisan('dossier:clean')->assertExitCode(0);

        // --- VÉRIFICATIONS ---
        $this->assertModelMissing($abandonedDraft, 'Le vieux brouillon doit être supprimé (Date forcée SQL)');
        $this->assertModelMissing($expiredPaid, 'Le dossier expiré doit être supprimé');
        
        $this->assertModelExists($recentDraft, 'Le brouillon récent doit rester');
        $this->assertModelExists($validPaid, 'Le dossier valide doit rester');
        
        $this->assertFalse(Storage::disk('local')->exists("dossiers/{$expiredPaid->id}"), 'Le dossier physique doit être supprimé');
    }

    public function test_system_clean_command_removes_old_files(): void
    {
        $tempPath = storage_path('app/temp');
        
        // --- CAS 1 : Fichier Vieux (DOIT ÊTRE SUPPRIMÉ) ---
        $oldFile = $tempPath . '/test_old_' . uniqid() . '.tmp';
        File::put($oldFile, 'delete me');
        
        // On modifie physiquement la date du fichier (Il y a 5 jours)
        $oldTime = time() - (5 * 24 * 3600);
        touch($oldFile, $oldTime);
        clearstatcache(true, $oldFile); // Force PHP à oublier l'ancienne date

        // --- CAS 2 : Fichier Récent (DOIT RESTER) ---
        $newFile = $tempPath . '/test_new_' . uniqid() . '.tmp';
        File::put($newFile, 'keep me');
        // Date = maintenant

        // --- EXÉCUTION ---
        $this->artisan('system:clean')->assertExitCode(0);

        // --- VÉRIFICATIONS ---
        $this->assertFileDoesNotExist($oldFile, 'Le fichier vieux de 5 jours doit être supprimé');
        $this->assertFileExists($newFile, 'Le fichier récent doit être conservé');

        // Nettoyage manuel
        if (File::exists($newFile)) File::delete($newFile);
        if (File::exists($oldFile)) File::delete($oldFile);
    }
}