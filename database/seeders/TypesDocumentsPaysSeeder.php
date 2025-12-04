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
        $belgique = Pays::where('code', 'BE')->first();
        $suisse = Pays::where('code', 'CH')->first();

        if (!$france || !$belgique || !$suisse) {
            $this->command->error('❌ Les pays n\'existent pas. Lancez d\'abord PaysSeeder.');
            return;
        }

        // Types de documents pour la FRANCE
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
                'code' => 'professionnel',
                'libelle' => 'Justificatif de situation professionnelle',
                'description' => 'Contrat de travail, Attestation employeur, Carte étudiant, Avis de situation Pôle Emploi',
                'ordre' => 3,
            ],
            [
                'code' => 'financier',
                'libelle' => 'Justificatif financier',
                'description' => 'Bulletins de salaire (3 derniers mois), Avis d\'imposition, Attestation de ressources',
                'ordre' => 4,
            ],
            [
                'code' => 'fiscal',
                'libelle' => 'Justificatif fiscal',
                'description' => 'Avis d\'imposition, Attestation de non-imposition',
                'ordre' => 5,
            ],
            [
                'code' => 'garant',
                'libelle' => 'Documents du garant',
                'description' => 'Pièce d\'identité, Justificatif de domicile, Bulletins de salaire du garant',
                'ordre' => 6,
            ],
            [
                'code' => 'autre',
                'libelle' => 'Autres documents',
                'description' => 'Tout autre document complémentaire',
                'ordre' => 7,
            ],
        ];

        // Types de documents pour la BELGIQUE
        $typesBE = [
            [
                'code' => 'identite',
                'libelle' => 'Justificatif d\'identité',
                'description' => 'Carte d\'identité belge, Passeport, Titre de séjour',
                'ordre' => 1,
            ],
            [
                'code' => 'domicile',
                'libelle' => 'Justificatif de domicile',
                'description' => 'Facture d\'électricité, Quittance de loyer, Composition de ménage (moins de 3 mois)',
                'ordre' => 2,
            ],
            [
                'code' => 'professionnel',
                'libelle' => 'Justificatif de situation professionnelle',
                'description' => 'Contrat de travail, Attestation employeur, Carte étudiant, Attestation ONEM',
                'ordre' => 3,
            ],
            [
                'code' => 'financier',
                'libelle' => 'Justificatif financier',
                'description' => 'Fiches de paie (3 derniers mois), Avertissement extrait de rôle, Attestation bancaire',
                'ordre' => 4,
            ],
            [
                'code' => 'fiscal',
                'libelle' => 'Justificatif fiscal',
                'description' => 'Avertissement extrait de rôle, Attestation de non-imposition',
                'ordre' => 5,
            ],
            [
                'code' => 'solvabilite',
                'libelle' => 'Justificatif de solvabilité',
                'description' => 'Attestation bancaire, Extrait de compte, Preuve de fonds disponibles',
                'ordre' => 6,
            ],
            [
                'code' => 'garant',
                'libelle' => 'Documents du garant',
                'description' => 'Pièce d\'identité, Justificatif de domicile, Fiches de paie du garant',
                'ordre' => 7,
            ],
            [
                'code' => 'autre',
                'libelle' => 'Autres documents',
                'description' => 'Tout autre document complémentaire',
                'ordre' => 8,
            ],
        ];

        // Types de documents pour la SUISSE
        $typesCH = [
            [
                'code' => 'identite',
                'libelle' => 'Justificatif d\'identité',
                'description' => 'Carte d\'identité suisse, Passeport, Permis de séjour',
                'ordre' => 1,
            ],
            [
                'code' => 'domicile',
                'libelle' => 'Justificatif de domicile',
                'description' => 'Facture d\'électricité, Quittance de loyer, Attestation de domicile (moins de 3 mois)',
                'ordre' => 2,
            ],
            [
                'code' => 'professionnel',
                'libelle' => 'Justificatif de situation professionnelle',
                'description' => 'Contrat de travail, Attestation employeur, Carte étudiant, Attestation chômage',
                'ordre' => 3,
            ],
            [
                'code' => 'financier',
                'libelle' => 'Justificatif financier',
                'description' => 'Bulletins de salaire (3 derniers mois), Attestation fiscale, Relevés bancaires',
                'ordre' => 4,
            ],
            [
                'code' => 'solvabilite',
                'libelle' => 'Justificatif de solvabilité',
                'description' => 'Attestation bancaire, Extrait de compte, Certificat de solvabilité',
                'ordre' => 5,
            ],
            [
                'code' => 'garant',
                'libelle' => 'Documents du garant',
                'description' => 'Pièce d\'identité, Justificatif de domicile, Bulletins de salaire du garant',
                'ordre' => 6,
            ],
            [
                'code' => 'autre',
                'libelle' => 'Autres documents',
                'description' => 'Tout autre document complémentaire',
                'ordre' => 7,
            ],
        ];

        // Insertion des types pour chaque pays
        foreach ($typesFR as $type) {
            TypeDocumentPays::create(array_merge($type, ['pays_id' => $france->id]));
        }

        foreach ($typesBE as $type) {
            TypeDocumentPays::create(array_merge($type, ['pays_id' => $belgique->id]));
        }

        foreach ($typesCH as $type) {
            TypeDocumentPays::create(array_merge($type, ['pays_id' => $suisse->id]));
        }

        $this->command->info('✅ Types de documents créés pour les 3 pays :');
        $this->command->info('   - France: 7 types (avec fiscal)');
        $this->command->info('   - Belgique: 8 types (avec fiscal et solvabilité)');
        $this->command->info('   - Suisse: 7 types (avec solvabilité)');
    }
}
