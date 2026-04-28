<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductModifier;
use App\Models\ProductModifierOption;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::where('slug', 'golden-horse')->firstOrFail();

        $modifierTemplates = [
            'size' => [
                'name' => 'Boy', 'type' => 'single', 'order' => 1,
                'options' => [
                    ['slug' => 'tall',   'name' => 'Tall',   'price_delta' => 0,  'is_default' => true],
                    ['slug' => 'grande', 'name' => 'Grande', 'price_delta' => 15, 'is_default' => false],
                    ['slug' => 'venti',  'name' => 'Venti',  'price_delta' => 30, 'is_default' => false],
                ],
            ],
            'milk' => [
                'name' => 'Süt', 'type' => 'single', 'order' => 2,
                'options' => [
                    ['slug' => 'whole',   'name' => 'Tam yağlı süt',         'price_delta' => 0,  'is_default' => true],
                    ['slug' => 'skim',    'name' => 'Yağsız süt',            'price_delta' => 0,  'is_default' => false],
                    ['slug' => 'oat',     'name' => 'Yulaf sütü',            'price_delta' => 15, 'is_default' => false],
                    ['slug' => 'almond',  'name' => 'Badem sütü',            'price_delta' => 15, 'is_default' => false],
                    ['slug' => 'coconut', 'name' => 'Hindistan cevizi sütü', 'price_delta' => 15, 'is_default' => false],
                ],
            ],
            'extras' => [
                'name' => 'Ekstralar', 'type' => 'multi', 'order' => 3,
                'options' => [
                    ['slug' => 'shot',     'name' => '+1 Shot Espresso',  'price_delta' => 20, 'is_default' => false],
                    ['slug' => 'whip',     'name' => 'Krema',             'price_delta' => 10, 'is_default' => false],
                    ['slug' => 'vanilla',  'name' => 'Vanilya şurubu',    'price_delta' => 10, 'is_default' => false],
                    ['slug' => 'caramel',  'name' => 'Karamel şurubu',    'price_delta' => 10, 'is_default' => false],
                    ['slug' => 'hazelnut', 'name' => 'Fındık şurubu',     'price_delta' => 10, 'is_default' => false],
                ],
            ],
        ];

        $drinkMods = ['size', 'milk', 'extras'];
        $coldMods  = ['size', 'milk', 'extras'];
        $sizeOnly  = ['size'];
        $sizeExtras = ['size', 'extras'];

        $products = [
            ['slug' => 'p-pink-matcha',                   'category' => 'seasonal',    'name' => 'Pink Matcha',                  'description' => 'Pitaya ve premium matcha ile hazırlanan Golden Horse imzası.', 'price' => 185, 'image' => '💗', 'is_new' => true, 'is_recommended' => true, 'tags' => ['signature','pitaya','matcha'], 'calories' => 210, 'mods' => $drinkMods],
            ['slug' => 'p-mustang-shake',                 'category' => 'seasonal',    'name' => 'Mustang Protein Shake',        'description' => 'Yüksek proteinli, kahve bazlı shake.', 'price' => 195, 'image' => '🥤', 'is_new' => true, 'is_recommended' => true, 'tags' => ['signature','protein'], 'calories' => 320, 'mods' => $coldMods],
            ['slug' => 'p-iced-lemon-vanilla-latte',      'category' => 'cold-drinks', 'name' => 'Iced Lemon Vanilla Latte',     'description' => 'Serinletici limon aromalı, vanilyalı buzlu latte.', 'price' => 165, 'image' => '🍋', 'is_new' => true, 'tags' => ['yeni','limon','vanilya'], 'calories' => 210, 'mods' => $coldMods],
            ['slug' => 'p-iced-ube-vanilla-latte',        'category' => 'cold-drinks', 'name' => 'Iced Ube Vanilla Latte',       'description' => 'Ube (mor yam) aromalı buzlu latte.', 'price' => 175, 'image' => '💜', 'is_new' => true, 'tags' => ['yeni','ube'], 'calories' => 240, 'mods' => $coldMods],
            ['slug' => 'p-iced-ube-vanilla-macchiato',    'category' => 'cold-drinks', 'name' => 'Iced Ube Vanilla Macchiato',   'description' => 'Ube şurubu ve ekstra espresso shot ile macchiato.', 'price' => 180, 'image' => '🟣', 'is_new' => true, 'calories' => 260, 'mods' => $coldMods],
            ['slug' => 'p-iced-ube-matcha-latte',         'category' => 'cold-drinks', 'name' => 'Iced Ube Matcha Latte',        'description' => 'Matcha ve ube katmanlı buzlu latte.', 'price' => 185, 'image' => '💚', 'is_new' => true, 'calories' => 230, 'mods' => $coldMods],
            ['slug' => 'p-lemon-vanilla-latte',           'category' => 'hot-coffee',  'name' => 'Lemon Vanilla Latte',          'description' => 'Limon ve vanilya aromalı sıcak latte.', 'price' => 150, 'image' => '☕', 'is_new' => true, 'calories' => 220, 'mods' => $drinkMods],
            ['slug' => 'p-ube-vanilla-latte',             'category' => 'hot-coffee',  'name' => 'Ube Vanilla Latte',            'description' => 'Ube ve vanilya aromalı sıcak latte.', 'price' => 160, 'image' => '☕', 'is_new' => true, 'calories' => 230, 'mods' => $drinkMods],
            ['slug' => 'p-lemon-vanilla-cream-frap',      'category' => 'cold-drinks', 'name' => 'Lemon Vanilla Cream Frappuccino', 'description' => 'Kremsi, vanilyalı, limonlu frappuccino.', 'price' => 195, 'image' => '🍹', 'is_new' => true, 'calories' => 310, 'mods' => $coldMods],
            ['slug' => 'p-caffe-americano',               'category' => 'hot-coffee',  'name' => 'Caffè Americano',              'description' => 'Sıcak su ile uzatılmış, yoğun espresso.', 'price' => 115, 'image' => '☕', 'calories' => 10, 'mods' => $sizeExtras],
            ['slug' => 'p-ube-matcha-latte',              'category' => 'hot-tea',     'name' => 'Ube Matcha Latte',             'description' => 'Sıcak matcha ve ube latte.', 'price' => 170, 'image' => '🍵', 'is_new' => true, 'calories' => 200, 'mods' => $drinkMods],
            ['slug' => 'p-cardamom-latte',                'category' => 'hot-coffee',  'name' => 'Cardamom Latte',               'description' => 'Kakule aromalı sıcak latte.', 'price' => 155, 'image' => '☕', 'is_new' => true, 'calories' => 220, 'mods' => $drinkMods],
            ['slug' => 'p-iced-pistachio-latte',          'category' => 'cold-drinks', 'name' => 'Iced Pistachio Latte',         'description' => 'Antep fıstığı aromalı buzlu latte.', 'price' => 175, 'image' => '💚', 'is_new' => true, 'calories' => 260, 'mods' => $coldMods],
            ['slug' => 'p-cinnamon-dolce-latte',          'category' => 'hot-coffee',  'name' => 'Cinnamon Dolce Latte',         'description' => 'Tarçın ve karamel şurubu ile sıcak latte.', 'price' => 160, 'image' => '☕', 'is_new' => true, 'calories' => 240, 'mods' => $drinkMods],
            ['slug' => 'p-pistachio-macchiato',           'category' => 'hot-coffee',  'name' => 'Pistachio Macchiato',          'description' => 'Antep fıstığı aromalı sıcak macchiato.', 'price' => 175, 'image' => '☕', 'is_new' => true, 'calories' => 250, 'mods' => $drinkMods],
            ['slug' => 'p-iced-cardamom-latte',           'category' => 'cold-drinks', 'name' => 'Iced Cardamom Latte',          'description' => 'Kakule aromalı buzlu latte.', 'price' => 165, 'image' => '🧊', 'is_new' => true, 'calories' => 210, 'mods' => $coldMods],
            ['slug' => 'p-matcha-frappuccino',            'category' => 'cold-drinks', 'name' => 'Matcha Frappuccino',           'description' => 'Matcha aromalı blender frappuccino.', 'price' => 190, 'image' => '🍹', 'calories' => 320, 'mods' => $coldMods],
            ['slug' => 'p-matcha-latte',                  'category' => 'hot-tea',     'name' => 'Matcha Latte',                 'description' => 'Premium matcha tozu ile sıcak latte.', 'price' => 160, 'image' => '🍵', 'calories' => 180, 'mods' => $drinkMods],
            ['slug' => 'p-iced-matcha-latte',             'category' => 'cold-drinks', 'name' => 'Iced Matcha Latte',            'description' => 'Buzlu matcha latte.', 'price' => 165, 'image' => '🍵', 'calories' => 180, 'mods' => $coldMods],
            ['slug' => 'p-caffe-latte',                   'category' => 'hot-coffee',  'name' => 'Caffè Latte',                  'description' => 'Klasik sıcak latte.', 'price' => 135, 'image' => '☕', 'calories' => 190, 'mods' => $drinkMods],
            ['slug' => 'p-iced-lemon-sparkling',          'category' => 'cold-drinks', 'name' => 'Iced Lemon Sparkling',         'description' => 'Serin, gazlı limonata.', 'price' => 110, 'image' => '🍋', 'is_new' => true, 'calories' => 90, 'mods' => $sizeOnly],
            ['slug' => 'p-poppy-bagel',                   'category' => 'food',        'name' => 'Haşhaşlı Simit Sandviç',       'description' => 'Haşhaşlı simit, kaşar peyniri, domates.', 'price' => 125, 'image' => '🥐', 'calories' => 380, 'mods' => []],
            ['slug' => 'p-cinnamon-roll',                 'category' => 'food',        'name' => 'Tarçınlı Rulo',                'description' => 'Sıcak servis tarçınlı rulo.', 'price' => 95, 'image' => '🥮', 'calories' => 310, 'mods' => []],
            ['slug' => 'p-croissant',                     'category' => 'food',        'name' => 'Sade Kruvasan',                'description' => 'Tereyağlı, katmanlı kruvasan.', 'price' => 75, 'image' => '🥐', 'calories' => 290, 'mods' => []],
            ['slug' => 'p-ethiopia-beans',                'category' => 'merch',       'name' => 'Ethiopia Çekirdek 250g',       'description' => 'Sitrus ve koyu kakao notaları.', 'price' => 395, 'image' => '🫘', 'mods' => []],
            ['slug' => 'p-guatemala-beans',               'category' => 'merch',       'name' => 'Guatemala Antigua Çekirdek 250g', 'description' => 'Kakao ve hafif baharat notaları.', 'price' => 395, 'image' => '🫘', 'mods' => []],
            ['slug' => 'p-spring-blend',                  'category' => 'merch',       'name' => 'Spring Season Blend 250g',     'description' => '2026 ilkbahar özel harmanı.', 'price' => 425, 'image' => '🌸', 'mods' => []],
        ];

        foreach ($products as $p) {
            $cat = Category::where('tenant_id', $tenant->id)->where('slug', $p['category'])->firstOrFail();
            $product = Product::updateOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => $p['slug']],
                [
                    'tenant_id'      => $tenant->id,
                    'category_id'    => $cat->id,
                    'name'           => $p['name'],
                    'description'    => $p['description'] ?? null,
                    'price'          => $p['price'],
                    'currency'       => 'TRY',
                    'image'          => $p['image'] ?? null,
                    'is_new'         => $p['is_new'] ?? false,
                    'is_recommended' => $p['is_recommended'] ?? false,
                    'is_active'      => true,
                    'calories'       => $p['calories'] ?? null,
                    'tags'           => $p['tags'] ?? null,
                    'star_reward'    => 1,
                ]
            );

            $product->modifiers()->delete();
            foreach ($p['mods'] as $modKey) {
                $tpl = $modifierTemplates[$modKey];
                $modifier = $product->modifiers()->create([
                    'slug'  => $modKey,
                    'name'  => $tpl['name'],
                    'type'  => $tpl['type'],
                    'order' => $tpl['order'],
                ]);
                foreach ($tpl['options'] as $idx => $opt) {
                    $modifier->options()->create([
                        'slug'        => $opt['slug'],
                        'name'        => $opt['name'],
                        'price_delta' => $opt['price_delta'],
                        'is_default'  => $opt['is_default'],
                        'order'       => $idx + 1,
                    ]);
                }
            }
        }
    }
}
