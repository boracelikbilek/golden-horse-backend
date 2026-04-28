<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::where('slug', 'golden-horse')->firstOrFail();

        $categories = [
            ['slug' => 'hot-coffee',  'name' => 'Hot Drinks',  'subtitle' => 'Sıcak kahveler & çaylar',     'image' => '☕', 'order' => 1],
            ['slug' => 'cold-drinks', 'name' => 'Cold Drinks', 'subtitle' => 'Iced latte & soğuk demler',   'image' => '🧊', 'order' => 2],
            ['slug' => 'seasonal',    'name' => 'Signature',   'subtitle' => 'İmza lezzetler',              'image' => '💗', 'order' => 3],
            ['slug' => 'food',        'name' => 'Food',        'subtitle' => 'Kahvaltı & atıştırmalık',     'image' => '🥐', 'order' => 4],
            ['slug' => 'hot-tea',     'name' => 'Ice Cream',   'subtitle' => 'Dondurma çeşitleri',          'image' => '🍨', 'order' => 5],
            ['slug' => 'merch',       'name' => 'Roasting',    'subtitle' => 'Çekirdek kahveler',           'image' => '🫘', 'order' => 6],
        ];

        foreach ($categories as $c) {
            Category::updateOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => $c['slug']],
                $c + ['tenant_id' => $tenant->id]
            );
        }
    }
}
