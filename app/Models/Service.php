<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'atelier_id',
        'nom',
        'type_service',
        'description',
        'tarif_estime',
        'duree_estimee',
        'disponible',
    ];

    protected $casts = [
        'tarif_estime' => 'decimal:2',
        'disponible' => 'boolean',
    ];

    /**
     * Chaque service appartient à un atelier.
     */
    public function atelier(): BelongsTo
    {
        return $this->belongsTo(Atelier::class);
    }

    /**
     * Scope pour filtrer les services disponibles.
     */
    public function scopeDisponible($query)
    {
        return $query->where('disponible', true);
    }
}
