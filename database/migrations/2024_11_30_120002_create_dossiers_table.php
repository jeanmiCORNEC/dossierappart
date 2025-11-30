<?php

use App\Enums\DossierStatus;
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
        Schema::create('dossiers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('pays_id')->constrained()->onDelete('cascade');
            $table->string('email')->nullable(); // Récupéré post-paiement via Stripe
            $table->string('status')->default(DossierStatus::DRAFT->value); // Enum
            $table->string('download_token')->unique()->nullable(); // Jeton sécurisé pour téléchargement
            $table->string('stripe_payment_id')->nullable(); // ID du paiement Stripe
            $table->timestamp('expires_at')->nullable(); // Date de suppression (paid_at + 24h)
            $table->timestamps();
            $table->softDeletes(); // Pour traçabilité RGPD 30j
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossiers');
    }
};
