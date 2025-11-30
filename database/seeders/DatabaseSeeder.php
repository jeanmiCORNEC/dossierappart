<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seedeurs personnalisés DossierAppart
        $this->call([
            PaysSeeder::class,
            TypesDocumentsPaysSeeder::class,
        ]);

        $this->command->info('✅ Base de données initialisée avec succès !');
    }
}
