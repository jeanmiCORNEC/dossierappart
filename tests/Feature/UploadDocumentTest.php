<?php

namespace Tests\Feature;

use App\Models\Dossier;
use App\Models\Pays;
use App\Models\TypeDocumentPays;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadDocumentTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function test_can_upload_pdf_to_dossier(): void
    {
        $france = Pays::firstOrCreate(['code' => 'FR'], ['nom' => 'France', 'indicatif' => '+33']);
        $typeDocument = TypeDocumentPays::firstOrCreate(
            ['pays_id' => $france->id, 'code' => 'identite'],
            ['libelle' => 'Pièce d\'identité', 'ordre' => 1]
        );
        $dossier = Dossier::factory()->create(['pays_id' => $france->id]);
        $file = UploadedFile::fake()->create('carte-identite.pdf', 2000);

        $response = $this->post("/dossiers/{$dossier->id}/documents", [
            'type_document_pays_id' => $typeDocument->id,
            'file' => $file,
        ]);

        $this->assertEquals(1, $dossier->documents()->count());
        $this->assertDatabaseHas('documents', [
            'dossier_id' => $dossier->id,
            'type_document_pays_id' => $typeDocument->id,
            'original_filename' => 'carte-identite.pdf',
        ]);
        $document = $dossier->documents()->first();
        $this->assertTrue(Storage::disk('local')->exists($document->storage_path));
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Document ajouté avec succès');
    }

    public function test_can_upload_image_to_dossier(): void
    {
        $france = Pays::firstOrCreate(['code' => 'FR'], ['nom' => 'France', 'indicatif' => '+33']);
        $typeDocument = TypeDocumentPays::firstOrCreate(
            ['pays_id' => $france->id, 'code' => 'identite'],
            ['libelle' => 'Pièce d\'identité', 'ordre' => 1]
        );
        $dossier = Dossier::factory()->create(['pays_id' => $france->id]);
        $file = UploadedFile::fake()->image('photo.jpg')->size(3000);

        $response = $this->post("/dossiers/{$dossier->id}/documents", [
            'type_document_pays_id' => $typeDocument->id,
            'file' => $file,
        ]);

        $this->assertEquals(1, $dossier->documents()->count());
        $document = $dossier->documents()->first();
        $this->assertTrue(Storage::disk('local')->exists($document->storage_path));
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Document ajouté avec succès');
    }

    public function test_rejects_files_over_10mb(): void
    {
        $france = Pays::firstOrCreate(['code' => 'FR'], ['nom' => 'France', 'indicatif' => '+33']);
        $typeDocument = TypeDocumentPays::firstOrCreate(
            ['pays_id' => $france->id, 'code' => 'identite'],
            ['libelle' => 'Pièce d\'identité', 'ordre' => 1]
        );
        $dossier = Dossier::factory()->create(['pays_id' => $france->id]);
        $file = UploadedFile::fake()->create('gros-fichier.pdf', 11000);

        $response = $this->post("/dossiers/{$dossier->id}/documents", [
            'type_document_pays_id' => $typeDocument->id,
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('file');
        $this->assertEquals(0, $dossier->documents()->count());
    }

    public function test_rejects_invalid_file_types(): void
    {
        $france = Pays::firstOrCreate(['code' => 'FR'], ['nom' => 'France', 'indicatif' => '+33']);
        $typeDocument = TypeDocumentPays::firstOrCreate(
            ['pays_id' => $france->id, 'code' => 'identite'],
            ['libelle' => 'Pièce d\'identité', 'ordre' => 1]
        );
        $dossier = Dossier::factory()->create(['pays_id' => $france->id]);
        $file = UploadedFile::fake()->create('virus.exe', 100);

        $response = $this->post("/dossiers/{$dossier->id}/documents", [
            'type_document_pays_id' => $typeDocument->id,
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('file');
        $this->assertEquals(0, $dossier->documents()->count());
    }
}
