<?php

namespace App\Models;

use App\Enums\StatutDemande;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemandeDon extends Model
{
    protected $fillable = ['vetement_id', 'demandeur_id', 'message', 'statut'];

    protected function casts(): array
    {
        return ['statut' => StatutDemande::class];
    }

    public function vetement(): BelongsTo
    {
        return $this->belongsTo(Vetement::class);
    }

    public function demandeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'demandeur_id');
    }
}