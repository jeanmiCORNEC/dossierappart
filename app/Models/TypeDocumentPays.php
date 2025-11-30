<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TypeDocumentPays extends Model
{
    use HasFactory;

    protected $table = 'types_documents_pays';

    protected $fillable = [
        'pays_id',
        'code',
        'libelle',
        'description',
        'ordre',
    ];

    protected function casts(): array
    {
        return [
            'ordre' => 'integer',
        ];
    }

    /**
     * Le pays auquel appartient ce type de document
     */
    public function pays(): BelongsTo
    {
        return $this->belongsTo(Pays::class);
    }

    /**
     * Scope pour ordonner les types par ordre d'affichage
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('ordre');
    }
}
