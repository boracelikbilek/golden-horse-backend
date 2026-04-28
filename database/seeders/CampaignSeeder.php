<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::where('slug', 'golden-horse')->firstOrFail();

        $campaigns = [
            ['slug' => 'c-whole-bean-5star', 'title' => 'Tüm Çekirdek Kahveler', 'subtitle' => 'Her paketle 5⭐ hediye',          'image' => '🫘', 'gradient' => ['#2B1F14','#CDA863'], 'reward_text' => '5⭐ hediye', 'cta_text' => 'İncele',     'ends_at' => '2026-05-15 23:59:59'],
            ['slug' => 'c-23-nisan',         'title' => '23 Nisan Neşesi',        'subtitle' => 'Çocuklara özel',                  'image' => '🎈', 'gradient' => ['#B8D8E8','#E8C8D8'], 'reward_text' => null,         'cta_text' => 'Keşfet',     'ends_at' => '2026-04-24 23:59:59'],
            ['slug' => 'c-double-star',      'title' => 'Çift Yıldız Salısı',     'subtitle' => 'Salı günleri 2 kat yıldız',       'image' => '⭐', 'gradient' => ['#CDA863','#9A7F4A'], 'reward_text' => '2x yıldız', 'cta_text' => 'Hatırlat',  'ends_at' => null],
            ['slug' => 'c-happy-hour',       'title' => 'Golden Hour',            'subtitle' => '14:00-17:00 arası %25 indirim',   'image' => '🌅', 'gradient' => ['#A67C5A','#CDA863'], 'reward_text' => null,         'cta_text' => 'Menüye Git', 'ends_at' => null],
            ['slug' => 'c-welcome',          'title' => 'Hoş geldin!',            'subtitle' => 'İlk siparişine 3⭐ hediye',       'image' => '🐎', 'gradient' => ['#2B1F14','#CDA863'], 'reward_text' => '3⭐',        'cta_text' => 'Sipariş Ver','ends_at' => null],
        ];

        foreach ($campaigns as $c) {
            Campaign::updateOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => $c['slug']],
                $c + ['tenant_id' => $tenant->id]
            );
        }
    }
}
