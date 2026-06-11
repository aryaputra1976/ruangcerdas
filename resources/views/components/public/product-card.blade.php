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
    $primaryBadge = $product->category?->name ?? 'Produk Digital';
@endphp

<div class="group overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
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

    <div class="p-5">
        <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
            <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">
                {{ $primaryBadge }}
            </span>
            @if ($remainingQuota > 0)
                <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">
                    Sisa {{ $remainingQuota }}
                </span>
            @endif
        </div>

        <a href="{{ route('products.show', $product->slug) }}" class="block">
            <h3 class="line-clamp-2 text-lg font-black text-slate-950 transition group-hover:text-blue-600 sm:text-xl">
                {{ $product->name }}
            </h3>
        </a>

        <div class="mt-4 flex items-end justify-between gap-3">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-widest text-blue-600">
                    {{ $priceLabel }}
                </p>

                @if ($isDiscounted && $normalPrice > $price)
                    <p class="mt-1 text-sm text-slate-400 line-through">
                        {{ \App\Support\Money::rupiah($normalPrice) }}
                    </p>
                @endif

                <p class="mt-1 text-2xl font-black text-slate-950 sm:text-3xl">
                    {{ \App\Support\Money::rupiah($price) }}
                </p>
            </div>

            @if ($waUrl)
                <a href="{{ $waUrl }}"
                   target="_blank" rel="noopener noreferrer"
                   onclick="window.rcTrack && window.rcTrack('Contact', {source: 'product_card_whatsapp'});"
                   class="text-sm font-semibold text-slate-500 transition hover:text-green-700">
                    Tanya
                </a>
            @endif
        </div>

        <div class="mt-5 flex gap-2.5">
            <a href="{{ route('checkout.create', $product->slug) }}"
               onclick="window.rcTrack && window.rcTrack('InitiateCheckout', {source: 'product_card_checkout', content_type: 'product', content_ids: [{{ $product->id }}], value: {{ (int) $price }}, currency: 'IDR'});"
               class="inline-flex flex-1 items-center justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">
                Beli Sekarang
            </a>
            <a href="{{ route('products.show', $product->slug) }}"
               onclick="window.rcTrack && window.rcTrack('ProductCardClick', {source: 'product_detail_button', content_type: 'product', content_ids: [{{ $product->id }}]});"
               class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700 transition hover:border-blue-600 hover:text-blue-600">
                Detail
            </a>
        </div>
    </div>
</div>
