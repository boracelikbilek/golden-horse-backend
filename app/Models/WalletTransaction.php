<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class WalletTransaction extends Model
{
    use BelongsToTenant;

    public const TYPE_TOPUP    = 'topup';
    public const TYPE_PURCHASE = 'purchase';
    public const TYPE_REFUND   = 'refund';
    public const TYPE_ADJUST   = 'adjust';

    protected $fillable = [
        'tenant_id', 'user_id', 'bayi_id', 'store_id', 'order_id', 'cashier_id',
        'type', 'amount', 'balance_after', 'currency', 'reason', 'meta',
    ];

    protected $casts = [
        'amount'        => 'decimal:2',
        'balance_after' => 'decimal:2',
        'meta'          => 'array',
    ];

    public function user(): BelongsTo    { return $this->belongsTo(User::class); }
    public function cashier(): BelongsTo { return $this->belongsTo(User::class, 'cashier_id'); }
    public function bayi(): BelongsTo    { return $this->belongsTo(Bayi::class); }
    public function store(): BelongsTo   { return $this->belongsTo(Store::class); }
    public function order(): BelongsTo   { return $this->belongsTo(Order::class); }

    /**
     * Atomik olarak users.card_balance'i degistirir ve ledger satiri olusturur.
     * $amount: + credit (topup/refund), - debit (purchase/adjust-)
     */
    public static function record(array $payload): self
    {
        return DB::transaction(function () use ($payload) {
            $user = User::lockForUpdate()->findOrFail($payload['user_id']);
            $newBalance = (float) $user->card_balance + (float) $payload['amount'];
            $user->update(['card_balance' => $newBalance]);

            return self::create(array_merge($payload, [
                'balance_after' => $newBalance,
                'currency'      => $payload['currency'] ?? 'TRY',
            ]));
        });
    }
}
