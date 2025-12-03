<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'dossier_id',
        'type_document_pays_id',
        'original_filename',
        'storage_path',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    /**
     * Le dossier auquel appartient ce document
     */
    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    /**
     * Le type de document
     */
    public function typeDocumentPays(): BelongsTo
    {
        return $this->belongsTo(TypeDocumentPays::class, 'type_document_pays_id');
    }

    /**
     * Vérifier si le fichier est une image
     */
    public function isImage(): bool
    {
        $extension = pathinfo($this->original_filename, PATHINFO_EXTENSION);
        return in_array(strtolower($extension), ['jpg', 'jpeg', 'png']);
    }

    /**
     * Vérifier si le fichier est un PDF
     */
    public function isPdf(): bool
    {
        $extension = pathinfo($this->original_filename, PATHINFO_EXTENSION);
        return strtolower($extension) === 'pdf';
    }
}
