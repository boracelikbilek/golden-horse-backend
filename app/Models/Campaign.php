<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'slug', 'title', 'subtitle', 'image', 'gradient',
        'ends_at', 'reward_text', 'cta_text', 'is_active',
    ];

    protected $casts = [
        'gradient'  => 'array',
        'ends_at'   => 'datetime',
        'is_active' => 'boolean',
    ];
}
