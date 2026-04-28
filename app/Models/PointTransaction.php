<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointTransaction extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'user_id', 'order_id', 'type', 'points', 'balance_after', 'reason'];

    protected $casts = [
        'points'        => 'integer',
        'balance_after' => 'integer',
    ];

    public function user(): BelongsTo  { return $this->belongsTo(User::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
}
