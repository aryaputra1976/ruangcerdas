<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductFaq;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;

class ProductFaqController extends Controller
{
    public function index(Product $product)
    {
        $faqs = $product->faqs()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.products.faqs.index', compact('product', 'faqs'));
    }

    public function store(Request $request, Product $product)
    {
        $validated = $this->validateFaq($request);

        $faq = $product->faqs()->create([
            'question' => trim($validated['question']),
            'answer' => trim($validated['answer']),
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
        ]);

        ActivityLogger::log(
            'product_faq.created',
            $product,
            'Admin menambahkan FAQ produk.',
            [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'faq_id' => $faq->id,
                'question' => str($faq->question)->limit(100, ''),
            ]
        );

        return redirect()
            ->route('admin.products.faqs.index', $product)
            ->with('success', 'FAQ produk berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product, ProductFaq $faq)
    {
        if ($faq->product_id !== $product->id) {
            abort(404);
        }

        $validated = $this->validateFaq($request);

        $faq->update([
            'question' => trim($validated['question']),
            'answer' => trim($validated['answer']),
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
        ]);

        ActivityLogger::log(
            'product_faq.updated',
            $product,
            'Admin memperbarui FAQ produk.',
            [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'faq_id' => $faq->id,
                'question' => str($faq->question)->limit(100, ''),
            ]
        );

        return redirect()
            ->route('admin.products.faqs.index', $product)
            ->with('success', 'FAQ produk berhasil diperbarui.');
    }

    public function destroy(Product $product, ProductFaq $faq)
    {
        if ($faq->product_id !== $product->id) {
            abort(404);
        }

        $faqId = $faq->id;
        $question = str($faq->question)->limit(100, '');

        $faq->delete();

        ActivityLogger::log(
            'product_faq.deleted',
            $product,
            'Admin menghapus FAQ produk.',
            [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'faq_id' => $faqId,
                'question' => $question,
            ]
        );

        return redirect()
            ->route('admin.products.faqs.index', $product)
            ->with('success', 'FAQ produk berhasil dihapus.');
    }

    private function validateFaq(Request $request): array
    {
        return $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
