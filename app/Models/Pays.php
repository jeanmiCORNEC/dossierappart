<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pays extends Model
{
    use HasFactory;

    protected $table = 'pays';

    protected $fillable = [
        'code',
        'nom',
        'actif',
    ];

    protected function casts(): array
    {
        return [
            'actif' => 'boolean',
        ];
    }

    /**
     * Types de documents pour ce pays
     */
    /**
     * Types de documents pour ce pays
     */
    public function typesDocumentsPays(): HasMany
    {
        return $this->hasMany(TypeDocumentPays::class);
    }

    /**
     * Dossiers créés pour ce pays
     */
    public function dossiers(): HasMany
    {
        return $this->hasMany(Dossier::class);
    }

    /**
     * Scope pour récupérer uniquement les pays actifs
     */
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }
}
