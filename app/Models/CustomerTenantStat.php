<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        $this->increment('stars', $points);

        if ($this->stars >= $this->star_target && $this->tier === 'green') {
            $this->update(['tier' => 'gold']);
        }

        return PointTransaction::create([
            'tenant_id'     => $this->tenant_id,
            'user_id'       => $this->user_id,
            'order_id'      => $order?->id,
            'type'          => 'earn',
            'points'        => $points,
            'balance_after' => $this->fresh()->stars,
            'reason'        => $reason ?? ($order ? "Sipariş #{$order->id}" : null),
        ]);
    }
}
