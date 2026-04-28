<?php

namespace Database\Seeders;

use App\Models\Bayi;
use App\Models\Store;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::where('slug', 'golden-horse')->firstOrFail();
        $canakkaleBayi = Bayi::where('tenant_id', $tenant->id)->where('slug', 'canakkale-merkez')->firstOrFail();
        $istanbulBayi  = Bayi::where('tenant_id', $tenant->id)->where('slug', 'istanbul')->firstOrFail();

        $stores = [
            ['bayi_id' => $canakkaleBayi->id, 'slug' => 's-troy-park',         'name' => 'TROY PARK',          'address' => 'Barbaros Mah. Troya Cad. No:2, Troy Park AVM Zemin Kat No: 5', 'city' => 'Çanakkale', 'district' => 'Merkez',  'phone' => '+90 286 555 01 01', 'opening_time' => '07:30', 'closing_time' => '23:00', 'latitude' => 40.1394, 'longitude' => 26.4088, 'tags' => ['drive-thru','free-wifi'],     'coming_soon' => true],
            ['bayi_id' => $canakkaleBayi->id, 'slug' => 's-17-burda',          'name' => '17 BURDA AVM',       'address' => 'Barbaros Mah. Atatürk Cad. No:207/29',                          'city' => 'Çanakkale', 'district' => 'Merkez',  'phone' => '+90 286 555 01 02', 'opening_time' => '08:30', 'closing_time' => '22:30', 'latitude' => 40.1420, 'longitude' => 26.4135, 'tags' => ['free-wifi'],                  'coming_soon' => true],
            ['bayi_id' => $canakkaleBayi->id, 'slug' => 's-canakkale-kordon',  'name' => 'ÇANAKKALE KORDON',   'address' => 'Cevatpaşa Mah. Kayserili Ahmet Paşa Cad. Akol',                'city' => 'Çanakkale', 'district' => 'Merkez',  'phone' => '+90 286 555 01 03', 'opening_time' => '08:00', 'closing_time' => '23:00', 'latitude' => 40.1548, 'longitude' => 26.4029, 'tags' => ['deniz-manzarasi','free-wifi'],'coming_soon' => true],
            ['bayi_id' => $istanbulBayi->id,  'slug' => 's-istanbul-kadikoy',  'name' => 'KADIKÖY MODA',       'address' => 'Caferağa Mah. Moda Cad. No:45',                                  'city' => 'İstanbul',  'district' => 'Kadıköy', 'phone' => '+90 216 555 01 04', 'opening_time' => '07:00', 'closing_time' => '23:30', 'latitude' => 40.9849, 'longitude' => 29.0280, 'tags' => ['free-wifi'],                  'coming_soon' => false],
            ['bayi_id' => $istanbulBayi->id,  'slug' => 's-istanbul-taksim',   'name' => 'TAKSIM MEYDAN',      'address' => 'Hüseyinağa Mah. İstiklal Cad. No:115',                          'city' => 'İstanbul',  'district' => 'Beyoğlu', 'phone' => '+90 212 555 01 05', 'opening_time' => '07:00', 'closing_time' => '00:00', 'latitude' => 41.0369, 'longitude' => 28.9850, 'tags' => ['24-saat-yakin'],              'coming_soon' => false],
            ['bayi_id' => $istanbulBayi->id,  'slug' => 's-istanbul-levent',   'name' => 'LEVENT METRO',       'address' => 'Esentepe Mah. Büyükdere Cad. No:185',                            'city' => 'İstanbul',  'district' => 'Şişli',   'phone' => '+90 212 555 01 06', 'opening_time' => '07:00', 'closing_time' => '22:00', 'latitude' => 41.0820, 'longitude' => 29.0170, 'tags' => ['ofis-bolgesi','free-wifi'],   'coming_soon' => false],
        ];

        foreach ($stores as $s) {
            $row = $s + ['tenant_id' => $tenant->id];
            Store::updateOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => $s['slug']],
                $row
            );
        }

        // Demo kasiyer (Troy Park subesi)
        $troyPark = Store::where('tenant_id', $tenant->id)->where('slug', 's-troy-park')->first();
        if ($troyPark) {
            User::updateOrCreate(
                ['email' => 'kasa@goldenhorse.coffee'],
                [
                    'name'      => 'Troy Park Kasiyeri',
                    'phone'     => '+905000000030',
                    'password'  => Hash::make('kasa1234'),
                    'role'      => User::ROLE_CASHIER,
                    'tenant_id' => $tenant->id,
                    'bayi_id'   => $canakkaleBayi->id,
                    'store_id'  => $troyPark->id,
                    'tier'      => 'gold',
                    'join_date' => now(),
                ]
            );
        }
    }
}
