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

<section class="bg-slate-50 py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mb-10">
            <a href="{{ route('products.show', $product->slug) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">
                Kembali ke Produk
            </a>
            <p class="mt-5 inline-flex rounded-full bg-blue-50 px-4 py-2 text-xs font-bold uppercase tracking-widest text-blue-700">
                Checkout
            </p>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-950 md:text-5xl">Lengkapi Data Pembelian</h1>
            <p class="mt-3 max-w-3xl text-slate-600">
                Masukkan data aktif agar link download dapat dikirim setelah pembayaran disetujui.
            </p>
        </div>

        <div class="mb-6 rounded-2xl border border-slate-200 bg-white px-5 py-4 text-center text-sm font-semibold text-slate-700">
            Pembayaran manual • Verifikasi admin • Link download via email • Token aman
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                Data checkout belum lengkap. Silakan periksa kembali form di bawah.
            </div>
        @endif

        <div class="grid gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
                    <h2 class="text-2xl font-black text-slate-950">Form Data Pembeli</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        Pastikan data benar sebelum lanjut.
                    </p>

                    <form method="POST" action="{{ route('checkout.store', $product->slug) }}" class="mt-8 space-y-6">
                        @csrf

                        <div>
                            <label for="buyer_name" class="block text-sm font-bold text-slate-700">Nama Lengkap <span class="text-red-600">*</span></label>
                            <input id="buyer_name" name="buyer_name" type="text" value="{{ old('buyer_name') }}" placeholder="Contoh: Khairul Anwar" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-700 shadow-sm focus:border-blue-600 focus:ring-blue-600" required autofocus>
                            @error('buyer_name')
                                <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="buyer_email" class="block text-sm font-bold text-slate-700">Email Aktif <span class="text-red-600">*</span></label>
                            <input id="buyer_email" name="buyer_email" type="email" value="{{ old('buyer_email') }}" placeholder="email@contoh.com" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-700 shadow-sm focus:border-blue-600 focus:ring-blue-600" required>
                            <p class="mt-2 text-sm text-slate-500">Email digunakan untuk menerima link download.</p>
                            @error('buyer_email')
                                <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="buyer_whatsapp" class="block text-sm font-bold text-slate-700">Nomor WhatsApp <span class="text-red-600">*</span></label>
                            <input id="buyer_whatsapp" name="buyer_whatsapp" type="text" value="{{ old('buyer_whatsapp') }}" placeholder="Contoh: 081234567890" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-700 shadow-sm focus:border-blue-600 focus:ring-blue-600" required>
                            <p class="mt-2 text-sm text-slate-500">WhatsApp digunakan jika admin perlu konfirmasi pembayaran.</p>
                            @error('buyer_whatsapp')
                                <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="coupon_code" class="block text-sm font-bold text-slate-700">Kode Kupon</label>
                            <input id="coupon_code" name="coupon_code" type="text" value="{{ old('coupon_code') }}" placeholder="Contoh: HEMAT10" oninput="this.value = this.value.toUpperCase()" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-700 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                            <p class="mt-2 text-sm text-slate-500">Opsional. Gunakan huruf/angka tanpa spasi.</p>
                            @error('coupon_code')
                                <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full rounded-2xl bg-blue-600 px-6 py-4 text-base font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">
                            Lanjutkan Checkout
                        </button>

                        <p class="text-center text-xs leading-5 text-slate-500">
                            Transaksi diproses manual oleh admin Ruang Cerdas. File digital hanya dapat diakses melalui link download aman.
                        </p>
                        <p class="text-center text-xs leading-5 text-slate-500">
                            Pastikan email aktif karena link download akan dikirim setelah pembayaran disetujui.
                        </p>
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

                        <div class="mt-6 rounded-2xl bg-slate-50 p-4 text-sm leading-6 text-slate-600">
                            Link download dikirim via email setelah pembayaran disetujui.
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-blue-100 bg-blue-50 p-6 shadow-sm">
                        <h3 class="font-black text-blue-950">Alur setelah checkout</h3>
                        <ol class="mt-4 list-decimal space-y-2 pl-5 text-sm leading-6 text-blue-900">
                            <li>Order dibuat.</li>
                            <li>Lakukan pembayaran manual dan upload bukti.</li>
                            <li>Admin approve, link download dikirim email.</li>
                        </ol>
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
