<?php

namespace Database\Factories;

use App\Models\Pays;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pays>
 */
class PaysFactory extends Factory
{
    protected $model = Pays::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->countryCode()),
            'nom' => $this->faker->country(),
            'actif' => true,
        ];
    }

    /**
     * Indicate that the pays is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'actif' => false,
        ]);
    }
}
