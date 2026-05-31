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

        <div class="mb-10 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <a href="{{ route('products.show', $product->slug) }}"
                   class="text-sm font-semibold text-blue-600 hover:text-blue-700">
                    ← Kembali ke detail produk
                </a>

                <h1 class="mt-4 text-4xl font-black tracking-tight text-slate-950">
                    Checkout
                </h1>

                <p class="mt-3 max-w-2xl text-slate-600">
                    Isi data pembeli dengan benar. Invoice dan instruksi pembayaran akan dibuat otomatis setelah checkout.
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 shadow-sm">
                Produk digital dikirim via link download aman.
            </div>
        </div>

        <div class="grid gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-600 text-lg font-black text-white">
                            1
                        </div>

                        <div>
                            <h2 class="text-2xl font-black text-slate-950">
                                Data Pembeli
                            </h2>

                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Data ini digunakan untuk identitas invoice dan verifikasi pembayaran.
                            </p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('checkout.store', $product->slug) }}" class="mt-8 space-y-6">
                        @csrf

                        <div>
                            <label for="buyer_name" class="block text-sm font-bold text-slate-700">
                                Nama Lengkap <span class="text-red-600">*</span>
                            </label>

                            <input
                                id="buyer_name"
                                name="buyer_name"
                                type="text"
                                value="{{ old('buyer_name') }}"
                                placeholder="Contoh: Khairul Anwar"
                                class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-700 shadow-sm focus:border-blue-600 focus:ring-blue-600"
                                required
                                autofocus
                            >

                            @error('buyer_name')
                                <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="buyer_email" class="block text-sm font-bold text-slate-700">
                                Email Aktif <span class="text-red-600">*</span>
                            </label>

                            <input
                                id="buyer_email"
                                name="buyer_email"
                                type="email"
                                value="{{ old('buyer_email') }}"
                                placeholder="email@contoh.com"
                                class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-700 shadow-sm focus:border-blue-600 focus:ring-blue-600"
                                required
                            >

                            <p class="mt-2 text-sm text-slate-500">
                                Email digunakan sebagai identitas order.
                            </p>

                            @error('buyer_email')
                                <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="buyer_whatsapp" class="block text-sm font-bold text-slate-700">
                                Nomor WhatsApp <span class="text-red-600">*</span>
                            </label>

                            <input
                                id="buyer_whatsapp"
                                name="buyer_whatsapp"
                                type="text"
                                value="{{ old('buyer_whatsapp') }}"
                                placeholder="Contoh: 081234567890"
                                class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-700 shadow-sm focus:border-blue-600 focus:ring-blue-600"
                                required
                            >

                            <p class="mt-2 text-sm text-slate-500">
                                WhatsApp digunakan jika admin perlu menghubungi terkait pembayaran.
                            </p>

                            @error('buyer_whatsapp')
                                <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="coupon_code" class="block text-sm font-bold text-slate-700">
                                Kode Kupon
                            </label>

                            <input
                                id="coupon_code"
                                name="coupon_code"
                                type="text"
                                value="{{ old('coupon_code') }}"
                                placeholder="Contoh: HEMAT10"
                                oninput="this.value = this.value.toUpperCase()"
                                class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-700 shadow-sm focus:border-blue-600 focus:ring-blue-600"
                            >

                            <p class="mt-2 text-sm text-slate-500">
                                Opsional. Gunakan huruf/angka tanpa spasi, contoh: HEMAT10.
                            </p>

                            @error('coupon_code')
                                <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5 text-sm leading-6 text-blue-900">
                            Setelah klik tombol checkout, sistem akan membuat invoice. Pembayaran dilakukan manual melalui transfer bank atau QRIS.
                        </div>

                        <button type="submit"
                                class="w-full rounded-2xl bg-blue-600 px-6 py-4 text-base font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">
                            Buat Invoice & Lanjut Pembayaran
                        </button>
                    </form>
                </div>
            </div>

            <aside>
                <div class="sticky top-24 space-y-6">

                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-bold uppercase tracking-widest text-blue-600">
                            Ringkasan Order
                        </p>

                        <h2 class="mt-4 text-2xl font-black text-slate-950">
                            {{ $product->name }}
                        </h2>

                        @if ($product->short_description)
                            <p class="mt-3 text-sm leading-6 text-slate-600">
                                {{ $product->short_description }}
                            </p>
                        @endif

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

                    <div class="rounded-[2rem] border border-emerald-100 bg-emerald-50 p-6 shadow-sm">
                        <h3 class="font-black text-emerald-950">
                            Alur Setelah Checkout
                        </h3>

                        <ol class="mt-4 list-decimal space-y-2 pl-5 text-sm leading-6 text-emerald-900">
                            <li>Invoice dibuat otomatis.</li>
                            <li>Pembeli melakukan pembayaran.</li>
                            <li>Pembeli upload bukti bayar.</li>
                            <li>Admin approve pembayaran.</li>
                            <li>Link download aktif.</li>
                        </ol>
                    </div>

                </div>
            </aside>
        </div>
    </div>
</section>
@endsection
