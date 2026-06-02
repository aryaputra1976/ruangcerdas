<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductPreviewImage;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductPreviewImageController extends Controller
{
    public function index(Product $product)
    {
        $previewImages = $product->previewImages()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.products.preview-images.index', compact('product', 'previewImages'));
    }

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'max:4096'],
            'title' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $path = $request->file('image')->store('product-previews/' . $product->id, 'public');

        $previewImage = $product->previewImages()->create([
            'image_path' => $path,
            'title' => $validated['title'] ?? null,
            'caption' => $validated['caption'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
        ]);

        ActivityLogger::log(
            'product_preview_image.created',
            $product,
            'Admin menambahkan gambar preview produk.',
            [
                'product_name' => $product->name,
                'preview_image_id' => $previewImage->id,
            ]
        );

        return redirect()
            ->route('admin.products.preview-images.index', $product)
            ->with('success', 'Preview image berhasil ditambahkan.');
    }

    public function destroy(Product $product, ProductPreviewImage $previewImage)
    {
        if ($previewImage->product_id !== $product->id) {
            abort(404);
        }

        if ($previewImage->image_path && Storage::disk('public')->exists($previewImage->image_path)) {
            Storage::disk('public')->delete($previewImage->image_path);
        }

        $previewImageId = $previewImage->id;
        $previewImage->delete();

        ActivityLogger::log(
            'product_preview_image.deleted',
            $product,
            'Admin menghapus gambar preview produk.',
            [
                'product_name' => $product->name,
                'preview_image_id' => $previewImageId,
            ]
        );

        return redirect()
            ->route('admin.products.preview-images.index', $product)
            ->with('success', 'Preview image berhasil dihapus.');
    }
}
