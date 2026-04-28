<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QrSession;
use Illuminate\Http\Request;

class QrController extends Controller
{
    public function rotate(Request $request)
    {
        $user = $request->user();

        // Eskimis sessionlari iptal et (sadece son 24 saat icin temizle)
        QrSession::where('user_id', $user->id)
            ->whereNull('used_at')
            ->where('expires_at', '<', now()->subDay())
            ->delete();

        $session = QrSession::rotate($user, ttlSeconds: 60);

        return response()->json([
            'token'     => $session->token,
            'expiresAt' => $session->expires_at->toIso8601String(),
            'ttl'       => 60,
        ]);
    }
}
