<?php

namespace App\Models;

use App\Enums\Statut;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Intervention extends Model
{
    use HasFactory;

    protected $fillable = [
        'atelier_id', 'titre', 'description', 'statut', 'cout_estime', 'date_debut', 'date_fin',
    ];

    protected function casts(): array
    {
        return [
            'statut' => Statut::class,
            'cout_estime' => 'decimal:2',
            'date_debut' => 'date',
            'date_fin' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (Intervention $i) {
            $demande = $i->demande;
            if (! $demande) {
                return;
            }

            // Notifications automatiques vers le client
            if ($i->wasRecentlyCreated) {
                $demande->prevenirClient("Nouvelle intervention prévue : « {$i->titre} »", '🪡');
            } elseif ($i->wasChanged('statut')) {
                $demande->prevenirClient(
                    "Intervention « {$i->titre} » : {$i->statut->label()}",
                    $i->statut === Statut::Terminee ? '✅' : '✂️'
                );
            }

            $demande->syncStatut();
        });

        static::deleted(fn (Intervention $i) => $i->demande?->syncStatut());
    }

    public function demande(): BelongsTo
    {
        return $this->belongsTo(Demande::class);
    }

    public function atelier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'atelier_id');
    }

    /** Fait avancer d'un cran (En attente → En cours → Terminée) et remplit les dates. */
    public function avancer(): bool
    {
        $next = $this->statut->next();
        if (! $next) {
            return false;
        }

        $data = ['statut' => $next, 'date_debut' => $this->date_debut ?? today()];
        if ($next === Statut::Terminee) {
            $data['date_fin'] = today();
        }
        $this->update($data);

        return true;
    }
}
