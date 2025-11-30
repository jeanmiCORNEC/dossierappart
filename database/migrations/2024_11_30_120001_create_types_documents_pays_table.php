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
        Schema::create('types_documents_pays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pays_id')->constrained()->onDelete('cascade');
            $table->string('code'); // identite, domicile, situation_pro, ressources
            $table->string('libelle'); // "Justificatif d'identité"
            $table->text('description')->nullable(); // "CNI, Passeport, Titre de séjour"
            $table->integer('ordre')->default(0); // Pour l'affichage ordonné
            $table->timestamps();

            $table->unique(['pays_id', 'code']); // Un seul "identite" par pays
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('types_documents_pays');
    }
};
