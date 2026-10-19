<?php

namespace App\Models;

use App\Enums\DonStatus;
use Database\Factories\DonFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Don extends Model
{
    /** @use HasFactory<DonFactory> */
    use HasFactory;

    protected $fillable = [
        'association_id', 'user_id', 'description', 'quantity', 'status', 'donated_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => DonStatus::class,
            'donated_at' => 'date',
            'quantity' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Don $don) {
            $don->reference ??= 'DON-'.now()->format('Ymd').'-'.Str::upper(Str::random(5));
            $don->status ??= DonStatus::EnAttente;
        });

        // Suivi de l'état : chaque création / changement de statut est journalisé.
        static::created(fn (Don $don) => $don->logStatus());
        static::updated(function (Don $don) {
            if ($don->wasChanged('status')) {
                $don->logStatus();
            }
        });
    }

    /** Don N ─── 1 Association */
    public function association(): BelongsTo
    {
        return $this->belongsTo(Association::class);
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(DonStatusLog::class)->oldest('id');
    }

    public function logStatus(): void
    {
        $this->statusLogs()->create(['status' => $this->status, 'user_id' => auth()->id()]);
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        return $query->when($status && DonStatus::tryFrom($status), fn ($q) => $q->where('status', $status));
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, fn ($q) => $q->where(function ($q) use ($term) {
            $q->where('reference', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        }));
    }
}
