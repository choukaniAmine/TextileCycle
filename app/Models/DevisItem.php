<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevisItem extends Model
{
    use HasFactory;

    protected $table = 'devis_items';

    protected $fillable = [
        'devis_id',
        'service_id',
        'nom_service',
        'type_service',
        'prix_unitaire',
        'quantite',
        'sous_total',
    ];

    protected $casts = [
        'prix_unitaire' => 'decimal:2',
        'quantite'      => 'integer',
        'sous_total'    => 'decimal:2',
    ];

    public function devis(): BelongsTo
    {
        return $this->belongsTo(Devis::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
