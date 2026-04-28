<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $q = Product::with('category');
        if (! $user->isSuperadmin()) {
            $q->where('tenant_id', $user->tenant_id);
        }
        $products = $q->orderBy('name')->paginate(50);
        return view('admin.products.index', compact('products'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $categories = Category::where('tenant_id', $user->tenant_id)->orderBy('order')->get();
        return view('admin.products.form', ['product' => new Product(), 'categories' => $categories]);
    }

    public function edit(Request $request, Product $product)
    {
        $user = $request->user();
        if (! $user->isSuperadmin() && $product->tenant_id !== $user->tenant_id) abort(403);
        $categories = Category::where('tenant_id', $product->tenant_id)->orderBy('order')->get();
        return view('admin.products.form', compact('product', 'categories'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'category_id' => 'required|integer|exists:categories,id',
            'slug'        => 'required|string|max:191',
            'name'        => 'required|string|max:191',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'image'       => 'nullable|string|max:32',
            'star_reward' => 'required|integer|min:0',
            'is_new'      => 'sometimes|boolean',
            'is_recommended' => 'sometimes|boolean',
            'is_active'   => 'sometimes|boolean',
        ]);
        $data['tenant_id'] = $user->tenant_id;
        $data['is_new'] = $request->boolean('is_new');
        $data['is_recommended'] = $request->boolean('is_recommended');
        $data['is_active'] = $request->boolean('is_active');
        Product::create($data);
        return redirect()->route('admin.products.index')->with('success', 'Ürün eklendi.');
    }

    public function update(Request $request, Product $product)
    {
        $user = $request->user();
        if (! $user->isSuperadmin() && $product->tenant_id !== $user->tenant_id) abort(403);
        $data = $request->validate([
            'category_id' => 'required|integer|exists:categories,id',
            'slug'        => 'required|string|max:191',
            'name'        => 'required|string|max:191',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'image'       => 'nullable|string|max:32',
            'star_reward' => 'required|integer|min:0',
        ]);
        $data['is_new'] = $request->boolean('is_new');
        $data['is_recommended'] = $request->boolean('is_recommended');
        $data['is_active'] = $request->boolean('is_active');
        $product->update($data);
        return redirect()->route('admin.products.index')->with('success', 'Ürün güncellendi.');
    }

    public function destroy(Product $product, Request $request)
    {
        $user = $request->user();
        if (! $user->isSuperadmin() && $product->tenant_id !== $user->tenant_id) abort(403);
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Ürün silindi.');
    }
}
