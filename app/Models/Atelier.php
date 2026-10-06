<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Atelier extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'adresse',
        'ville',
        'code_postal',
        'telephone',
        'email',
        'image',
        'horaires',
        'est_actif',
    ];

    protected $casts = [
        'est_actif' => 'boolean',
    ];

    /**
     * Un atelier propose plusieurs services.
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Scope pour filtrer les ateliers actifs.
     */
    public function scopeActif($query)
    {
        return $query->where('est_actif', true);
    }
}
