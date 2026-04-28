<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bayi extends Model
{
    protected $table = 'bayis';

    protected $fillable = [
        'tenant_id', 'owner_id', 'slug', 'name',
        'contact_email', 'contact_phone', 'city', 'district', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function tenant(): BelongsTo  { return $this->belongsTo(Tenant::class); }
    public function owner(): BelongsTo   { return $this->belongsTo(User::class, 'owner_id'); }
    public function stores(): HasMany    { return $this->hasMany(Store::class); }
    public function orders(): HasMany    { return $this->hasMany(Order::class); }
    public function cashiers(): HasMany  { return $this->hasMany(User::class)->where('role', User::ROLE_CASHIER); }
}
