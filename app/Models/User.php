<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_SUPERADMIN   = 'superadmin';
    public const ROLE_TENANT_OWNER = 'tenant_owner';
    public const ROLE_BAYI_OWNER   = 'bayi_owner';
    public const ROLE_CASHIER      = 'cashier';
    public const ROLE_CUSTOMER     = 'customer';

    protected $fillable = [
        'name', 'email', 'phone', 'password',
        'role', 'tenant_id', 'bayi_id', 'store_id',
        'tier', 'stars', 'star_target', 'reward_drinks_available',
        'card_balance', 'currency', 'join_date', 'avatar', 'notifications',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password'          => 'hashed',
            'card_balance'      => 'decimal:2',
            'stars'             => 'integer',
            'star_target'       => 'integer',
            'reward_drinks_available' => 'integer',
            'join_date'         => 'date',
            'notifications'     => 'array',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function bayi(): BelongsTo
    {
        return $this->belongsTo(Bayi::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class);
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class)->withPivot('earned_at');
    }

    public function qrSessions(): HasMany
    {
        return $this->hasMany(QrSession::class);
    }

    public function tenantStats(): HasMany
    {
        return $this->hasMany(CustomerTenantStat::class);
    }

    public function statsFor(Tenant|int $tenant): CustomerTenantStat
    {
        $tenantId = is_int($tenant) ? $tenant : $tenant->id;
        return CustomerTenantStat::firstOrCreate(
            ['user_id' => $this->id, 'tenant_id' => $tenantId],
            ['tier' => 'green', 'stars' => 0, 'star_target' => 150]
        );
    }

    public function isSuperadmin(): bool   { return $this->role === self::ROLE_SUPERADMIN; }
    public function isTenantOwner(): bool  { return $this->role === self::ROLE_TENANT_OWNER; }
    public function isBayiOwner(): bool    { return $this->role === self::ROLE_BAYI_OWNER; }
    public function isCashier(): bool      { return $this->role === self::ROLE_CASHIER; }
    public function isCustomer(): bool     { return $this->role === self::ROLE_CUSTOMER; }
    public function isAdminLike(): bool    { return in_array($this->role, [self::ROLE_SUPERADMIN, self::ROLE_TENANT_OWNER, self::ROLE_BAYI_OWNER, self::ROLE_CASHIER]); }
}
