@props([
    'product',
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
@endphp

<div class="group overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
    <a href="{{ route('products.show', $product->slug) }}" class="block">
        <div class="flex aspect-[16/10] items-center justify-center bg-gradient-to-br from-blue-50 to-emerald-50">
            @if ($coverUrl)
                <img src="{{ $coverUrl }}"
                     alt="{{ $product->name }}"
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
        <div class="mb-3 flex items-center justify-between gap-3">
            @if ($product->category)
                <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">
                    {{ $product->category->name }}
                </span>
            @else
                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                    Produk Digital
                </span>
            @endif

            @if ($remainingQuota > 0)
                <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
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
        @endif

        <div class="mt-5">
            <p class="text-xs font-bold uppercase tracking-widest text-blue-600">
                {{ $priceLabel }}
            </p>

            @if ($isDiscounted && $normalPrice > $price)
                <p class="mt-1 text-sm text-slate-400 line-through">
                    {{ \App\Support\Money::rupiah($normalPrice) }}
                </p>
            @endif

            <p class="mt-1 text-3xl font-black text-slate-950">
                {{ \App\Support\Money::rupiah($price) }}
            </p>
        </div>

        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('products.show', $product->slug) }}"
               class="inline-flex flex-1 items-center justify-center rounded-2xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700 transition hover:border-blue-600 hover:text-blue-600">
                Detail
            </a>

            <a href="{{ route('checkout.create', $product->slug) }}"
               class="inline-flex flex-1 items-center justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">
                Beli
            </a>
        </div>
    </div>
</div>