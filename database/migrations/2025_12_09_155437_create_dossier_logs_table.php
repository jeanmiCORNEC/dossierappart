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
         Schema::create('dossier_logs', function (Blueprint $table) {
            $table->id();
            
            // Lien vers le dossier
            $table->foreignUuid('dossier_id')
                  ->constrained('dossiers')
                  ->cascadeOnDelete(); // Si le dossier est supprimé (Cron), les logs partent avec (RGPD compliant)
            
            $table->string('action_type'); // ex: 'legal_consent', 'email_sent', 'download'
            $table->string('ip_address')->nullable(); // Important pour la preuve juridique
            $table->string('user_agent')->nullable(); // Info device (optionnel mais utile)
            $table->text('details')->nullable(); // Contenu du mail ou texte de la checkbox
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossier_logs');
    }
};
