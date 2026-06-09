<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\LandingSetting;
use App\Models\Product;
use App\Models\Testimonial;
use App\Services\PricingService;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->with('category')
            ->withCount([
                'reviews as visible_reviews_count' => fn ($query) => $query->where('is_visible', true),
            ])
            ->withAvg([
                'reviews as visible_reviews_avg' => fn ($query) => $query->where('is_visible', true),
            ], 'rating')
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

        if ($request->filled('product_type')) {
            $query->where('product_type', $request->product_type);
        }

        $fileStatus = $request->query('file_status');
        $perPage = 10;

        if (in_array($fileStatus, ['missing', 'ready'], true)) {
            $allProducts = $query->get();

            $filtered = $allProducts->filter(function (Product $product) use ($fileStatus) {
                $isMissing = $product->isMissingPrivateFile();

                return $fileStatus === 'missing' ? $isMissing : ! $isMissing;
            })->values();

            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $currentItems = $filtered->slice(($currentPage - 1) * $perPage, $perPage)->values();

            $products = new LengthAwarePaginator(
                $currentItems,
                $filtered->count(),
                $perPage,
                $currentPage,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'query' => $request->query(),
                ]
            );
        } else {
            $products = $query->paginate($perPage)->withQueryString();
        }

        $categories = Category::query()
            ->orderBy('name')
            ->get();

        $productTypeOptions = Product::productTypeLabels();

        return view('admin.products.index', compact('products', 'categories', 'productTypeOptions'));
    }

    public function create()
    {
        $categories = Category::query()
            ->orderBy('name')
            ->get();

        $productTypeOptions = Product::productTypeLabels();

        return view('admin.products.create', compact('categories', 'productTypeOptions'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);

        $validated['slug'] = $this->makeUniqueSlug($validated['slug'] ?: $validated['name']);
        $validated = $this->applyInferredProductMetadata($validated);

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
            $path = $this->storeDigitalFile($file, null);

            $validated['digital_file_path'] = $path;
            $validated['download_filename'] = $file->getClientOriginalName();
            $validated['file_size'] = (int) $file->getSize();
            $validated['file_mime_type'] = $file->getClientMimeType();
            $validated['file_uploaded_at'] = now();
        }

        $product = Product::create($validated);

        if ($request->hasFile('digital_file')) {
            $newPath = $this->storeDigitalFile($request->file('digital_file'), $product->id);
            if ($newPath !== $product->digital_file_path) {
                Storage::disk('private')->delete($product->digital_file_path);
                $product->update(['digital_file_path' => $newPath]);
            }

            ActivityLogger::log(
                'product_file.uploaded',
                $product,
                'Admin mengunggah file digital produk.',
                ['product_name' => $product->name]
            );
        }

        ActivityLogger::log(
            'product.created',
            $product,
            'Admin menambahkan produk baru.',
            ['product_name' => $product->name]
        );

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
        $product->loadCount([
            'reviews as visible_reviews_count' => fn ($query) => $query->where('is_visible', true),
        ])->loadAvg([
            'reviews as visible_reviews_avg' => fn ($query) => $query->where('is_visible', true),
        ], 'rating');

        $categories = Category::query()
            ->orderBy('name')
            ->get();

        $productTypeOptions = Product::productTypeLabels();

        return view('admin.products.edit', compact('product', 'categories', 'productTypeOptions'));
    }

    public function preview(Product $product, PricingService $pricingService)
    {
        $product->load([
            'category',
            'previewImages' => fn ($query) => $query
                ->orderBy('sort_order')
                ->orderBy('id'),
            'faqs' => fn ($query) => $query
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id'),
            'reviews' => fn ($query) => $query
                ->where('is_visible', true)
                ->orderByDesc('reviewed_at')
                ->orderByDesc('id'),
        ]);

        $pricing = $pricingService->resolve($product);

        $testimonials = Testimonial::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->latest()
            ->take(3)
            ->get();

        $landingSetting = LandingSetting::query()->first();
        $supportWhatsapp = LandingSetting::query()->value('support_whatsapp');
        $isPreview = true;
        $canCheckout = $product->isVisibleToPublic();
        $previewStatus = [
            'is_active' => (bool) $product->is_active,
            'has_file' => ! $product->isMissingPrivateFile(),
            'is_public_visible' => $canCheckout,
        ];

        return view('public.products.show', compact(
            'product',
            'pricing',
            'testimonials',
            'landingSetting',
            'supportWhatsapp',
            'isPreview',
            'canCheckout',
            'previewStatus'
        ));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $this->validateProduct($request, $product);

        $validated['slug'] = $this->makeUniqueSlug(
            $validated['slug'] ?: $validated['name'],
            $product->id
        );
        $validated = $this->applyInferredProductMetadata($validated);

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
            $path = $this->storeDigitalFile($file, $product->id);

            $validated['digital_file_path'] = $path;
            $validated['download_filename'] = $file->getClientOriginalName();
            $validated['file_size'] = (int) $file->getSize();
            $validated['file_mime_type'] = $file->getClientMimeType();
            $validated['file_uploaded_at'] = now();
        }

        $product->update($validated);

        if ($request->hasFile('digital_file')) {
            ActivityLogger::log(
                'product_file.uploaded',
                $product,
                'Admin mengganti file digital produk.',
                ['product_name' => $product->name]
            );
        }

        ActivityLogger::log(
            'product.updated',
            $product,
            'Admin memperbarui produk.',
            ['product_name' => $product->name]
        );

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

        ActivityLogger::log(
            'product.deleted',
            $product,
            'Admin menonaktifkan produk.',
            ['product_name' => $product->name]
        );

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil dinonaktifkan.');
    }

    public function downloadFile(Product $product)
    {
        abort_unless(
            $product->digital_file_path && Storage::disk('private')->exists($product->digital_file_path),
            404,
            'File produk tidak ditemukan.'
        );

        ActivityLogger::log(
            'product_file.downloaded_by_admin',
            $product,
            'Admin mengunduh file digital produk.',
            ['product_name' => $product->name]
        );

        return Storage::disk('private')->download(
            $product->digital_file_path,
            $product->download_filename ?: basename($product->digital_file_path)
        );
    }

    public function destroyFile(Product $product)
    {
        if ($product->digital_file_path) {
            Storage::disk('private')->delete($product->digital_file_path);
        }

        $product->update([
            'digital_file_path' => null,
            'download_filename' => null,
            'file_size' => null,
            'file_mime_type' => null,
            'file_uploaded_at' => null,
        ]);

        ActivityLogger::log(
            'product_file.deleted',
            $product,
            'Admin menghapus file digital produk.',
            ['product_name' => $product->name]
        );

        return redirect()
            ->back()
            ->with('success', 'File produk berhasil dihapus.');
    }

    private function validateProduct(Request $request, ?Product $product = null): array
    {
        $validator = validator($request->all(), [
            'category_id' => ['nullable', 'exists:categories,id'],
            'product_type' => ['nullable', 'in:' . implode(',', array_keys(Product::productTypeLabels()))],
            'category' => ['nullable', 'string', 'max:255'],
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
            'digital_file' => [
                'nullable',
                'file',
                'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/zip,application/x-zip-compressed',
                'max:102400',
            ],
        ]);

        $validator->after(function (Validator $validator) use ($request, $product) {
            if (! $request->boolean('is_published')) {
                return;
            }

            if (! $request->boolean('is_active')) {
                $validator->errors()->add('is_published', 'Produk harus aktif sebelum bisa dipublish.');
            }

            $hasExistingFile = $product?->privateFileExists() ?? false;
            $hasUploadedFile = $request->hasFile('digital_file');

            if (! $hasExistingFile && ! $hasUploadedFile) {
                $validator->errors()->add(
                    'digital_file',
                    'Upload file digital terlebih dahulu sebelum produk dipublish.'
                );
            }
        });

        return $validator->validate();
    }

    private function storeDigitalFile($file, ?int $productId): string
    {
        $safeName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $filename = ($safeName ?: 'file-produk') . '-' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $directory = $productId ? 'products/' . $productId : 'products/tmp';

        return $file->storeAs($directory, $filename, 'private');
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

    private function applyInferredProductMetadata(array $validated): array
    {
        $slugSource = (string) ($validated['slug'] ?? '');
        $nameSource = strtolower((string) ($validated['name'] ?? ''));
        $categoryName = '';

        if (! empty($validated['category_id'])) {
            $categoryName = strtolower((string) Category::query()->whereKey($validated['category_id'])->value('name'));
        }

        $combinedSource = strtolower(trim($slugSource . ' ' . $nameSource . ' ' . $categoryName));

        if (blank($validated['product_type'] ?? null)) {
            $validated['product_type'] = match (true) {
                str_contains($combinedSource, 'tryout') => 'tryout',
                str_contains($combinedSource, 'template') => 'template',
                str_contains($combinedSource, 'source-code'),
                str_contains($combinedSource, 'source code'),
                str_contains($combinedSource, 'aplikasi') => 'source_code',
                str_contains($combinedSource, 'bundle') => 'bundle',
                default => 'ebook',
            };
        }

        if (blank($validated['category'] ?? null)) {
            $validated['category'] = match (true) {
                str_contains($combinedSource, 'pppk-tendik'),
                str_contains($combinedSource, 'tendik') => 'pppk-tendik',
                str_contains($combinedSource, 'pppk') => 'pppk',
                str_contains($combinedSource, 'cpns') => 'cpns',
                default => null,
            };
        }

        return $validated;
    }
}
