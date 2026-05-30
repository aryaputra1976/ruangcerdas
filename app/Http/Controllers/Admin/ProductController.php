<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->with('category')
            ->latest();

        if ($request->filled('q')) {
            $q = $request->q;

            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->paginate(10)->withQueryString();

        $categories = Category::query()
            ->orderBy('name')
            ->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::query()
            ->orderBy('name')
            ->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);

        $validated['slug'] = $this->makeUniqueSlug($validated['slug'] ?: $validated['name']);

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->boolean('is_published')) {
            $validated['published_at'] = now();
        } else {
            $validated['published_at'] = null;
        }

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')
                ->store('products/covers', 'public');
        }

        if ($request->hasFile('digital_file')) {
            $file = $request->file('digital_file');

            $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                . '-'
                . now()->format('YmdHis')
                . '.'
                . $file->getClientOriginalExtension();

            $path = $file->storeAs('products', $filename, 'private');

            $validated['digital_file_path'] = $path;
            $validated['download_filename'] = $file->getClientOriginalName();
        }

        Product::create($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        return redirect()->route('admin.products.edit', $product);
    }

    public function edit(Product $product)
    {
        $categories = Category::query()
            ->orderBy('name')
            ->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $this->validateProduct($request, $product);

        $validated['slug'] = $this->makeUniqueSlug(
            $validated['slug'] ?: $validated['name'],
            $product->id
        );

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->boolean('is_published')) {
            $validated['published_at'] = $product->published_at ?? now();
        } else {
            $validated['published_at'] = null;
        }

        if ($request->hasFile('cover_image')) {
            if ($product->cover_image) {
                Storage::disk('public')->delete($product->cover_image);
            }

            $validated['cover_image'] = $request->file('cover_image')
                ->store('products/covers', 'public');
        }

        if ($request->hasFile('digital_file')) {
            if ($product->digital_file_path) {
                Storage::disk('private')->delete($product->digital_file_path);
            }

            $file = $request->file('digital_file');

            $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                . '-'
                . now()->format('YmdHis')
                . '.'
                . $file->getClientOriginalExtension();

            $path = $file->storeAs('products', $filename, 'private');

            $validated['digital_file_path'] = $path;
            $validated['download_filename'] = $file->getClientOriginalName();
        }

        $product->update($validated);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->update([
            'is_active' => false,
            'published_at' => null,
        ]);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil dinonaktifkan.');
    }

    private function validateProduct(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'contents' => ['nullable', 'string'],

            'normal_price' => ['required', 'integer', 'min:0'],
            'sale_price' => ['nullable', 'integer', 'min:0'],
            'first_buyer_price' => ['nullable', 'integer', 'min:0'],
            'first_buyer_quota' => ['nullable', 'integer', 'min:0'],

            'cover_image' => ['nullable', 'image', 'max:2048'],
            'digital_file' => ['nullable', 'file', 'mimes:zip', 'max:102400'],
        ]);
    }

    private function makeUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value);
        $slug = $baseSlug;
        $counter = 2;

        while (
            Product::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}