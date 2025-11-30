<?php

namespace Tests\Feature;

use App\Models\Pays;
use App\Models\TypeDocumentPays;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSetupTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Configuration avant chaque test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // On force le seeding manuellement car la propriété $seed semble capricieuse
        $this->seed();
    }

    /**
     * Vérifie que la France est bien configurée avec ses 4 types de documents.
     */
    public function test_database_has_france_with_4_document_types(): void
    {
        // Vérifier que la France existe et est active
        $france = Pays::where('code', 'FR')->first();

        $this->assertNotNull($france, 'Le pays France (FR) devrait exister en base.');
        $this->assertEquals('France', $france->nom);
        $this->assertTrue($france->actif, 'La France devrait être active.');

        // Vérifier qu'il y a exactement 4 types de documents pour la France
        $typesCount = TypeDocumentPays::where('pays_id', $france->id)->count();
        $this->assertEquals(4, $typesCount, 'Il devrait y avoir 4 types de documents pour la France.');

        // Vérifier que tous les types attendus sont présents
        $expectedTypes = ['identite', 'domicile', 'situation_pro', 'ressources'];
        $actualTypes = TypeDocumentPays::where('pays_id', $france->id)
            ->pluck('code')
            ->toArray();

        // On trie les tableaux pour comparer le contenu indépendamment de l'ordre
        sort($expectedTypes);
        sort($actualTypes);

        $this->assertEquals($expectedTypes, $actualTypes, 'Les codes des types de documents ne correspondent pas.');
    }

    /**
     * Vérifie que la Belgique et la Suisse sont présentes mais désactivées.
     */
    public function test_belgium_and_switzerland_are_disabled(): void
    {
        $belgique = Pays::where('code', 'BE')->first();
        $suisse = Pays::where('code', 'CH')->first();

        $this->assertNotNull($belgique, 'La Belgique (BE) devrait exister.');
        $this->assertFalse($belgique->actif, 'La Belgique devrait être désactivée pour le MVP.');

        $this->assertNotNull($suisse, 'La Suisse (CH) devrait exister.');
        $this->assertFalse($suisse->actif, 'La Suisse devrait être désactivée pour le MVP.');
    }
}
