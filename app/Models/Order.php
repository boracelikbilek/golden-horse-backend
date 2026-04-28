<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'bayi_id', 'store_id', 'user_id', 'cashier_id',
        'status', 'subtotal', 'total', 'currency', 'stars_earned',
        'note', 'placed_at',
    ];

    protected $casts = [
        'subtotal'     => 'decimal:2',
        'total'        => 'decimal:2',
        'stars_earned' => 'integer',
        'placed_at'    => 'datetime',
    ];

    public function user(): BelongsTo     { return $this->belongsTo(User::class); }
    public function cashier(): BelongsTo  { return $this->belongsTo(User::class, 'cashier_id'); }
    public function bayi(): BelongsTo     { return $this->belongsTo(Bayi::class); }
    public function store(): BelongsTo    { return $this->belongsTo(Store::class); }
    public function items(): HasMany      { return $this->hasMany(OrderItem::class); }
    public function pointTransaction(): HasOne { return $this->hasOne(PointTransaction::class); }
}
