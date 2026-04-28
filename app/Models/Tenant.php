<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    protected $fillable = [
        'slug', 'name', 'legal_name', 'logo',
        'primary_color', 'contact_email', 'contact_phone',
        'is_active', 'owner_id',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function owner(): BelongsTo            { return $this->belongsTo(User::class, 'owner_id'); }
    public function bayis(): HasMany              { return $this->hasMany(Bayi::class); }
    public function stores(): HasMany             { return $this->hasMany(Store::class); }
    public function categories(): HasMany         { return $this->hasMany(Category::class); }
    public function products(): HasMany           { return $this->hasMany(Product::class); }
    public function campaigns(): HasMany          { return $this->hasMany(Campaign::class); }
    public function badges(): HasMany             { return $this->hasMany(Badge::class); }
    public function orders(): HasMany             { return $this->hasMany(Order::class); }
    public function pointTransactions(): HasMany  { return $this->hasMany(PointTransaction::class); }
    public function customers(): HasMany          { return $this->hasMany(CustomerTenantStat::class); }
    public function staff(): HasMany              { return $this->hasMany(User::class)->whereIn('role', [User::ROLE_TENANT_OWNER, User::ROLE_BAYI_OWNER, User::ROLE_CASHIER]); }
}
