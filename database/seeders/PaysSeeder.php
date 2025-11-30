<?php

namespace Database\Seeders;

use App\Models\Pays;
use Illuminate\Database\Seeder;

class PaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pays = [
            [
                'code' => 'FR',
                'nom' => 'France',
                'actif' => true,
            ],
            [
                'code' => 'BE',
                'nom' => 'Belgique',
                'actif' => false, // Désactivé pour le MVP
            ],
            [
                'code' => 'CH',
                'nom' => 'Suisse',
                'actif' => false, // Désactivé pour le MVP
            ],
        ];

        foreach ($pays as $p) {
            Pays::create($p);
        }

        $this->command->info('✅ 3 pays créés (FR actif, BE/CH désactivés)');
    }
}
