<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QrSession;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WalletController extends Controller
{
    public function show()
    {
        return view('admin.wallet');
    }

    /**
     * Kasiyer musteriye TL yukler.
     * Musteriyi QR token, telefon veya email ile cozer.
     */
    public function topup(Request $request)
    {
        $cashier = $request->user();
        $tenantId = $cashier->tenant_id;

        $data = $request->validate([
            'identifier' => ['required', 'string'],
            'method'     => ['required', Rule::in(['qr', 'phone', 'email'])],
            'amount'     => ['required', 'numeric', 'min:1', 'max:10000'],
            'note'       => ['nullable', 'string', 'max:500'],
        ]);

        $customer = $this->resolveCustomer($data['method'], $data['identifier']);
        if (! $customer) {
            return back()->withErrors(['identifier' => 'Müşteri bulunamadı.'])->withInput();
        }

        if ($customer->role !== User::ROLE_CUSTOMER) {
            return back()->withErrors(['identifier' => 'Yalnızca müşteri hesaplarına yükleme yapılabilir.'])->withInput();
        }

        $tx = WalletTransaction::record([
            'tenant_id'  => $tenantId,
            'user_id'    => $customer->id,
            'bayi_id'    => $cashier->bayi_id,
            'store_id'   => $cashier->store_id,
            'cashier_id' => $cashier->id,
            'type'       => WalletTransaction::TYPE_TOPUP,
            'amount'     => (float) $data['amount'],
            'reason'     => $data['note'] ?? 'Kasiyer TL yüklemesi',
        ]);

        return redirect()->route('admin.wallet')
            ->with('success', sprintf(
                '%s hesabına %.2f TL yüklendi. Yeni bakiye: %.2f TL',
                $customer->name,
                (float) $data['amount'],
                (float) $tx->balance_after,
            ));
    }

    private function resolveCustomer(string $method, string $identifier): ?User
    {
        return match ($method) {
            'qr'    => optional(QrSession::where('token', $identifier)->first())->user,
            'phone' => User::where('phone', $identifier)->first(),
            'email' => User::where('email', $identifier)->first(),
        };
    }
}
