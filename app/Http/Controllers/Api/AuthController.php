<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request, TenantContext $tenants)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'min:2', 'max:120'],
            'email'    => ['required', 'email', 'max:191', Rule::unique('users', 'email')],
            'phone'    => ['required', 'string', 'min:8', 'max:32', Rule::unique('users', 'phone')],
            'password' => ['required', 'string', 'min:6', 'max:191'],
        ]);

        $tenant = $tenants->resolve($request);

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'phone'     => $data['phone'],
            'password'  => Hash::make($data['password']),
            'role'      => User::ROLE_CUSTOMER,
            'tier'      => 'green',
            'join_date' => now(),
        ]);

        $stats = $user->statsFor($tenant);

        return response()->json([
            'token' => $user->createToken('mobile')->plainTextToken,
            'user'  => $this->shapeUser($user, $stats),
        ], 201);
    }

    public function login(Request $request, TenantContext $tenants)
    {
        $data = $request->validate([
            'identifier' => ['required', 'string'],
            'password'   => ['required', 'string'],
        ]);

        $user = User::where('email', $data['identifier'])
            ->orWhere('phone', $data['identifier'])
            ->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'E-posta/telefon veya şifre hatalı.'], 422);
        }

        $tenant = $tenants->resolve($request);
        $stats  = $user->statsFor($tenant);

        return response()->json([
            'token' => $user->createToken('mobile')->plainTextToken,
            'user'  => $this->shapeUser($user, $stats),
        ]);
    }

    public function me(Request $request, TenantContext $tenants)
    {
        $user   = $request->user();
        $tenant = $tenants->resolve($request);
        $stats  = $user->statsFor($tenant);

        return response()->json($this->shapeUser($user, $stats));
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->noContent();
    }

    private function shapeUser(User $user, $stats): array
    {
        return [
            'id'                       => 'u-'.$user->id,
            'name'                     => $user->name,
            'email'                    => $user->email,
            'phone'                    => $user->phone,
            'role'                     => $user->role,
            'tier'                     => $stats->tier,
            'stars'                    => $stats->stars,
            'starTarget'               => $stats->star_target,
            'rewardDrinksAvailable'    => $stats->reward_drinks_available,
            'cardBalance'              => (float) $user->card_balance,
            'currency'                 => 'TL',
            'notifications'            => 0,
            'joinDate'                 => optional($user->join_date)->toDateString(),
        ];
    }
}
