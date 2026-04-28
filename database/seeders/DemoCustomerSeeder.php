<?php

namespace Database\Seeders;

use App\Models\CustomerTenantStat;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoCustomerSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::where('slug', 'golden-horse')->firstOrFail();

        $customer = User::updateOrCreate(
            ['email' => 'demo@goldenhorse.coffee'],
            [
                'name'      => 'Bora Demo',
                'phone'     => '+905000000100',
                'password'  => Hash::make('demo1234'),
                'role'      => User::ROLE_CUSTOMER,
                'tier'      => 'green',
                'join_date' => now()->subDays(30),
            ]
        );

        CustomerTenantStat::updateOrCreate(
            ['user_id' => $customer->id, 'tenant_id' => $tenant->id],
            [
                'tier'             => 'green',
                'stars'            => 1,
                'star_target'      => 150,
                'lifetime_orders'  => 0,
                'lifetime_spent'   => 0,
            ]
        );
    }
}
