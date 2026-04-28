<?php

namespace App\Models\Concerns;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopeForTenant(Builder $query, Tenant|int|null $tenant): Builder
    {
        if ($tenant === null) {
            return $query;
        }
        $tenantId = is_int($tenant) ? $tenant : $tenant->id;
        return $query->where($this->getTable().'.tenant_id', $tenantId);
    }
}
