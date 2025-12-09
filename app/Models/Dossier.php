<?php

namespace App\Models;

use App\Enums\DossierStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Dossier extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'dossiers';

    protected $fillable = [
        'pays_id',
        'email',
        'status',
        'download_token',
        'stripe_payment_id',
        'expires_at',
        'created_at',
        'final_pdf_path',
        'processed_at'
    ];

    protected function casts(): array
    {
        return [
            'status' => DossierStatus::class,
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Générer un UUID au lieu d'un ID auto-incrémenté
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->download_token)) {
                $model->download_token = Str::random(64);
            }
        });
    }

    /**
     * Le pays pour lequel ce dossier est créé
     */
    public function pays(): BelongsTo
    {
        return $this->belongsTo(Pays::class);
    }

    /**
     * Les documents de ce dossier
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class)->orderBy('sort_order');
    }

    /**
     * Vérifier si le dossier est expiré
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Vérifier si le dossier peut être téléchargé
     */
    public function canBeDownloaded(): bool
    {
        return $this->status === DossierStatus::COMPLETED && !$this->isExpired();
    }

    public function logs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DossierLog::class);
    }
}
