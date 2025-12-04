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
                'actif' => true, // Activé pour l'ouverture internationale
            ],
            [
                'code' => 'CH',
                'nom' => 'Suisse',
                'actif' => true, // Activé pour l'ouverture internationale
            ],
        ];

        foreach ($pays as $p) {
            Pays::create($p);
        }

        $this->command->info('✅ 3 pays créés (FR, BE, CH tous actifs)');
    }
}
