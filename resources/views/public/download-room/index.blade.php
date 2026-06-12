@extends('layouts.public')

@section('title', 'Ruang Akses - Ruang Cerdas')
@section('meta_description', 'Lihat semua order pembeli berdasarkan email, termasuk produk digital dan tryout.')
@section('robots', 'noindex,nofollow')

@section('content')
<section class="bg-slate-50 pt-4 pb-8 md:pt-5 md:pb-8">
    <div class="mx-auto max-w-5xl px-6">
        <div class="mb-6">
            <p class="inline-flex rounded-full bg-blue-50 px-4 py-2 text-xs font-bold uppercase tracking-widest text-blue-700">
                Ruang Akses
            </p>
            <h1 class="mt-3 text-3xl font-black tracking-tight text-slate-950 md:text-4xl">Buka semua akses pembelian Anda</h1>
            <p class="mt-2 max-w-3xl text-slate-600">
                Masukkan email pembeli dan salah satu nomor invoice untuk melihat semua order yang terkait, termasuk produk digital dan tryout.
            </p>
        </div>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm md:p-8">
                @if ($errors->has('download_room'))
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                        {{ $errors->first('download_room') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('public.download-room.show') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="buyer_email" class="block text-sm font-bold text-slate-700">Email Pembeli</label>
                        <input
                            id="buyer_email"
                            name="buyer_email"
                            type="email"
                            value="{{ old('buyer_email') }}"
                            required
                            class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-700 shadow-sm focus:border-blue-600 focus:ring-blue-600"
                            placeholder="email@contoh.com"
                        >
                        @error('buyer_email')
                            <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="invoice_number" class="block text-sm font-bold text-slate-700">Nomor Invoice</label>
                        <input
                            id="invoice_number"
                            name="invoice_number"
                            type="text"
                            value="{{ old('invoice_number') }}"
                            required
                            class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-700 shadow-sm focus:border-blue-600 focus:ring-blue-600"
                            placeholder="Contoh: INV-RC-000123"
                        >
                        @error('invoice_number')
                            <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="rc-btn-success w-full px-6 py-4 text-base">
                        Buka Ruang Akses
                    </button>
                </form>
            </div>

            <aside class="space-y-6">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-black text-slate-950">Yang perlu disiapkan</h2>
                    <div class="mt-4 grid gap-3 text-sm leading-6 text-slate-700">
                        <div class="rounded-2xl bg-slate-50 px-4 py-3">Gunakan email yang dipakai saat checkout.</div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3">Masukkan nomor invoice sesuai pesanan.</div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3">Jika email cocok, semua order pembeli akan ditampilkan sekaligus.</div>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-blue-100 bg-blue-50 p-6 shadow-sm">
                    <h2 class="text-lg font-black text-blue-950">Belum menemukan invoice?</h2>
                    <p class="mt-3 text-sm leading-6 text-blue-900">
                        Cek email order atau buka halaman status order untuk memastikan data yang dipakai sudah benar.
                    </p>
                    <div class="mt-4 flex flex-col gap-3">
                        <a href="{{ route('public.order-tracking.index') }}" class="rc-btn-neutral px-5 py-3 text-sm">
                            Status Order
                        </a>
                        <a href="{{ route('products.index') }}" class="rc-btn-secondary px-5 py-3 text-sm">
                            Kembali ke Produk
                        </a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection
