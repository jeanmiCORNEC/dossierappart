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

    /**
     * Configuration avant chaque test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        // $this->seed(); // On peuple la base (Pays, Types)

        // Utiliser un disque de stockage fake pour les tests
        Storage::fake('local');
    }

    public function test_can_upload_pdf_to_dossier(): void
    {
        // 1. Créer les données de test
        $france = Pays::create([
            'code' => 'FR',
            'nom' => 'France',
            'indicatif' => '+33',
        ]);

        $typeDocument = TypeDocumentPays::create([
            'pays_id' => $france->id,
            'code' => 'identite',
            'libelle' => 'Pièce d\'identité',
            'ordre' => 1,
        ]);

        $dossier = Dossier::factory()->create(['pays_id' => $france->id]);

        // 2. Créer un faux fichier PDF
        $file = UploadedFile::fake()->create('carte-identite.pdf', 2000); // 2 MB

        // 3. Uploader le fichier
        $response = $this->post("/dossiers/{$dossier->id}/documents", [
            'type_document_pays_id' => $typeDocument->id,
            'file' => $file,
        ]);

        // 4. Vérifier qu'un document a été créé en base
        $this->assertEquals(1, $dossier->documents()->count());
        $this->assertDatabaseHas('documents', [
            'dossier_id' => $dossier->id,
            'type_document_pays_id' => $typeDocument->id,
            'original_filename' => 'carte-identite.pdf',
        ]);

        // 5. Vérifier que le fichier a été stocké
        $document = $dossier->documents()->first();
        // utiliser exists() et l'assertion PHPUnit pour éviter l'erreur "Undefined method"
        $this->assertTrue(Storage::disk('local')->exists($document->storage_path));

        // 6. Vérifier la réponse (redirect back with success)
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Document ajouté avec succès');
    }

    public function test_can_upload_image_to_dossier(): void
    {
        // 1. Créer les données de test
        $france = Pays::create([
            'code' => 'FR',
            'nom' => 'France',
            'indicatif' => '+33',
        ]);

        $typeDocument = TypeDocumentPays::create([
            'pays_id' => $france->id,
            'code' => 'identite',
            'libelle' => 'Pièce d\'identité',
            'ordre' => 1,
        ]);

        $dossier = Dossier::factory()->create(['pays_id' => $france->id]);

        // 2. Créer une fausse image JPG
        $file = UploadedFile::fake()->image('photo.jpg')->size(3000); // 3 MB

        // 3. Uploader le fichier
        $response = $this->post("/dossiers/{$dossier->id}/documents", [
            'type_document_pays_id' => $typeDocument->id,
            'file' => $file,
        ]);

        // 4. Vérifier qu'un document a été créé
        $this->assertEquals(1, $dossier->documents()->count());
        // 5. Vérifier que le fichier a été stocké
        $document = $dossier->documents()->first();
        // utiliser exists() et l'assertion PHPUnit pour éviter l'erreur "Undefined method"
        $this->assertTrue(Storage::disk('local')->exists($document->storage_path));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Document ajouté avec succès');
    }

    public function test_rejects_files_over_10mb(): void
    {
        // 1. Créer les données de test
        $france = Pays::create([
            'code' => 'FR',
            'nom' => 'France',
            'indicatif' => '+33',
        ]);

        $typeDocument = TypeDocumentPays::create([
            'pays_id' => $france->id,
            'code' => 'identite',
            'libelle' => 'Pièce d\'identité',
            'ordre' => 1,
        ]);

        $dossier = Dossier::factory()->create(['pays_id' => $france->id]);

        // 2. Créer un fichier de 11 MB (> 10 MB)
        $file = UploadedFile::fake()->create('gros-fichier.pdf', 11000); // 11 MB

        // 3. Tenter d'uploader le fichier
        $response = $this->post("/dossiers/{$dossier->id}/documents", [
            'type_document_pays_id' => $typeDocument->id,
            'file' => $file,
        ]);

        // 4. Vérifier qu'il y a une erreur de validation
        $response->assertSessionHasErrors('file');

        // 5. Vérifier qu'aucun document n'a été créé
        $this->assertEquals(0, $dossier->documents()->count());
    }

    public function test_rejects_invalid_file_types(): void
    {
        // 1. Créer les données de test
        $france = Pays::create([
            'code' => 'FR',
            'nom' => 'France',
            'indicatif' => '+33',
        ]);

        $typeDocument = TypeDocumentPays::create([
            'pays_id' => $france->id,
            'code' => 'identite',
            'libelle' => 'Pièce d\'identité',
            'ordre' => 1,
        ]);

        $dossier = Dossier::factory()->create(['pays_id' => $france->id]);

        // 2. Créer un fichier .exe (type interdit)
        $file = UploadedFile::fake()->create('virus.exe', 100);

        // 3. Tenter d'uploader le fichier
        $response = $this->post("/dossiers/{$dossier->id}/documents", [
            'type_document_pays_id' => $typeDocument->id,
            'file' => $file,
        ]);

        // 4. Vérifier qu'il y a une erreur de validation
        $response->assertSessionHasErrors('file');

        // 5. Vérifier qu'aucun document n'a été créé
        $this->assertEquals(0, $dossier->documents()->count());
    }
}
