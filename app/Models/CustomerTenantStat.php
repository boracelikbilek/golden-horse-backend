<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class CustomerTenantStat extends Model
{
    protected $table = 'customer_tenant_stats';

    protected $fillable = [
        'user_id', 'tenant_id', 'tier', 'stars', 'star_target',
        'reward_drinks_available', 'lifetime_orders', 'lifetime_spent',
        'last_order_at', 'favorite_product_id',
    ];

    protected $casts = [
        'stars'                   => 'integer',
        'star_target'             => 'integer',
        'reward_drinks_available' => 'integer',
        'lifetime_orders'         => 'integer',
        'lifetime_spent'          => 'decimal:2',
        'last_order_at'           => 'datetime',
    ];

    public function user(): BelongsTo            { return $this->belongsTo(User::class); }
    public function tenant(): BelongsTo          { return $this->belongsTo(Tenant::class); }
    public function favoriteProduct(): BelongsTo { return $this->belongsTo(Product::class, 'favorite_product_id'); }

    public function awardStars(int $points, ?Order $order = null, ?string $reason = null): PointTransaction
    {
        return DB::transaction(function () use ($points, $order, $reason) {
            // Satiri kilitle ve guncel degerleri oku -> balance_after race-free
            /** @var self $locked */
            $locked = self::whereKey($this->id)->lockForUpdate()->first();

            $newBalance = (int) $locked->stars + $points;
            $payload = ['stars' => $newBalance];
            if ($newBalance >= $locked->star_target && $locked->tier === 'green') {
                $payload['tier'] = 'gold';
            }
            $locked->update($payload);

            // Disardaki bu instance'in da senkron kalmasi icin
            $this->fill($payload)->syncOriginal();

            return PointTransaction::create([
                'tenant_id'     => $locked->tenant_id,
                'user_id'       => $locked->user_id,
                'order_id'      => $order?->id,
                'type'          => 'earn',
                'points'        => $points,
                'balance_after' => $newBalance,
                'reason'        => $reason ?? ($order ? "Sipariş #{$order->id}" : null),
            ]);
        });
    }
}
