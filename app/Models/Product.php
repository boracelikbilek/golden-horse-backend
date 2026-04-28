<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'slug', 'category_id', 'name', 'description', 'price', 'currency',
        'image', 'is_new', 'is_recommended', 'is_active', 'calories', 'tags',
        'star_reward',
    ];

    protected $casts = [
        'price'          => 'decimal:2',
        'is_new'         => 'boolean',
        'is_recommended' => 'boolean',
        'is_active'      => 'boolean',
        'calories'       => 'integer',
        'tags'           => 'array',
        'star_reward'    => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function modifiers(): HasMany
    {
        return $this->hasMany(ProductModifier::class)->orderBy('order');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
