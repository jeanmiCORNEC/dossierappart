<?php

namespace Tests\Feature;

use App\Enums\DossierStatus;
use App\Models\Dossier;
use App\Models\Pays;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateDossierTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Configuration avant chaque test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(); // On peuple la base (Pays, Types)
    }

    public function test_guest_can_create_dossier(): void
    {
        // 1. Récupérer la France
        $france = Pays::where('code', 'FR')->first();

        // 2. Faire la requête POST pour créer un dossier
        $response = $this->post('/dossiers', [
            'pays_id' => $france->id,
        ]);

        // 3. Vérifier qu'un dossier a été créé
        $this->assertDatabaseCount('dossiers', 1);

        $dossier = Dossier::first();

        // 4. Vérifier les attributs du dossier
        $this->assertEquals($france->id, $dossier->pays_id);
        $this->assertEquals(DossierStatus::DRAFT, $dossier->status);
        $this->assertNotNull($dossier->download_token); // Doit être généré automatiquement
        $this->assertNull($dossier->email); // Pas encore d'email

        // 5. Vérifier la redirection vers la page d'upload
        // La route sera /dossiers/{uuid}/upload
        $response->assertRedirect("/dossiers/{$dossier->id}/upload");
    }

    public function test_cannot_create_dossier_with_invalid_country(): void
    {
        $response = $this->post('/dossiers', [
            'pays_id' => 999999, // ID inexistant
        ]);

        $response->assertSessionHasErrors('pays_id');
        $this->assertDatabaseCount('dossiers', 0);
    }
}
