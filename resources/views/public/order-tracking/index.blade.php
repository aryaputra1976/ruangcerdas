@extends('layouts.public')

@section('title', 'Status Order - Ruang Cerdas')
@section('meta_description', 'Lacak status order Ruang Cerdas menggunakan nomor invoice dan email/WhatsApp pembeli.')
@section('robots', 'noindex,nofollow')

@section('content')
<section class="bg-slate-50 pt-3 pb-6 md:pt-4 md:pb-8">
    <div class="mx-auto max-w-5xl px-6">
        <div class="mb-5">
            <p class="inline-flex rounded-full bg-blue-50 px-4 py-2 text-xs font-bold uppercase tracking-widest text-blue-700">
                Status Order
            </p>
            <h1 class="mt-3 text-3xl font-black tracking-tight text-slate-950 md:text-4xl">Cek status order Anda</h1>
            <p class="mt-2 max-w-3xl text-slate-600">
                Masukkan invoice dan email atau WhatsApp yang dipakai saat checkout.
            </p>
        </div>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm md:p-7">
                @if ($errors->has('tracking'))
                    <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                        {{ $errors->first('tracking') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('public.order-tracking.show') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="invoice_number" class="block text-sm font-bold text-slate-700">Nomor Invoice</label>
                        <input id="invoice_number" name="invoice_number" type="text" required
                               value="{{ old('invoice_number') }}"
                               class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-700 shadow-sm focus:border-blue-600 focus:ring-blue-600"
                               placeholder="Contoh: RC-20260531-0001">
                    </div>

                    <div>
                        <label for="contact" class="block text-sm font-bold text-slate-700">Email atau WhatsApp</label>
                        <input id="contact" name="contact" type="text" required
                               value="{{ old('contact') }}"
                               class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-700 shadow-sm focus:border-blue-600 focus:ring-blue-600"
                               placeholder="nama@email.com / 0812xxxx">
                    </div>

                    <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-600">
                        Jika status sudah <span class="font-semibold text-slate-900">paid</span>, lanjutkan ke <span class="font-semibold text-slate-900">Ruang Akses</span> untuk membuka produk.
                    </div>

                    <button type="submit" class="rc-btn-secondary w-full px-6 py-4 text-base">
                        Lihat Status Order
                    </button>
                </form>
            </div>

            <aside class="space-y-6">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-black text-slate-950">Yang perlu disiapkan</h2>
                    <div class="mt-4 grid gap-3 text-sm leading-6 text-slate-700">
                        <div class="rounded-2xl bg-slate-50 px-4 py-3">Masukkan nomor invoice dengan benar.</div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3">Gunakan email atau WhatsApp yang sama seperti saat checkout.</div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3">Jika sudah paid, buka produk dari Ruang Akses.</div>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-blue-100 bg-blue-50 p-6 shadow-sm">
                    <h2 class="text-lg font-black text-blue-950">Butuh halaman yang lebih sederhana?</h2>
                    <p class="mt-3 text-sm leading-6 text-blue-900">
                        Jika Anda cukup punya email pembeli dan invoice, halaman Status Order utama biasanya lebih cepat dipakai.
                    </p>
                    <div class="mt-4 flex flex-col gap-3">
                        <a href="{{ route('public.orders.lookup') }}" class="rc-btn-neutral px-5 py-3 text-sm">
                            Buka Status Order
                        </a>
                        <a href="{{ route('public.download-room.index') }}" class="rc-btn-success px-5 py-3 text-sm">
                            Ruang Akses
                        </a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection
