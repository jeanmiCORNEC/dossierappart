<?php

namespace Database\Seeders;

use App\Models\Pays;
use App\Models\TypeDocumentPays;
use Illuminate\Database\Seeder;

class TypesDocumentsPaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $france = Pays::where('code', 'FR')->first();

        if (!$france) {
            $this->command->error('❌ Le pays France n\'existe pas. Lancez d\'abord PaysSeeder.');
            return;
        }

        $typesFR = [
            [
                'code' => 'identite',
                'libelle' => 'Justificatif d\'identité',
                'description' => 'Carte Nationale d\'Identité, Passeport, Titre de séjour',
                'ordre' => 1,
            ],
            [
                'code' => 'domicile',
                'libelle' => 'Justificatif de domicile',
                'description' => 'Facture d\'électricité, Quittance de loyer, Attestation d\'hébergement (moins de 3 mois)',
                'ordre' => 2,
            ],
            [
                'code' => 'situation_pro',
                'libelle' => 'Justificatif de situation professionnelle',
                'description' => 'Contrat de travail, Attestation employeur, Carte étudiant, Avis de situation Pôle Emploi',
                'ordre' => 3,
            ],
            [
                'code' => 'ressources',
                'libelle' => 'Justificatif de ressources',
                'description' => 'Bulletins de salaire (3 derniers mois), Avis d\'imposition, Attestation de ressources',
                'ordre' => 4,
            ],
        ];

        foreach ($typesFR as $type) {
            TypeDocumentPays::create(array_merge($type, ['pays_id' => $france->id]));
        }

        $this->command->info('✅ 4 types de documents créés pour la France');
    }
}
