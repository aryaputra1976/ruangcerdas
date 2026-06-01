@extends('layouts.public')

@section('title', 'Checkout - ' . $product->name)
@section('robots', 'noindex,nofollow')

@section('content')
@php
    $price = $pricing['price'] ?? $product->normal_price;
    $normalPrice = $pricing['normal_price'] ?? $product->normal_price;
    $isDiscounted = $pricing['is_discounted'] ?? false;
    $remainingQuota = $pricing['remaining_quota'] ?? 0;
    $priceLabel = $pricing['label'] ?? 'Harga Produk';
    $supportNumber = preg_replace('/\D+/', '', (string) ($supportWhatsapp ?? ''));
@endphp

<section class="bg-slate-50 py-14 md:py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mb-8">
            <a href="{{ route('products.show', $product->slug) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">
                Kembali ke Produk
            </a>
            <p class="mt-5 inline-flex rounded-full bg-blue-50 px-4 py-2 text-xs font-bold uppercase tracking-widest text-blue-700">
                Checkout
            </p>
            <h1 class="mt-3 text-3xl font-black tracking-tight text-slate-950 md:text-4xl">Lengkapi Data Pembelian</h1>
            <p class="mt-3 max-w-3xl text-slate-600">
                Isi data pembeli dengan benar agar instruksi pembayaran dan link download dapat diterima dengan jelas.
            </p>
        </div>

        <div class="mb-6 rounded-2xl border border-slate-200 bg-white px-5 py-4 text-center text-sm font-semibold text-slate-700">
            Pembayaran dilakukan manual setelah checkout • Link download dikirim setelah pembayaran disetujui admin
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                Data checkout belum lengkap. Silakan periksa kembali form di bawah.
            </div>
        @endif

        <div class="grid gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm md:p-8">
                    <div class="mb-8">
                        <h2 class="text-2xl font-black text-slate-950">Form Data Pembeli</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Semua field bertanda <span class="font-bold text-red-600">*</span> wajib diisi dengan data aktif.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('checkout.store', $product->slug) }}" class="space-y-6">
                        @csrf

                        <div class="grid gap-6 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <label for="buyer_name" class="block text-sm font-bold text-slate-700">Nama Lengkap <span class="text-red-600">*</span></label>
                                <input id="buyer_name" name="buyer_name" type="text" value="{{ old('buyer_name') }}" placeholder="Contoh: Khairul Anwar" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-700 shadow-sm focus:border-blue-600 focus:ring-blue-600" required autofocus>
                                <p class="mt-2 text-sm text-slate-500">Gunakan nama yang mudah dikenali untuk verifikasi pembayaran.</p>
                                @error('buyer_name')
                                    <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="buyer_email" class="block text-sm font-bold text-slate-700">Email Aktif <span class="text-red-600">*</span></label>
                                <input id="buyer_email" name="buyer_email" type="email" value="{{ old('buyer_email') }}" placeholder="email@contoh.com" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-700 shadow-sm focus:border-blue-600 focus:ring-blue-600" required>
                                <p class="mt-2 text-sm text-slate-500">Email ini dipakai untuk mengirim link download setelah pembayaran disetujui.</p>
                                @error('buyer_email')
                                    <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="buyer_whatsapp" class="block text-sm font-bold text-slate-700">Nomor WhatsApp <span class="text-red-600">*</span></label>
                                <input id="buyer_whatsapp" name="buyer_whatsapp" type="text" value="{{ old('buyer_whatsapp') }}" placeholder="Contoh: 081234567890" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-700 shadow-sm focus:border-blue-600 focus:ring-blue-600" required>
                                <p class="mt-2 text-sm text-slate-500">Dipakai jika admin perlu menghubungi Anda terkait pembayaran.</p>
                                @error('buyer_whatsapp')
                                    <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="coupon_code" class="block text-sm font-bold text-slate-700">Kode Kupon</label>
                            <input id="coupon_code" name="coupon_code" type="text" value="{{ old('coupon_code') }}" placeholder="Contoh: HEMAT10" oninput="this.value = this.value.toUpperCase()" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-700 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                            <p class="mt-2 text-sm text-slate-500">Opsional. Gunakan huruf/angka tanpa spasi.</p>
                            @error('coupon_code')
                                <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="rounded-2xl border border-blue-100 bg-blue-50 px-4 py-4 text-sm leading-6 text-blue-900">
                            <p class="font-bold">Setelah checkout:</p>
                            <p class="mt-1">Anda akan menerima invoice dan instruksi pembayaran manual. Setelah bukti bayar diupload dan disetujui admin, link download akan dikirim ke email Anda.</p>
                        </div>

                        <button type="submit" class="w-full rounded-2xl bg-blue-600 px-6 py-4 text-base font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">
                            Lanjutkan Checkout
                        </button>

                        <p class="text-center text-xs leading-5 text-slate-500">
                            Dengan melanjutkan checkout, Anda menyetujui
                            <a href="{{ route('public.terms') }}" class="text-blue-600 hover:text-blue-700">Syarat & Ketentuan</a>
                            dan
                            <a href="{{ route('public.privacy') }}" class="text-blue-600 hover:text-blue-700">Kebijakan Privasi</a>.
                        </p>
                    </form>
                </div>
            </div>

            <aside>
                <div class="sticky top-24 space-y-6">
                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Ringkasan Produk</p>
                        <h2 class="mt-4 text-2xl font-black text-slate-950">{{ $product->name }}</h2>
                        @if ($product->category)
                            <p class="mt-2 text-sm font-semibold text-slate-500">{{ $product->category->name }}</p>
                        @endif
                        @if ($product->short_description)
                            <p class="mt-3 text-sm leading-6 text-slate-600">{{ $product->short_description }}</p>
                        @endif

                        <div class="mt-4 flex flex-wrap gap-2">
                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">Produk Digital</span>
                            <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">Download Aman</span>
                        </div>

                        <div class="mt-6 border-t border-slate-200 pt-6">
                            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">{{ $priceLabel }}</p>
                            @if ($isDiscounted && $normalPrice > $price)
                                <p class="mt-2 text-sm text-slate-400 line-through">{{ \App\Support\Money::rupiah($normalPrice) }}</p>
                            @endif
                            <p class="mt-1 text-4xl font-black text-slate-950">{{ \App\Support\Money::rupiah($price) }}</p>
                            @if ($remainingQuota > 0)
                                <p class="mt-3 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                                    Harga awal aktif. Tersisa {{ $remainingQuota }} slot.
                                </p>
                            @endif
                        </div>

                        <div class="mt-6 grid gap-3 rounded-2xl bg-slate-50 p-4 text-sm leading-6 text-slate-700">
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                                    <svg viewBox="0 0 20 20" fill="none" class="h-3.5 w-3.5" aria-hidden="true">
                                        <path d="M16 6L8.5 13.5L5 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span>Pembayaran dilakukan manual setelah checkout.</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                                    <svg viewBox="0 0 20 20" fill="none" class="h-3.5 w-3.5" aria-hidden="true">
                                        <path d="M16 6L8.5 13.5L5 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span>Link download dikirim via email setelah pembayaran disetujui admin.</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-blue-100 bg-blue-50 p-6 shadow-sm">
                        <h3 class="font-black text-blue-950">Alur setelah checkout</h3>
                        <div class="mt-4 grid gap-3 text-sm leading-6 text-blue-900">
                            @foreach ([
                                'Invoice dibuat otomatis setelah form dikirim.',
                                'Lakukan pembayaran manual sesuai instruksi.',
                                'Upload bukti pembayaran untuk verifikasi admin.',
                                'Link download dikirim ke email setelah status paid.',
                            ] as $index => $checkoutStep)
                                <div class="flex items-start gap-3 rounded-2xl bg-white px-4 py-3">
                                    <span class="inline-flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">{{ $index + 1 }}</span>
                                    <span>{{ $checkoutStep }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if ($supportNumber !== '')
                        <a href="https://wa.me/{{ $supportNumber }}" target="_blank" rel="noopener noreferrer" class="inline-flex w-full items-center justify-center rounded-2xl bg-green-600 px-5 py-3 text-sm font-bold text-white hover:bg-green-700">
                            Butuh Bantuan? WhatsApp Admin
                        </a>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection
