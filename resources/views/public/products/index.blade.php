@extends('layouts.public')

@section('title', 'Produk Digital - Ruang Cerdas')
@section('meta_description', 'Katalog produk digital siap pakai dari Ruang Cerdas.')
@section('canonical', route('products.index'))
@section('body_class', 'has-mobile-sticky-cta')

@section('content')
@php
    $hasFilter = request()->filled('q') || request()->filled('category') || request()->filled('type');
    $totalProducts = method_exists($products, 'total') ? $products->total() : $products->count();
    $presetCategories = [
        'cpns-pppk' => 'CPNS & PPPK',
        'administrasi-kerja' => 'Administrasi Kerja',
        'skill-digital-pemula' => 'Skill Digital Pemula',
        'template-produktivitas' => 'Template Produktivitas',
        'aplikasi-siap-pakai' => 'Aplikasi Siap Pakai',
    ];
    $categoryLabelMap = [];
    foreach ($categories as $cat) {
        $categoryLabelMap[$cat->slug] = $cat->name;
    }
    $categoryOptions = $presetCategories + $categoryLabelMap;
    $activeCategoryLabel = request('category')
        ? ($categoryLabelMap[request('category')] ?? $presetCategories[request('category')] ?? request('category'))
        : null;
@endphp

<section id="katalog" class="bg-slate-50 pt-2 pb-7 md:pt-3 md:pb-9">
    <div class="mx-auto max-w-7xl px-6">
        <h1 class="mb-3 text-3xl font-black tracking-tight text-slate-950 md:text-4xl">Produk</h1>

        <div id="product-filters" class="mb-5 rounded-[2rem] border border-slate-200 bg-white p-4 shadow-sm scroll-mt-24">
            <form method="GET" action="{{ route('products.index') }}" class="grid gap-3 lg:grid-cols-12">
                <div class="lg:col-span-6">
                    <label for="q" class="sr-only">Cari Produk</label>
                    <input id="q" type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, deskripsi, atau isi produk..." class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-700 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                </div>
                <div class="lg:col-span-4">
                    <label for="category" class="sr-only">Kategori</label>
                    <select id="category" name="category" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-700 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                        <option value="">Semua Kategori</option>
                        @foreach ($categoryOptions as $slug => $label)
                            <option value="{{ $slug }}" @selected(request('category') === $slug)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-3 lg:col-span-2">
                    <button type="submit" class="rc-btn-secondary w-full px-5 py-3 text-sm">
                        Filter
                    </button>
                </div>
            </form>

            @if ($hasFilter)
                <div class="mt-4 flex flex-wrap items-center gap-2">
                        @if (request('q'))
                            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700">{{ request('q') }}</span>
                        @endif
                        @if (request('category'))
                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">{{ $activeCategoryLabel }}</span>
                        @endif
                    <a href="{{ route('products.index') }}" class="ml-1 text-xs font-bold uppercase tracking-wide text-red-600 hover:text-red-700">Reset</a>
                </div>
            @endif
        </div>

        @if ($products->isNotEmpty())
            <div class="grid gap-4 md:gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($products as $product)
                    @include('components.public.product-card', [
                        'product' => $product,
                        'supportWhatsapp' => $supportWhatsapp ?? null,
                        'whatsappCtaText' => $landingSetting?->whatsapp_cta_text ?? 'Tanya via WhatsApp',
                        'whatsappDefaultMessage' => $landingSetting?->whatsapp_default_message,
                    ])
                @endforeach
            </div>
            <div class="mt-8">{{ $products->links() }}</div>
        @else
            <div class="rounded-[2rem] border border-dashed border-slate-300 bg-white p-12 text-center shadow-sm">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-xl font-black text-slate-700">RC</div>
                <h2 class="mt-5 text-2xl font-black text-slate-950">
                    @if ($hasFilter)
                        Produk tidak ditemukan
                    @else
                        Produk belum tersedia.
                    @endif
                </h2>
                <p class="mx-auto mt-3 max-w-md text-slate-600">
                    @if ($hasFilter)
                        Tidak ada produk aktif dan publish yang sesuai dengan filter pencarian Anda.
                    @else
                        Produk belum tersedia.
                    @endif
                </p>
                <div class="mt-6 flex justify-center gap-3">
                    @if ($hasFilter)
                        <a href="{{ route('products.index') }}" class="rc-btn-neutral px-6 py-3 text-sm">
                            Reset Filter
                        </a>
                    @endif
                    <a href="{{ route('home') }}" class="rc-btn-neutral px-6 py-3 text-sm">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        @endif
    </div>
</section>

<div class="fixed inset-x-0 bottom-0 z-40 border-t border-slate-200 bg-white/95 p-3 backdrop-blur md:hidden">
    <div class="mx-auto flex max-w-7xl gap-2">
        <a href="#product-filters" onclick="window.rcTrack && window.rcTrack('HeroCtaClick', {source: 'sticky_products_filter'});" class="rc-btn-secondary flex-1 rounded-xl px-4 py-3 text-sm">
            Filter Produk
        </a>
        @php
            $stickySupport = preg_replace('/\D+/', '', (string) ($supportWhatsapp ?? ''));
            if (str_starts_with($stickySupport, '0')) {
                $stickySupport = '62' . substr($stickySupport, 1);
            }
        @endphp
        @if ($stickySupport !== '')
            <a href="https://wa.me/{{ $stickySupport }}?text={{ rawurlencode('Halo Ruang Cerdas, saya butuh rekomendasi produk.') }}" target="_blank" rel="noopener noreferrer" onclick="window.rcTrack && window.rcTrack('Contact', {source: 'sticky_products_whatsapp'});" class="rc-btn-success flex-1 rounded-xl px-4 py-3 text-sm">
                WhatsApp
            </a>
        @endif
    </div>
</div>
@endsection
