<?php

namespace App\Models;

use App\Enums\Statut;
use App\Enums\TypeDemande;
use App\Notifications\DemandeNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Demande extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'atelier_id', 'titre', 'type', 'vetement', 'description',
        'photo', 'urgent', 'date_souhaitee', 'statut',
    ];

    protected function casts(): array
    {
        return [
            'type' => TypeDemande::class,
            'statut' => Statut::class,
            'urgent' => 'boolean',
            'date_souhaitee' => 'date',
        ];
    }

    // ----- Relations -----
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function atelier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'atelier_id');
    }

    public function interventions(): HasMany
    {
        return $this->hasMany(Intervention::class);
    }

    // ----- Scopes -----
    /** Demandes que n'importe quel atelier peut encore prendre en charge. */
    public function scopeDisponibles(Builder $query): Builder
    {
        return $query->whereNull('atelier_id')->where('statut', Statut::EnAttente->value);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, fn ($q) => $q->where(function ($q) use ($term) {
            $q->where('titre', 'like', "%{$term}%")
              ->orWhere('vetement', 'like', "%{$term}%")
              ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$term}%"));
        }));
    }

    // ----- Règles métier -----
    public function peutEtreModifiee(): bool
    {
        return $this->statut === Statut::EnAttente;
    }

    public function peutEtreSupprimee(): bool
    {
        return in_array($this->statut, [Statut::EnAttente, Statut::Annulee], true);
    }

    public function estAssigneA(User $user): bool
    {
        return $this->atelier_id !== null && (int) $this->atelier_id === (int) $user->id;
    }

    /** Date souhaitée dépassée alors que le travail n'est pas fini. */
    public function enRetard(): bool
    {
        return $this->date_souhaitee !== null
            && ! in_array($this->statut, [Statut::Terminee, Statut::Annulee], true)
            && $this->date_souhaitee->copy()->endOfDay()->isPast();
    }

    public function progression(): int
    {
        if ($this->statut === Statut::Terminee) {
            return 100;
        }
        $total = $this->interventions->count();

        return $total === 0 ? 0 : (int) round(
            $this->interventions->filter(fn ($i) => $i->statut === Statut::Terminee)->count() / $total * 100
        );
    }

    public function coutTotal(): float
    {
        return (float) $this->interventions->sum('cout_estime');
    }

    // ----- Actions de l'atelier -----

    /**
     * Prise en charge "atomique" : un seul atelier peut gagner, même si deux cliquent en même temps
     * (l'UPDATE ne passe que si la demande est encore libre).
     */
    public function prendreEnCharge(User $atelier): bool
    {
        $gagne = static::whereKey($this->id)->whereNull('atelier_id')
            ->where('statut', Statut::EnAttente->value)
            ->update(['atelier_id' => $atelier->id, 'statut' => Statut::EnCours->value]) === 1;

        if ($gagne) {
            $this->refresh();
            $nom = $atelier->organization ?? $atelier->name;
            $this->prevenirClient("{$nom} a pris en charge votre demande", '🏠');
        }

        return $gagne;
    }

    /** L'atelier se désiste : possible seulement si aucune intervention n'a démarré. */
    public function liberer(): bool
    {
        if ($this->interventions()->where('statut', '!=', Statut::EnAttente->value)->exists()) {
            return false;
        }

        $this->interventions()->delete();
        $this->update(['atelier_id' => null, 'statut' => Statut::EnAttente]);
        $this->prevenirClient('Votre demande est de nouveau disponible pour les ateliers', '🔄');

        return true;
    }

    /** Le statut suit les interventions ; une demande prise en charge est au minimum "En cours". */
    public function syncStatut(): void
    {
        if ($this->statut === Statut::Annulee) {
            return;
        }

        $items = $this->interventions()->get();

        $nouveau = match (true) {
            $items->isNotEmpty() && $items->every(fn ($i) => $i->statut === Statut::Terminee) => Statut::Terminee,
            $this->atelier_id !== null || $items->contains(fn ($i) => $i->statut !== Statut::EnAttente) => Statut::EnCours,
            default => Statut::EnAttente,
        };

        if ($nouveau !== $this->statut) {
            $this->update(['statut' => $nouveau]);

            if ($nouveau === Statut::Terminee) {
                $this->prevenirClient('Bonne nouvelle : votre vêtement est prêt ! 🎉', '🎉');
            }
        }
        $this->unsetRelation('interventions');
    }

    public function prevenirClient(string $message, string $icon = '🧵'): void
    {
        $this->user?->notify(new DemandeNotification($this, $message, $icon));
    }
}
