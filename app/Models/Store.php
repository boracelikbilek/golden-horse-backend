<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'bayi_id', 'slug', 'name', 'address', 'city', 'district',
        'phone', 'opening_time', 'closing_time', 'latitude', 'longitude',
        'tags', 'coming_soon', 'is_active',
    ];

    protected $casts = [
        'tags'        => 'array',
        'coming_soon' => 'boolean',
        'is_active'   => 'boolean',
        'latitude'    => 'float',
        'longitude'   => 'float',
    ];

    public function bayi(): BelongsTo  { return $this->belongsTo(Bayi::class); }
    public function orders(): HasMany  { return $this->hasMany(Order::class); }
}
