<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Services\TenantContext;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function categories(Request $request, TenantContext $tenants)
    {
        $tenant = $tenants->resolve($request);

        $rows = Category::forTenant($tenant)->where('is_active', true)->orderBy('order')->get();

        return response()->json($rows->map(fn ($c) => [
            'id'       => $c->slug,
            'name'     => $c->name,
            'subtitle' => $c->subtitle,
            'image'    => $c->image,
            'order'    => $c->order,
        ]));
    }

    public function products(Request $request, TenantContext $tenants)
    {
        $tenant = $tenants->resolve($request);

        $rows = Product::forTenant($tenant)
            ->where('is_active', true)
            ->with(['category', 'modifiers.options'])
            ->get();

        return response()->json($rows->map(fn ($p) => $this->shapeProduct($p)));
    }

    public function product(Request $request, TenantContext $tenants, string $slug)
    {
        $tenant  = $tenants->resolve($request);
        $product = Product::forTenant($tenant)->where('slug', $slug)
            ->with(['category', 'modifiers.options'])->firstOrFail();

        return response()->json($this->shapeProduct($product));
    }

    public function stores(Request $request, TenantContext $tenants)
    {
        $tenant = $tenants->resolve($request);

        $rows = Store::forTenant($tenant)->where('is_active', true)->get();

        return response()->json($rows->map(fn ($s) => [
            'id'          => $s->slug,
            'name'        => $s->name,
            'address'     => $s->address,
            'city'        => $s->city,
            'district'    => $s->district,
            'phone'       => $s->phone,
            'openingTime' => $s->opening_time,
            'closingTime' => $s->closing_time,
            'latitude'    => $s->latitude,
            'longitude'   => $s->longitude,
            'tags'        => $s->tags,
            'comingSoon'  => $s->coming_soon,
        ]));
    }

    public function campaigns(Request $request, TenantContext $tenants)
    {
        $tenant = $tenants->resolve($request);
        $rows = Campaign::forTenant($tenant)->where('is_active', true)->get();

        return response()->json($rows->map(fn ($c) => [
            'id'         => $c->slug,
            'title'      => $c->title,
            'subtitle'   => $c->subtitle,
            'image'      => $c->image,
            'gradient'   => $c->gradient,
            'endsAt'     => optional($c->ends_at)->toDateString(),
            'rewardText' => $c->reward_text,
            'ctaText'    => $c->cta_text,
        ]));
    }

    public function badges(Request $request, TenantContext $tenants)
    {
        $tenant = $tenants->resolve($request);
        $rows = Badge::forTenant($tenant)->get();

        $earned = $request->user()
            ? $request->user()->badges()->pluck('badges.id')->toArray()
            : [];

        return response()->json($rows->map(fn ($b) => [
            'id'            => $b->slug,
            'name'          => $b->name,
            'description'   => $b->description,
            'icon'          => $b->icon,
            'earned'        => in_array($b->id, $earned, true),
            'starsRequired' => $b->stars_required,
        ]));
    }

    public function rewardLevels(Request $request)
    {
        return response()->json([
            ['id' => 'green', 'name' => 'Green',  'minStars' => 0,   'maxStars' => 149, 'perks' => ['Hoş geldin yıldızı', 'Doğum günü hediyesi']],
            ['id' => 'gold',  'name' => 'Gold',   'minStars' => 150, 'maxStars' => null,'perks' => ['2x yıldız Salısı', 'Kişiye özel kampanyalar', 'Kahve hediye haklarına ulaş']],
        ]);
    }

    private function shapeProduct(Product $p): array
    {
        return [
            'id'             => $p->slug,
            'name'           => $p->name,
            'description'    => $p->description,
            'price'          => (float) $p->price,
            'currency'       => 'TL',
            'image'          => $p->image,
            'categoryId'     => $p->category?->slug,
            'isNew'          => $p->is_new,
            'isRecommended'  => $p->is_recommended,
            'tags'           => $p->tags ?? [],
            'calories'       => $p->calories,
            'modifiers'      => $p->modifiers->map(fn ($m) => [
                'id'      => $m->slug,
                'name'    => $m->name,
                'type'    => $m->type,
                'options' => $m->options->map(fn ($o) => [
                    'id'         => $o->slug,
                    'name'       => $o->name,
                    'priceDelta' => (float) $o->price_delta,
                    'default'    => $o->is_default,
                ])->all(),
            ])->all(),
        ];
    }
}
