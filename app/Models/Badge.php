<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'slug', 'name', 'description', 'icon', 'stars_required'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('earned_at');
    }
}
