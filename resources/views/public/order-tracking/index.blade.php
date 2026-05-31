@extends('layouts.public')

@section('title', 'Cek Status Order - Ruang Cerdas')
@section('meta_description', 'Lacak status order Ruang Cerdas menggunakan nomor invoice dan email/WhatsApp pembeli.')
@section('robots', 'noindex,nofollow')

@section('content')
<section class="bg-slate-50 py-16">
    <div class="mx-auto max-w-3xl px-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            <div class="text-center">
                <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Order Tracking</p>
                <h1 class="mt-3 text-3xl font-black text-slate-950">Cek Status Order</h1>
                <p class="mt-2 text-slate-600">Masukkan nomor invoice dan email/WhatsApp yang dipakai saat checkout.</p>
            </div>

            @if ($errors->has('tracking'))
                <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first('tracking') }}
                </div>
            @endif

            <form method="POST" action="{{ route('public.order-tracking.show') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="invoice_number" class="mb-1 block text-sm font-semibold text-slate-700">Nomor Invoice</label>
                    <input id="invoice_number" name="invoice_number" type="text" required
                           value="{{ old('invoice_number') }}"
                           class="w-full rounded-xl border border-slate-300 px-4 py-3 text-slate-900 outline-none ring-blue-200 focus:ring"
                           placeholder="Contoh: INV-RC-20260531-0001">
                </div>

                <div>
                    <label for="contact" class="mb-1 block text-sm font-semibold text-slate-700">Email atau WhatsApp</label>
                    <input id="contact" name="contact" type="text" required
                           value="{{ old('contact') }}"
                           class="w-full rounded-xl border border-slate-300 px-4 py-3 text-slate-900 outline-none ring-blue-200 focus:ring"
                           placeholder="nama@email.com / 0812xxxx">
                </div>

                <button type="submit" class="w-full rounded-xl bg-blue-600 px-5 py-3 font-semibold text-white hover:bg-blue-700">
                    Cek Order
                </button>
            </form>
        </div>
    </div>
</section>
@endsection
