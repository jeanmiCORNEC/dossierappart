<?php

namespace Database\Factories;

use App\Enums\DossierStatus;
use App\Models\Dossier;
use App\Models\Pays;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dossier>
 */
class DossierFactory extends Factory
{
    protected $model = Dossier::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Récupérer la France par défaut (suppose que les seeders ont été exécutés)
        $france = Pays::where('code', 'FR')->first();

        return [
            'pays_id' => $france ? $france->id : 1, // Fallback sur ID 1 si France non trouvée
            'email' => null,
            'status' => DossierStatus::DRAFT,
            'download_token' => Str::random(32), // Sera aussi généré par le boot() du model
            'stripe_payment_id' => null,
            'expires_at' => null,
        ];
    }
}
