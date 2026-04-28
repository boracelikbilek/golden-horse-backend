<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::where('slug', 'golden-horse')->firstOrFail();

        $badges = [
            ['slug' => 'b-first-sip',      'name' => 'İlk Yudum',         'description' => 'İlk siparişini verdin',           'icon' => '☕', 'stars_required' => null],
            ['slug' => 'b-golden-member',  'name' => 'Golden Üye',         'description' => 'Sadakat programına katıldın',     'icon' => '🏅', 'stars_required' => null],
            ['slug' => 'b-first-star',     'name' => 'İlk Yıldız',         'description' => 'İlk yıldızını kazandın',          'icon' => '⭐', 'stars_required' => 1],
            ['slug' => 'b-explorer',       'name' => 'Kâşif',              'description' => '3 farklı şubeden sipariş ver',    'icon' => '🧭', 'stars_required' => null],
            ['slug' => 'b-streak-7',       'name' => '7 Günlük Seri',      'description' => '7 gün üst üste sipariş ver',      'icon' => '🔥', 'stars_required' => null],
            ['slug' => 'b-platinum',       'name' => 'Platinum Üye',       'description' => '150 yıldız biriktir',             'icon' => '🐎', 'stars_required' => 150],
            ['slug' => 'b-seasonal',       'name' => 'Mevsim Tutkunu',     'description' => '5 dönemsel ürün dene',            'icon' => '🌸', 'stars_required' => null],
            ['slug' => 'b-morning-bird',   'name' => 'Sabah Kuşu',         'description' => '08:00 öncesi 10 sipariş ver',     'icon' => '🌅', 'stars_required' => null],
        ];

        foreach ($badges as $b) {
            Badge::updateOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => $b['slug']],
                $b + ['tenant_id' => $tenant->id]
            );
        }
    }
}
