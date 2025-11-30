<?php

namespace Database\Factories;

use App\Models\Pays;
use App\Models\TypeDocumentPays;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TypeDocumentPays>
 */
class TypeDocumentPaysFactory extends Factory
{
    protected $model = TypeDocumentPays::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pays_id' => Pays::factory(),
            'code' => $this->faker->unique()->word(),
            'libelle' => $this->faker->sentence(3),
            'description' => $this->faker->optional()->paragraph(),
            'ordre' => $this->faker->numberBetween(1, 10),
        ];
    }
}
