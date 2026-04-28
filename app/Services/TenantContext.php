<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantContext
{
    public function resolve(Request $request): Tenant
    {
        $slug = $request->header('X-Tenant');

        if (! $slug) {
            $user = $request->user();
            if ($user && $user->tenant_id) {
                return Tenant::findOrFail($user->tenant_id);
            }
            $slug = config('goldenhorse.app.default_tenant', 'golden-horse');
        }

        return Tenant::where('slug', $slug)->firstOrFail();
    }
}
