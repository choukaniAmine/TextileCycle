<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Avis extends Model
{
    use HasFactory;

    protected $table = 'avis';

    protected $fillable = ['atelier_id', 'user_id', 'note', 'commentaire', 'is_visible'];

    protected function casts(): array
    {
        return [
            'note'       => 'integer',
            'is_visible' => 'boolean',
        ];
    }

    public function atelier(): BelongsTo
    {
        return $this->belongsTo(Atelier::class);
    }

    public function auteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Étoiles pleines / demi / vides pour l'affichage */
    public function starsArray(): array
    {
        return array_map(fn($i) => $i <= $this->note ? 'full' : 'empty', range(1, 5));
    }
}
