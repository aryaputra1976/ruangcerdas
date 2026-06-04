<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function index(Product $product)
    {
        $reviews = $product->reviews()
            ->orderByDesc('reviewed_at')
            ->orderByDesc('id')
            ->get();

        return view('admin.products.reviews.index', compact('product', 'reviews'));
    }

    public function store(Request $request, Product $product)
    {
        $validated = $this->validateReview($request);

        $review = $product->reviews()->create([
            'author_name' => trim($validated['author_name']),
            'title' => filled($validated['title'] ?? null) ? trim($validated['title']) : null,
            'body' => trim($validated['body']),
            'rating' => (int) $validated['rating'],
            'is_visible' => (bool) ($validated['is_visible'] ?? false),
            'reviewed_at' => $validated['reviewed_at'] ?? null,
        ]);

        ActivityLogger::log(
            'product_review.created',
            $product,
            'Admin menambahkan testimoni produk.',
            [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'review_id' => $review->id,
                'author_name' => $review->author_name,
                'rating' => $review->rating,
            ]
        );

        return redirect()
            ->route('admin.products.reviews.index', $product)
            ->with('success', 'Testimoni produk berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product, ProductReview $review)
    {
        if ($review->product_id !== $product->id) {
            abort(404);
        }

        $validated = $this->validateReview($request);

        $review->update([
            'author_name' => trim($validated['author_name']),
            'title' => filled($validated['title'] ?? null) ? trim($validated['title']) : null,
            'body' => trim($validated['body']),
            'rating' => (int) $validated['rating'],
            'is_visible' => (bool) ($validated['is_visible'] ?? false),
            'reviewed_at' => $validated['reviewed_at'] ?? null,
        ]);

        ActivityLogger::log(
            'product_review.updated',
            $product,
            'Admin memperbarui testimoni produk.',
            [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'review_id' => $review->id,
                'author_name' => $review->author_name,
                'rating' => $review->rating,
            ]
        );

        return redirect()
            ->route('admin.products.reviews.index', $product)
            ->with('success', 'Testimoni produk berhasil diperbarui.');
    }

    public function destroy(Product $product, ProductReview $review)
    {
        if ($review->product_id !== $product->id) {
            abort(404);
        }

        $reviewId = $review->id;
        $authorName = $review->author_name;

        $review->delete();

        ActivityLogger::log(
            'product_review.deleted',
            $product,
            'Admin menghapus testimoni produk.',
            [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'review_id' => $reviewId,
                'author_name' => $authorName,
            ]
        );

        return redirect()
            ->route('admin.products.reviews.index', $product)
            ->with('success', 'Testimoni produk berhasil dihapus.');
    }

    private function validateReview(Request $request): array
    {
        return $request->validate([
            'author_name' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'is_visible' => ['nullable', 'boolean'],
            'reviewed_at' => ['nullable', 'date'],
        ]);
    }
}
