<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class QrSession extends Model
{
    protected $fillable = ['user_id', 'token', 'expires_at', 'used_at', 'used_by_cashier_id'];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at'    => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function rotate(User $user, int $ttlSeconds = 60): self
    {
        return self::create([
            'user_id'    => $user->id,
            'token'      => 'GH-'.Str::random(40),
            'expires_at' => now()->addSeconds($ttlSeconds),
        ]);
    }

    public function isValid(): bool
    {
        return $this->used_at === null && $this->expires_at->isFuture();
    }
}
