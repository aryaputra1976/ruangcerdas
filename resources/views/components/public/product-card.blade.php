@props([
    'product',
    'supportWhatsapp' => null,
    'whatsappCtaText' => 'Tanya via WhatsApp',
    'whatsappDefaultMessage' => null,
])

@php
    $pricing = $product->pricing ?? app(\App\Services\PricingService::class)->resolve($product);

    $price = $pricing['price'] ?? $product->normal_price;
    $normalPrice = $pricing['normal_price'] ?? $product->normal_price;
    $isDiscounted = $pricing['is_discounted'] ?? false;
    $remainingQuota = $pricing['remaining_quota'] ?? 0;
    $priceLabel = $pricing['label'] ?? 'Harga Produk';

    $coverUrl = $product->cover_image
        ? asset('storage/' . $product->cover_image)
        : null;
    $productType = (string) ($product->product_type ?? '');
    $productTypeLabel = \App\Models\Product::productTypeLabels()[$productType] ?? null;
    $productTypeBadgeClass = match ($productType) {
        'tryout' => 'bg-indigo-50 text-indigo-700',
        'ebook' => 'bg-emerald-50 text-emerald-700',
        'template' => 'bg-amber-50 text-amber-700',
        'source_code' => 'bg-fuchsia-50 text-fuchsia-700',
        'bundle' => 'bg-sky-50 text-sky-700',
        default => 'bg-slate-100 text-slate-700',
    };
    $supportNumber = preg_replace('/\D+/', '', (string) $supportWhatsapp);
    if (str_starts_with($supportNumber, '0')) {
        $supportNumber = '62' . substr($supportNumber, 1);
    }
    $messageTemplate = trim((string) $whatsappDefaultMessage) !== ''
        ? trim((string) $whatsappDefaultMessage)
        : 'Halo Ruang Cerdas, saya tertarik dengan produk: {nama}. Harga: {harga}. Link: {url}';
    $waMessage = strtr($messageTemplate, [
        '{nama}' => $product->name,
        '{harga}' => \App\Support\Money::rupiah($price),
        '{url}' => route('products.show', $product->slug),
    ]);
    $waUrl = $supportNumber !== '' ? 'https://wa.me/' . $supportNumber . '?text=' . rawurlencode($waMessage) : null;
@endphp

<div class="group overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
    <a href="{{ route('products.show', $product->slug) }}" onclick="window.rcTrack && window.rcTrack('ProductCardClick', {source: 'product_cover', content_type: 'product', content_ids: [{{ $product->id }}]});" class="block">
        <div class="flex aspect-[16/10] items-center justify-center bg-gradient-to-br from-blue-50 to-emerald-50">
            @if ($coverUrl)
                <img src="{{ $coverUrl }}"
                     alt="{{ $product->name }}"
                     loading="lazy"
                     width="640"
                     height="400"
                     class="h-full w-full object-cover">
            @else
                <div class="text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-600 text-xl font-black text-white">
                        RC
                    </div>
                    <p class="mt-3 text-sm font-semibold text-slate-500">
                        Ruang Cerdas
                    </p>
                </div>
            @endif
        </div>
    </a>

    <div class="p-6">
        <div class="mb-3 flex flex-wrap items-center gap-2">
            @if ($product->category)
                <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">
                    {{ $product->category->name }}
                </span>
            @endif
            @if ($productTypeLabel)
                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $productTypeBadgeClass }}">
                    {{ $productTypeLabel }}
                </span>
            @endif
            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">
                Digital
            </span>
            <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                Siap Download
            </span>
            @if ($remainingQuota > 0)
                <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">
                    Sisa {{ $remainingQuota }} slot
                </span>
            @endif
        </div>

        <a href="{{ route('products.show', $product->slug) }}" class="block">
            <h3 class="line-clamp-2 text-xl font-black text-slate-950 transition group-hover:text-blue-600">
                {{ $product->name }}
            </h3>
        </a>

        @if ($product->short_description)
            <p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-600">
                {{ $product->short_description }}
            </p>
        @else
            <p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-600">
                Produk digital siap pakai untuk kebutuhan kerja profesional.
            </p>
        @endif

        <div class="mt-5">
            <p class="text-xs font-bold uppercase tracking-widest text-blue-600">
                {{ $priceLabel }}
            </p>

            @if ($isDiscounted && $normalPrice > $price)
                <p class="mt-1 text-sm text-slate-400 line-through">
                    {{ \App\Support\Money::rupiah($normalPrice) }}
                </p>
                <p class="mt-1 inline-flex rounded-full bg-red-50 px-2 py-1 text-xs font-bold text-red-700">
                    Promo
                </p>
            @endif

            <p class="mt-1 text-3xl font-black text-slate-950">
                {{ \App\Support\Money::rupiah($price) }}
            </p>
        </div>

        <div class="mt-6 flex flex-col gap-3">
            <a href="{{ route('products.show', $product->slug) }}"
               onclick="window.rcTrack && window.rcTrack('ProductCardClick', {source: 'product_detail_button', content_type: 'product', content_ids: [{{ $product->id }}]});"
               class="inline-flex flex-1 items-center justify-center rounded-2xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700 transition hover:border-blue-600 hover:text-blue-600">
                Lihat Detail
            </a>

            <a href="{{ route('checkout.create', $product->slug) }}"
               onclick="window.rcTrack && window.rcTrack('HeroCtaClick', {source: 'product_card_checkout', content_type: 'product', content_ids: [{{ $product->id }}], value: {{ (int) $price }}, currency: 'IDR'});"
               class="inline-flex flex-1 items-center justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">
                Beli Sekarang
            </a>

            @if ($waUrl)
                <a href="{{ $waUrl }}"
                   target="_blank" rel="noopener noreferrer"
                   onclick="window.rcTrack && window.rcTrack('Contact', {source: 'product_card_whatsapp'});"
                   class="inline-flex flex-1 items-center justify-center rounded-2xl bg-green-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-green-600/20 transition hover:bg-green-700">
                    {{ $whatsappCtaText }}
                </a>
            @endif
        </div>
    </div>
</div>
