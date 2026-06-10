<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdCreative;
use App\Models\Product;
use App\Services\AdCreativeGenerator;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class AdCreativeController extends Controller
{
    public function __construct(
        private readonly AdCreativeGenerator $generator
    ) {
    }

    public function index(Request $request)
    {
        $query = AdCreative::query()
            ->with(['product', 'creator'])
            ->latest();

        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $query->where(function ($builder) use ($q) {
                $builder->where('title', 'like', "%{$q}%")
                    ->orWhere('headline', 'like', "%{$q}%")
                    ->orWhere('brand_text', 'like', "%{$q}%")
                    ->orWhereHas('product', fn ($productQuery) => $productQuery->where('name', 'like', "%{$q}%"));
            });
        }

        $creatives = $query->paginate(12)->withQueryString();

        return view('admin.ad-creatives.index', [
            'creatives' => $creatives,
        ]);
    }

    public function create()
    {
        return $this->formView();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mode' => ['nullable', 'string', 'in:single,bulk'],
            'generate_all_sizes' => ['nullable', 'boolean'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['integer', 'exists:products,id'],
            'template_key' => ['required', 'string', 'max:100'],
            'size_preset' => ['required', 'string', 'in:' . implode(',', array_keys(AdCreative::SIZE_PRESETS))],
            'title' => ['required', 'string', 'max:255'],
            'headline' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:1500'],
            'bullets' => ['nullable', 'string', 'max:2000'],
            'cta_text' => ['required', 'string', 'max:100'],
            'brand_text' => ['required', 'string', 'max:100'],
            'format' => ['nullable', 'string', 'in:png'],
        ]);

        if (! array_key_exists($validated['template_key'], $this->templateOptions())) {
            return back()->withErrors([
                'template_key' => 'Template iklan tidak dikenali.',
            ])->withInput();
        }

        $mode = $validated['mode'] ?? 'single';
        $sizePresetsToGenerate = $this->sizePresetsToGenerate($validated);

        $bulletLines = collect(preg_split('/\r\n|\r|\n/', (string) ($validated['bullets'] ?? '')) ?: [])
            ->map(fn ($line) => trim($line))
            ->filter()
            ->take(5)
            ->values()
            ->all();

        if ($mode === 'bulk') {
            $productIds = collect($validated['product_ids'] ?? [])->map(fn ($id) => (int) $id)->filter()->unique()->values();

            if ($productIds->isEmpty()) {
                return back()->withErrors([
                    'product_ids' => 'Pilih minimal satu produk untuk generate massal.',
                ])->withInput();
            }

            $products = Product::withTrashed()->whereIn('id', $productIds)->get()->keyBy('id');
            $created = collect();

            foreach ($productIds as $productId) {
                $product = $products->get($productId);

                if (! $product) {
                    continue;
                }

                $prefill = $this->buildProductPrefill($product);

                foreach ($sizePresetsToGenerate as $sizePreset) {
                    $created->push($this->createCreativeRecord(
                        $product,
                        [
                            'template_key' => $validated['template_key'],
                            'size_preset' => $sizePreset,
                            'title' => $validated['title'] !== '' ? $validated['title'] : $prefill['title'],
                            'headline' => $this->mergeProductHeadline($validated['headline'], $prefill['headline'], $product),
                            'body' => $this->mergeProductBody($validated['body'], $prefill['body'], $product),
                            'bullets' => $bulletLines !== []
                                ? $bulletLines
                                : (preg_split('/\r\n|\r|\n/', $prefill['bullets']) ?: []),
                            'cta_text' => $validated['cta_text'],
                            'brand_text' => $validated['brand_text'],
                            'format' => $validated['format'] ?? 'png',
                        ]
                    ));
                }
            }

            ActivityLogger::log(
                'ad_creative.bulk_created',
                null,
                'Admin membuat creative iklan massal.',
                [
                    'count' => $created->count(),
                    'template_key' => $validated['template_key'],
                    'size_count' => count($sizePresetsToGenerate),
                ]
            );

            return redirect()
                ->route('admin.ad-creatives.index')
                ->with('success', $created->count() . ' creative iklan berhasil dibuat sekaligus.');
        }

        $product = filled($validated['product_id'] ?? null)
            ? Product::withTrashed()->findOrFail((int) $validated['product_id'])
            : null;

        $created = collect();

        foreach ($sizePresetsToGenerate as $sizePreset) {
            $created->push($this->createCreativeRecord($product, [
                'template_key' => $validated['template_key'],
                'size_preset' => $sizePreset,
                'title' => $validated['title'],
                'headline' => $validated['headline'],
                'body' => $validated['body'],
                'bullets' => $bulletLines,
                'cta_text' => $validated['cta_text'],
                'brand_text' => $validated['brand_text'],
                'format' => $validated['format'] ?? 'png',
            ]));
        }

        if ($created->count() > 1) {
            ActivityLogger::log(
                'ad_creative.multi_size_created',
                null,
                'Admin membuat creative iklan beberapa ukuran sekaligus.',
                [
                    'count' => $created->count(),
                    'template_key' => $validated['template_key'],
                ]
            );

            return redirect()
                ->route('admin.ad-creatives.index')
                ->with('success', $created->count() . ' creative iklan untuk beberapa ukuran berhasil dibuat.');
        }

        $creative = $created->first();

        return redirect()
            ->route('admin.ad-creatives.show', $creative)
            ->with('success', 'Gambar iklan berhasil dibuat.');
    }

    public function edit(AdCreative $adCreative)
    {
        return $this->formView($adCreative);
    }

    public function update(Request $request, AdCreative $adCreative)
    {
        $validated = $this->validateSingleCreativePayload($request);

        if (! array_key_exists($validated['template_key'], $this->templateOptions())) {
            return back()->withErrors([
                'template_key' => 'Template iklan tidak dikenali.',
            ])->withInput();
        }

        $product = filled($validated['product_id'] ?? null)
            ? Product::withTrashed()->findOrFail((int) $validated['product_id'])
            : null;

        $oldImagePath = $adCreative->image_path;
        $generated = $this->generator->generate([
            'template_key' => $validated['template_key'],
            'size_preset' => $validated['size_preset'],
            'title' => $validated['title'],
            'headline' => $validated['headline'],
            'body' => $validated['body'],
            'bullets' => $validated['bullets'],
            'cta_text' => $validated['cta_text'],
            'brand_text' => $validated['brand_text'],
            'format' => 'png',
        ], $product);

        $adCreative->fill([
            'product_id' => $product?->id,
            'template_key' => $validated['template_key'],
            'title' => $validated['title'],
            'headline' => $validated['headline'],
            'body' => $validated['body'],
            'bullets' => $validated['bullets'],
            'cta_text' => $validated['cta_text'],
            'brand_text' => $validated['brand_text'],
            'image_path' => $generated['image_path'],
            'format' => $generated['format'],
            'width' => $generated['width'],
            'height' => $generated['height'],
        ])->save();

        if ($oldImagePath && $oldImagePath !== $generated['image_path'] && Storage::disk('public')->exists($oldImagePath)) {
            Storage::disk('public')->delete($oldImagePath);
        }

        ActivityLogger::log(
            'ad_creative.updated',
            $adCreative,
            'Admin memperbarui creative iklan.',
            [
                'product_id' => $product?->id,
                'template_key' => $adCreative->template_key,
            ]
        );

        return redirect()
            ->route('admin.ad-creatives.show', $adCreative)
            ->with('success', 'Creative iklan berhasil diperbarui dan digenerate ulang.');
    }

    public function show(AdCreative $adCreative)
    {
        $adCreative->load(['product', 'creator']);

        return view('admin.ad-creatives.show', [
            'creative' => $adCreative,
        ]);
    }

    public function download(AdCreative $adCreative)
    {
        if (! Storage::disk('public')->exists($adCreative->image_path)) {
            abort(404, 'File creative iklan tidak ditemukan.');
        }

        $format = strtolower((string) request('format', $adCreative->format ?: 'png'));

        if (! in_array($format, AdCreativeGenerator::SUPPORTED_FORMATS, true)) {
            abort(404, 'Format export creative tidak tersedia.');
        }

        ActivityLogger::log(
            'ad_creative.downloaded',
            $adCreative,
            'Admin mengunduh creative iklan.',
            [
                'image_path' => $adCreative->image_path,
                'format' => $format,
            ]
        );

        if ($format === 'png') {
            return Storage::disk('public')->download(
                $adCreative->image_path,
                'iklan-' . $adCreative->id . '.png',
                ['Content-Type' => 'image/png']
            );
        }

        try {
            $exported = $this->generator->export(Storage::disk('public')->path($adCreative->image_path), $format);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return response($exported['binary'], Response::HTTP_OK, [
            'Content-Type' => $exported['mime'],
            'Content-Disposition' => 'attachment; filename="iklan-' . $adCreative->id . '.' . $exported['extension'] . '"',
        ]);
    }

    public function duplicate(AdCreative $adCreative)
    {
        $adCreative->loadMissing('product');

        $duplicate = $this->createCreativeRecord($adCreative->product, [
            'template_key' => $adCreative->template_key,
            'title' => $adCreative->title . ' Copy',
            'headline' => $adCreative->headline,
            'body' => $adCreative->body,
            'bullets' => $adCreative->bullets ?? [],
            'cta_text' => $adCreative->cta_text,
            'brand_text' => $adCreative->brand_text,
            'format' => $adCreative->format,
        ]);

        ActivityLogger::log(
            'ad_creative.duplicated',
            $duplicate,
            'Admin menduplikasi creative iklan.',
            [
                'source_id' => $adCreative->id,
            ]
        );

        return redirect()
            ->route('admin.ad-creatives.show', $duplicate)
            ->with('success', 'Creative iklan berhasil diduplikasi.');
    }

    public function destroy(AdCreative $adCreative)
    {
        if ($adCreative->image_path && Storage::disk('public')->exists($adCreative->image_path)) {
            Storage::disk('public')->delete($adCreative->image_path);
        }

        $creativeId = $adCreative->id;
        $adCreative->delete();

        ActivityLogger::log(
            'ad_creative.deleted',
            null,
            'Admin menghapus creative iklan.',
            ['ad_creative_id' => $creativeId]
        );

        return redirect()
            ->route('admin.ad-creatives.index')
            ->with('success', 'Creative iklan berhasil dihapus.');
    }

    private function formView(?AdCreative $creative = null)
    {
        $products = Product::query()->orderBy('name')->get(['id', 'name', 'slug', 'short_description', 'sale_price', 'normal_price']);

        return view('admin.ad-creatives.create', [
            'creative' => $creative,
            'products' => $products,
            'templates' => $this->templateOptions(),
            'productPrefills' => $products
                ->mapWithKeys(fn (Product $product) => [$product->id => $this->buildProductPrefill($product)])
                ->all(),
            'sizePresets' => AdCreative::sizePresetOptions(),
        ]);
    }

    private function validateSingleCreativePayload(Request $request): array
    {
        $validated = $request->validate([
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'template_key' => ['required', 'string', 'max:100'],
            'size_preset' => ['required', 'string', 'in:' . implode(',', array_keys(AdCreative::SIZE_PRESETS))],
            'title' => ['required', 'string', 'max:255'],
            'headline' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:1500'],
            'bullets' => ['nullable', 'string', 'max:2000'],
            'cta_text' => ['required', 'string', 'max:100'],
            'brand_text' => ['required', 'string', 'max:100'],
        ]);

        $validated['bullets'] = collect(preg_split('/\r\n|\r|\n/', (string) ($validated['bullets'] ?? '')) ?: [])
            ->map(fn ($line) => trim($line))
            ->filter()
            ->take(5)
            ->values()
            ->all();

        return $validated;
    }

    private function sizePresetsToGenerate(array $validated): array
    {
        if ((bool) ($validated['generate_all_sizes'] ?? false)) {
            return array_keys(AdCreative::SIZE_PRESETS);
        }

        return [$validated['size_preset']];
    }

    private function templateOptions(): array
    {
        return [
            'viral_note' => 'Viral Note 9:16',
            'urgent_offer' => 'Urgent Offer 9:16',
            'social_proof' => 'Social Proof 9:16',
        ];
    }

    private function createCreativeRecord(?Product $product, array $payload): AdCreative
    {
        $generated = $this->generator->generate([
            'template_key' => $payload['template_key'],
            'size_preset' => $payload['size_preset'] ?? 'story',
            'title' => $payload['title'],
            'headline' => $payload['headline'],
            'body' => $payload['body'],
            'bullets' => $payload['bullets'] ?? [],
            'cta_text' => $payload['cta_text'],
            'brand_text' => $payload['brand_text'],
            'format' => $payload['format'] ?? 'png',
        ], $product);

        $creative = AdCreative::query()->create([
            'product_id' => $product?->id,
            'template_key' => $payload['template_key'],
            'title' => $payload['title'],
            'headline' => $payload['headline'],
            'body' => $payload['body'],
            'bullets' => collect($payload['bullets'] ?? [])->map(fn ($bullet) => trim((string) $bullet))->filter()->values()->all(),
            'cta_text' => $payload['cta_text'],
            'brand_text' => $payload['brand_text'],
            'image_path' => $generated['image_path'],
            'format' => $generated['format'],
            'width' => $generated['width'],
            'height' => $generated['height'],
            'created_by' => auth()->id(),
        ]);

        ActivityLogger::log(
            'ad_creative.created',
            $creative,
            'Admin membuat creative iklan baru.',
            [
                'product_id' => $product?->id,
                'template_key' => $creative->template_key,
            ]
        );

        return $creative;
    }

    private function mergeProductHeadline(string $headline, string $fallback, Product $product): string
    {
        return str_contains($headline, '{product}')
            ? str_replace('{product}', $product->name, $headline)
            : $headline;
    }

    private function mergeProductBody(string $body, string $fallback, Product $product): string
    {
        $priceText = 'Rp' . number_format((int) $product->public_price, 0, ',', '.');

        return str_replace(
            ['{product}', '{price}'],
            [$product->name, $priceText],
            $body !== '' ? $body : $fallback
        );
    }

    private function buildProductPrefill(Product $product): array
    {
        $price = 'Rp' . number_format((int) $product->public_price, 0, ',', '.');
        $shortDescription = trim((string) ($product->short_description ?: 'Materi digital yang ringkas, relevan, dan siap dipakai belajar.'));
        $productType = Product::PRODUCT_TYPE_LABELS[$product->product_type] ?? 'Produk Digital';

        return [
            'title' => 'Catatan ' . $productType,
            'headline' => $product->name . ' ini cocok buat yang butuh materi belajar lebih terarah tanpa bingung mulai dari mana.',
            'body' => $shortDescription . ' Cocok dipakai untuk bantu calon pembeli memahami isi produk dengan cepat.',
            'bullets' => implode("\n", [
                'Materi lebih ringkas dan fokus ke kebutuhan inti pembeli.',
                'Bisa dipakai belajar mandiri kapan saja tanpa jadwal kaku.',
                'Harga mulai ' . $price . ' dengan format digital yang praktis.',
            ]),
            'cta_text' => 'Cek Produknya',
            'brand_text' => 'ruangcerdas.id',
        ];
    }
}
