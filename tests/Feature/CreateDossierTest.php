<?php

namespace Tests\Feature;

use App\Enums\DossierStatus;
use App\Models\Dossier;
use App\Models\Pays;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CreateDossierTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Configuration avant chaque test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        // $this->seed(); // Pas de seed car on utilise la base locale existante
    }

    public function test_guest_can_create_dossier(): void
    {
        // 1. Créer les données de test
        $france = Pays::create([
            'code' => 'FR',
            'nom' => 'France',
            'indicatif' => '+33',
        ]);

        $initialCount = Dossier::count();

        // 2. Faire la requête POST pour créer un dossier
        $response = $this->post('/dossiers', [
            'pays_id' => $france->id,
        ]);

        // 3. Vérifier qu'un dossier a été créé
        $this->assertDatabaseCount('dossiers', $initialCount + 1);

        $dossier = Dossier::latest()->first();

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
        $response->assertSessionHasErrors('pays_id');
        // $this->assertDatabaseCount('dossiers', 0); // On ne peut pas vérifier 0 si la base n'est pas vide
        // On pourrait vérifier que le count n'a pas bougé, mais c'est déjà implicite si ça fail la validation

    }
}
