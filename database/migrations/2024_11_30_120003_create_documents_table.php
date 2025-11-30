<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('dossier_id')->constrained()->onDelete('cascade');
            $table->foreignId('type_document_pays_id')->constrained('types_documents_pays')->onDelete('cascade');
            $table->string('original_filename'); // Nom original du fichier
            $table->string('storage_path'); // Chemin vers le fichier brut (temporaire)
            $table->integer('sort_order')->default(0); // Pour l'ordre d'affichage dans le PDF final
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
