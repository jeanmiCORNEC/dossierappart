<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DossierLog extends Model
{
     protected $fillable = [
        'dossier_id', 'action_type', 'ip_address', 'user_agent', 'details'
    ];

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }
}
