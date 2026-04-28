<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            TenantSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            StoreSeeder::class,
            CampaignSeeder::class,
            BadgeSeeder::class,
            DemoCustomerSeeder::class,
        ]);
    }
}
