<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductModifierOption extends Model
{
    protected $fillable = ['product_modifier_id', 'slug', 'name', 'price_delta', 'is_default', 'order'];

    protected $casts = [
        'price_delta' => 'decimal:2',
        'is_default'  => 'boolean',
    ];

    public function modifier(): BelongsTo
    {
        return $this->belongsTo(ProductModifier::class, 'product_modifier_id');
    }
}
