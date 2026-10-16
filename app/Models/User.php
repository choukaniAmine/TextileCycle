<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, TwoFactorAuthenticatable;

    protected $fillable = [
        'name', 'email', 'avatar', 'password', 'role', 'phone', 'city', 'organization', 'bio', 'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
                    return Storage::disk('public')->url($this->avatar);
                }

                return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=4F46E5&background=EEF2FF';
            }
        );
    }
public function demandes(): HasMany
{
    return $this->hasMany(Demande::class);
} 

    /** Association gérée par ce compte (rôle « association »). */
    public function managedAssociation(): HasOne
    {
        return $this->hasOne(Association::class, 'manager_id');
    }

    /** Dons effectués par cet utilisateur. */
    public function dons(): HasMany
    {
        return $this->hasMany(Don::class);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin') || $this->role === UserRole::Admin;
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, fn ($q) => $q->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('city', 'like', "%{$term}%");
        }));
    }
}
