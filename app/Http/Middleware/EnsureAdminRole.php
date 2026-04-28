<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        if (! $user || ! $user->isAdminLike()) {
            return redirect()->route('admin.login');
        }

        if (! empty($roles) && ! in_array($user->role, $roles, true)) {
            abort(403, 'Bu sayfaya erişim yetkin yok.');
        }

        return $next($request);
    }
}
