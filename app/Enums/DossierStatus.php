<?php

namespace App\Enums;

enum DossierStatus: string
{
    case DRAFT = 'draft';           // En cours d'upload
    case PAID = 'paid';             // Paiement validé, en attente traitement
    case PROCESSING = 'processing'; // Traitement PDF en cours
    case COMPLETED = 'completed';   // PDF généré, prêt à télécharger
    case FAILED = 'failed';         // Échec de génération

    /**
     * Obtenir le libellé pour l'affichage
     */
    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Brouillon',
            self::PAID => 'Payé',
            self::PROCESSING => 'En cours de traitement',
            self::COMPLETED => 'Terminé',
            self::FAILED => 'Échec',
        };
    }

    /**
     * Obtenir la couleur pour l'UI
     */
    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::PAID => 'blue',
            self::PROCESSING => 'yellow',
            self::COMPLETED => 'green',
            self::FAILED => 'red',
        };
    }
}
