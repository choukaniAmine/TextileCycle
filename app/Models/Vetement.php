<?php

namespace App\Models;

use App\Enums\EtatVetement;
use App\Enums\TypeVetement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
  use App\Enums\StatutVetement;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Vetement extends Model
{
  

protected $fillable = [
    'nom', 'description', 'taille', 'etat', 'type', 'statut',
    'image', 'categorie_id', 'user_id',
];

protected function casts(): array
{
    return [
        'etat' => EtatVetement::class,
        'type' => TypeVetement::class,
        'statut' => StatutVetement::class,
    ];
}

public function demandes(): HasMany
{
    return $this->hasMany(DemandeDon::class);
}

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function imageUrl(): ?string
    {
        return $this->image ? Storage::url($this->image) : null;
    }
}