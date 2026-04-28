<?php

namespace Database\Seeders;

use App\Models\Bayi;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        // Superadmin (platform sahibi) — tenant'a bagli degil
        $superadmin = User::updateOrCreate(
            ['email' => 'super@goldenhorse.coffee'],
            [
                'name'      => 'Super Admin',
                'phone'     => '+905000000000',
                'password'  => Hash::make('super1234'),
                'role'      => User::ROLE_SUPERADMIN,
                'tier'      => 'gold',
                'join_date' => now(),
            ]
        );

        // Tenant: Golden Horse
        $ghOwner = User::updateOrCreate(
            ['email' => 'sahip@goldenhorse.coffee'],
            [
                'name'      => 'Golden Horse Sahibi',
                'phone'     => '+905000000010',
                'password'  => Hash::make('sahip1234'),
                'role'      => User::ROLE_TENANT_OWNER,
                'tier'      => 'gold',
                'join_date' => now(),
            ]
        );

        $tenant = Tenant::updateOrCreate(
            ['slug' => 'golden-horse'],
            [
                'name'          => 'Golden Horse Coffee',
                'legal_name'    => 'Golden Horse Kahve Tic. Ltd. Şti.',
                'primary_color' => '#CDA863',
                'contact_email' => 'info@goldenhorse.coffee',
                'contact_phone' => '+90 286 555 00 00',
                'is_active'     => true,
                'owner_id'      => $ghOwner->id,
            ]
        );

        $ghOwner->update(['tenant_id' => $tenant->id]);

        // Bayiler
        $canakkaleOwner = User::updateOrCreate(
            ['email' => 'canakkale@goldenhorse.coffee'],
            [
                'name'      => 'Çanakkale Bayi Sahibi',
                'phone'     => '+905000000020',
                'password'  => Hash::make('bayi1234'),
                'role'      => User::ROLE_BAYI_OWNER,
                'tenant_id' => $tenant->id,
                'tier'      => 'gold',
                'join_date' => now(),
            ]
        );

        $canakkaleBayi = Bayi::updateOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'canakkale-merkez'],
            [
                'name'          => 'Çanakkale Merkez Bayi',
                'owner_id'      => $canakkaleOwner->id,
                'contact_email' => 'canakkale@goldenhorse.coffee',
                'contact_phone' => '+90 286 555 00 10',
                'city'          => 'Çanakkale',
                'district'      => 'Merkez',
                'is_active'     => true,
            ]
        );

        $canakkaleOwner->update(['bayi_id' => $canakkaleBayi->id]);

        $istanbulOwner = User::updateOrCreate(
            ['email' => 'istanbul@goldenhorse.coffee'],
            [
                'name'      => 'İstanbul Bayi Sahibi',
                'phone'     => '+905000000021',
                'password'  => Hash::make('bayi1234'),
                'role'      => User::ROLE_BAYI_OWNER,
                'tenant_id' => $tenant->id,
                'tier'      => 'gold',
                'join_date' => now(),
            ]
        );

        $istanbulBayi = Bayi::updateOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'istanbul'],
            [
                'name'          => 'İstanbul Bayi',
                'owner_id'      => $istanbulOwner->id,
                'contact_email' => 'istanbul@goldenhorse.coffee',
                'contact_phone' => '+90 212 555 00 20',
                'city'          => 'İstanbul',
                'is_active'     => true,
            ]
        );

        $istanbulOwner->update(['bayi_id' => $istanbulBayi->id]);
    }
}
