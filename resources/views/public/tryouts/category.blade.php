@extends('layouts.public')

@section('title', $title . ' - Ruang Cerdas')
@section('meta_description', $description)
@section('canonical', url()->current())

@section('content')
<section class="relative overflow-hidden bg-slate-950 py-16 text-white">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(59,130,246,0.35),_transparent_34%),radial-gradient(circle_at_bottom_right,_rgba(16,185,129,0.22),_transparent_30%)]"></div>

    <div class="relative mx-auto max-w-7xl px-6">
        <div class="max-w-3xl">
            <p class="inline-flex rounded-full border border-white/10 bg-white/10 px-4 py-2 text-sm font-bold uppercase tracking-widest text-blue-100">
                {{ $title }}
            </p>
            <h1 class="mt-5 text-4xl font-black tracking-tight md:text-5xl">{{ $heading }}</h1>
            <p class="mt-5 text-base leading-8 text-slate-300 md:text-lg">{{ $description }}</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ $backUrl }}" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/30 transition hover:bg-blue-700">
                    Kembali ke Kategori Tryout
                </a>
                <a href="{{ route('products.index', ['type' => 'tryout']) }}" class="inline-flex items-center justify-center rounded-2xl border border-white/15 bg-white/10 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/15">
                    Lihat Produk Tryout
                </a>
            </div>
        </div>
    </div>
</section>

<section class="bg-slate-50 py-16">
    <div class="mx-auto max-w-7xl px-6">
        @if ($products->isNotEmpty())
            <div class="mb-8 rounded-3xl border border-slate-200 bg-white px-5 py-4 text-sm font-semibold text-slate-600 shadow-sm">
                Total paket aktif: <span class="font-black text-slate-950">{{ number_format($products->count(), 0, ',', '.') }}</span>
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($products as $product)
                    @include('components.public.product-card', [
                        'product' => $product,
                        'supportWhatsapp' => $supportWhatsapp ?? null,
                        'whatsappCtaText' => 'Tanya via WhatsApp',
                        'whatsappDefaultMessage' => null,
                    ])
                @endforeach
            </div>
        @else
            <div class="rounded-[2rem] border border-dashed border-slate-300 bg-white p-12 text-center shadow-sm">
                <span class="inline-flex rounded-full bg-amber-100 px-4 py-2 text-sm font-bold uppercase tracking-widest text-amber-700">
                    {{ $emptyBadge }}
                </span>
                <h2 class="mt-5 text-2xl font-black text-slate-950">{{ $heading }} belum tersedia</h2>
                <p class="mx-auto mt-3 max-w-2xl text-slate-600">
                    Produk tryout untuk kategori ini belum dipublikasikan. Begitu produk tersedia, halaman ini akan langsung menampilkan kartu paket yang bisa dibeli.
                </p>
            </div>
        @endif
    </div>
</section>
@endsection
