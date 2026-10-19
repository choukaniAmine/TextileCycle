<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Devis extends Model
{
    use HasFactory;

    protected $table = 'devis';

    protected $fillable = [
        'reference',
        'atelier_id',
        'user_id',
        'nom_client',
        'email_client',
        'telephone_client',
        'remarques',
        'montant_total',
        'co2_economise',
        'eau_economisee',
        'duree_estimee_jours',
        'statut',
    ];

    protected $casts = [
        'montant_total'       => 'decimal:2',
        'co2_economise'       => 'decimal:2',
        'eau_economisee'      => 'integer',
        'duree_estimee_jours' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($devis) {
            if (empty($devis->reference)) {
                $count = static::whereYear('created_at', now()->year)->count() + 1;
                $devis->reference = sprintf('DEV-%d-%04d', now()->year, $count);
            }
        });
    }

    public function atelier(): BelongsTo
    {
        return $this->belongsTo(Atelier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(DevisItem::class);
    }
}
