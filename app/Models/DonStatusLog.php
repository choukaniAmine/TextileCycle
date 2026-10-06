<?php

namespace App\Models;

use App\Enums\DonStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonStatusLog extends Model
{
    protected $fillable = ['don_id', 'user_id', 'status'];

    protected function casts(): array
    {
        return ['status' => DonStatus::class];
    }

    public function don(): BelongsTo
    {
        return $this->belongsTo(Don::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
