@extends('layouts.public')

@section('title', 'Checkout - ' . $product->name)

@section('content')
@php
    $price = $pricing['price'] ?? $product->normal_price;
    $normalPrice = $pricing['normal_price'] ?? $product->normal_price;
    $isDiscounted = $pricing['is_discounted'] ?? false;
    $remainingQuota = $pricing['remaining_quota'] ?? 0;
    $priceLabel = $pricing['label'] ?? 'Harga Produk';
@endphp

<section class="bg-slate-50 py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mb-10">
            <a href="{{ route('products.show', $product->slug) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">
                ← Kembali ke detail produk
            </a>

            <h1 class="mt-4 text-4xl font-black text-slate-950">
                Checkout
            </h1>

            <p class="mt-3 text-slate-600">
                Isi data pembeli dengan benar. Link download akan diberikan setelah pembayaran disetujui admin.
            </p>
        </div>

        <div class="grid gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                    <h2 class="text-2xl font-black">Data Pembeli</h2>

                    <form method="POST" action="{{ route('checkout.store', $product->slug) }}" class="mt-8 space-y-6">
                        @csrf

                        <div>
                            <label for="buyer_name" class="block text-sm font-bold text-slate-700">
                                Nama Lengkap
                            </label>
                            <input
                                id="buyer_name"
                                name="buyer_name"
                                type="text"
                                value="{{ old('buyer_name') }}"
                                placeholder="Contoh: Khairul Anwar"
                                class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:border-blue-600 focus:ring-blue-600"
                                required
                            >
                            @error('buyer_name')
                                <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="buyer_email" class="block text-sm font-bold text-slate-700">
                                Email Aktif
                            </label>
                            <input
                                id="buyer_email"
                                name="buyer_email"
                                type="email"
                                value="{{ old('buyer_email') }}"
                                placeholder="email@contoh.com"
                                class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:border-blue-600 focus:ring-blue-600"
                                required
                            >
                            <p class="mt-2 text-sm text-slate-500">
                                Email digunakan untuk identitas order dan pengiriman info download.
                            </p>
                            @error('buyer_email')
                                <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="buyer_whatsapp" class="block text-sm font-bold text-slate-700">
                                Nomor WhatsApp
                            </label>
                            <input
                                id="buyer_whatsapp"
                                name="buyer_whatsapp"
                                type="text"
                                value="{{ old('buyer_whatsapp') }}"
                                placeholder="Contoh: 081234567890"
                                class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:border-blue-600 focus:ring-blue-600"
                                required
                            >
                            <p class="mt-2 text-sm text-slate-500">
                                WhatsApp digunakan jika admin perlu menghubungi terkait pembayaran.
                            </p>
                            @error('buyer_whatsapp')
                                <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="rounded-2xl bg-blue-50 p-5 text-sm leading-6 text-blue-900">
                            Setelah klik tombol checkout, sistem akan membuat invoice. Pembayaran dilakukan manual melalui transfer/QRIS.
                        </div>

                        <button type="submit" class="w-full rounded-2xl bg-blue-600 px-6 py-4 text-base font-bold text-white shadow-lg shadow-blue-600/20 hover:bg-blue-700">
                            Buat Invoice & Lanjut Pembayaran
                        </button>
                    </form>
                </div>
            </div>

            <aside>
                <div class="sticky top-24 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-bold uppercase tracking-widest text-blue-600">
                        Ringkasan Order
                    </p>

                    <h2 class="mt-4 text-2xl font-black text-slate-950">
                        {{ $product->name }}
                    </h2>

                    <p class="mt-3 text-sm leading-6 text-slate-600">
                        {{ $product->short_description }}
                    </p>

                    <div class="mt-6 border-t border-slate-200 pt-6">
                        <p class="text-sm font-bold uppercase tracking-widest text-blue-600">
                            {{ $priceLabel }}
                        </p>

                        @if ($isDiscounted && $normalPrice > $price)
                            <p class="mt-2 text-sm text-slate-400 line-through">
                                {{ \App\Support\Money::rupiah($normalPrice) }}
                            </p>
                        @endif

                        <p class="mt-1 text-4xl font-black text-slate-950">
                            {{ \App\Support\Money::rupiah($price) }}
                        </p>

                        @if ($remainingQuota > 0)
                            <p class="mt-3 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                                Harga awal aktif. Tersisa {{ $remainingQuota }} slot.
                            </p>
                        @endif
                    </div>

                    <div class="mt-6 rounded-2xl bg-slate-50 p-4 text-sm leading-6 text-slate-600">
                        Produk berbentuk file digital. Setelah pembayaran disetujui, pembeli dapat mengunduh produk melalui link khusus.
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection