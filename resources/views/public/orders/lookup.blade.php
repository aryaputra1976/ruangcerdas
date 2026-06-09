@extends('layouts.public')

@section('title', 'Cek Order - Ruang Cerdas')
@section('meta_description', 'Cek status order Ruang Cerdas menggunakan nomor invoice dan email pembeli.')
@section('robots', 'noindex,nofollow')

@section('content')
<section class="bg-slate-50 py-14 md:py-16">
    <div class="mx-auto max-w-3xl px-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
            <div class="text-center">
                <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Cek Order</p>
                <h1 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">Lihat Status Order Anda</h1>
                <p class="mt-2 text-slate-600">Masukkan nomor invoice dan email yang dipakai saat checkout.</p>
            </div>

            @if ($errors->has('lookup'))
                <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first('lookup') }}
                </div>
            @endif

            <form method="POST" action="{{ route('public.orders.lookup.submit') }}" class="mt-6 space-y-5">
                @csrf

                <div>
                    <label for="invoice_number" class="mb-2 block text-sm font-semibold text-slate-700">Nomor Invoice</label>
                    <input
                        id="invoice_number"
                        name="invoice_number"
                        type="text"
                        required
                        value="{{ old('invoice_number') }}"
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-900 outline-none ring-blue-200 focus:ring"
                        placeholder="Contoh: INV-RC-20260610-001"
                    >
                    @error('invoice_number')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="buyer_email" class="mb-2 block text-sm font-semibold text-slate-700">Email Pembeli</label>
                    <input
                        id="buyer_email"
                        name="buyer_email"
                        type="email"
                        required
                        value="{{ old('buyer_email') }}"
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-900 outline-none ring-blue-200 focus:ring"
                        placeholder="nama@email.com"
                    >
                    @error('buyer_email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="rounded-2xl border border-blue-100 bg-blue-50 px-4 py-4 text-sm leading-6 text-blue-900">
                    Demi keamanan, order hanya akan ditampilkan jika nomor invoice dan email pembeli sama persis dengan data saat checkout.
                </div>

                <button type="submit" class="w-full rounded-2xl bg-blue-600 px-5 py-3.5 font-semibold text-white hover:bg-blue-700">
                    Cek Order Sekarang
                </button>
            </form>
        </div>
    </div>
</section>
@endsection
