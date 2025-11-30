<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\Dossier;
use App\Models\TypeDocumentPays;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    protected $model = Document::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isPdf = $this->faker->boolean(70); // 70% PDF, 30% images

        return [
            'dossier_id' => Dossier::factory(),
            'type_document_pays_id' => TypeDocumentPays::factory(),
            'original_filename' => $isPdf
                ? $this->faker->word() . '.pdf'
                : $this->faker->word() . '.' . $this->faker->randomElement(['jpg', 'png']),
            'storage_path' => 'dossiers/test/' . $this->faker->uuid() . ($isPdf ? '.pdf' : '.jpg'),
            'sort_order' => $this->faker->numberBetween(1, 20),
        ];
    }

    /**
     * Indicate that the document is a PDF.
     */
    public function pdf(): static
    {
        return $this->state(fn(array $attributes) => [
            'original_filename' => $this->faker->word() . '.pdf',
            'storage_path' => 'dossiers/test/' . $this->faker->uuid() . '.pdf',
        ]);
    }

    /**
     * Indicate that the document is an image.
     */
    public function image(string $extension = 'jpg'): static
    {
        return $this->state(fn(array $attributes) => [
            'original_filename' => $this->faker->word() . '.' . $extension,
            'storage_path' => 'dossiers/test/' . $this->faker->uuid() . '.' . $extension,
        ]);
    }
}
