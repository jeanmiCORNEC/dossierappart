<?php

namespace Tests\Feature;

use App\Models\Pays;
use App\Models\TypeDocumentPays;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DocumentTypesByCountryTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Types de documents communs à tous les pays
     */
    private array $commonTypes = [
        'identite',
        'domicile',
        'professionnel',
        'financier',
        'garant',
        'autre',
    ];

    public function test_france_has_correct_document_types(): void
    {
        $france = Pays::where('code', 'FR')->first();
        $this->assertNotNull($france, 'France should exist in database');

        $typeCodes = $france->typesDocumentsPays()->pluck('code')->toArray();

        // France should have all common types
        foreach ($this->commonTypes as $code) {
            $this->assertContains($code, $typeCodes, "France should have type: {$code}");
        }

        // France should have fiscal
        $this->assertContains('fiscal', $typeCodes, 'France should have type: fiscal');

        // France should NOT have solvabilite
        $this->assertNotContains('solvabilite', $typeCodes, 'France should NOT have type: solvabilite');

        // France should have exactly 7 types
        $this->assertCount(7, $typeCodes, 'France should have exactly 7 document types');
    }

    public function test_belgium_has_correct_document_types(): void
    {
        $belgique = Pays::where('code', 'BE')->first();
        $this->assertNotNull($belgique, 'Belgium should exist in database');

        $typeCodes = $belgique->typesDocumentsPays()->pluck('code')->toArray();

        // Belgium should have all common types
        foreach ($this->commonTypes as $code) {
            $this->assertContains($code, $typeCodes, "Belgium should have type: {$code}");
        }

        // Belgium should have fiscal
        $this->assertContains('fiscal', $typeCodes, 'Belgium should have type: fiscal');

        // Belgium should have solvabilite
        $this->assertContains('solvabilite', $typeCodes, 'Belgium should have type: solvabilite');

        // Belgium should have exactly 8 types
        $this->assertCount(8, $typeCodes, 'Belgium should have exactly 8 document types');
    }

    public function test_switzerland_has_correct_document_types(): void
    {
        $suisse = Pays::where('code', 'CH')->first();
        $this->assertNotNull($suisse, 'Switzerland should exist in database');

        $typeCodes = $suisse->typesDocumentsPays()->pluck('code')->toArray();

        // Switzerland should have all common types
        foreach ($this->commonTypes as $code) {
            $this->assertContains($code, $typeCodes, "Switzerland should have type: {$code}");
        }

        // Switzerland should have solvabilite
        $this->assertContains('solvabilite', $typeCodes, 'Switzerland should have type: solvabilite');

        // Switzerland should NOT have fiscal
        $this->assertNotContains('fiscal', $typeCodes, 'Switzerland should NOT have type: fiscal');

        // Switzerland should have exactly 7 types
        $this->assertCount(7, $typeCodes, 'Switzerland should have exactly 7 document types');
    }

    public function test_all_countries_are_active(): void
    {
        $countries = Pays::whereIn('code', ['FR', 'BE', 'CH'])->get();

        $this->assertCount(3, $countries, 'There should be 3 countries');

        foreach ($countries as $country) {
            $this->assertTrue($country->actif, "{$country->nom} should be active");
        }
    }

    public function test_no_deprecated_document_types_exist(): void
    {
        $deprecatedTypes = ['situation_pro', 'ressources', 'divers'];

        foreach ($deprecatedTypes as $deprecatedType) {
            $count = TypeDocumentPays::where('code', $deprecatedType)->count();
            $this->assertEquals(0, $count, "Deprecated type '{$deprecatedType}' should not exist");
        }
    }

    public function test_fiscal_only_exists_for_france_and_belgium(): void
    {
        $fiscalTypes = TypeDocumentPays::where('code', 'fiscal')
            ->with('pays')
            ->get();

        $countryCodes = $fiscalTypes->pluck('pays.code')->toArray();

        $this->assertContains('FR', $countryCodes, 'fiscal should exist for France');
        $this->assertContains('BE', $countryCodes, 'fiscal should exist for Belgium');
        $this->assertNotContains('CH', $countryCodes, 'fiscal should NOT exist for Switzerland');
        $this->assertCount(2, $fiscalTypes, 'fiscal should exist for exactly 2 countries');
    }

    public function test_solvabilite_only_exists_for_belgium_and_switzerland(): void
    {
        $solvabiliteTypes = TypeDocumentPays::where('code', 'solvabilite')
            ->with('pays')
            ->get();

        $countryCodes = $solvabiliteTypes->pluck('pays.code')->toArray();

        $this->assertNotContains('FR', $countryCodes, 'solvabilite should NOT exist for France');
        $this->assertContains('BE', $countryCodes, 'solvabilite should exist for Belgium');
        $this->assertContains('CH', $countryCodes, 'solvabilite should exist for Switzerland');
        $this->assertCount(2, $solvabiliteTypes, 'solvabilite should exist for exactly 2 countries');
    }
}
