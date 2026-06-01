@extends('layouts.public')

@section('title', 'Kebijakan Privasi - Ruang Cerdas')
@section('meta_description', 'Kebijakan privasi terkait penggunaan data pembeli, email, WhatsApp, pembayaran manual, dan link download di Ruang Cerdas.')
@section('robots', 'index,follow')
@section('canonical', route('public.privacy'))

@section('content')
<section class="bg-slate-50 py-14 md:py-16">
    <div class="mx-auto max-w-5xl px-6 text-center">
        <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Legal</p>
        <h1 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">Kebijakan Privasi</h1>
        <p class="mx-auto mt-4 max-w-3xl text-slate-600">Penjelasan penggunaan data pembeli pada proses pembelian dan pengiriman produk digital Ruang Cerdas.</p>
        <p class="mt-3 text-sm text-slate-500">Terakhir diperbarui: 2026</p>
    </div>
</section>

<section class="bg-white py-14 md:py-16">
    <div class="mx-auto max-w-5xl px-6">
        <div class="space-y-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
            @foreach ([
                [
                    'title' => 'Pengantar',
                    'body' => 'Kami menggunakan data pembeli seperlunya untuk menjalankan layanan Ruang Cerdas, terutama untuk proses pembelian, verifikasi pembayaran, dukungan pelanggan, dan pengiriman akses download.',
                ],
                [
                    'title' => 'Data yang dikumpulkan',
                    'body' => 'Data yang dapat kami gunakan meliputi nama pembeli, email, nomor WhatsApp atau kontak lain yang diisi saat checkout, data order atau invoice, serta bukti pembayaran yang diunggah pembeli.',
                ],
                [
                    'title' => 'Nama pembeli',
                    'body' => 'Nama digunakan untuk identifikasi order dan membantu proses verifikasi pembayaran serta layanan pelanggan.',
                ],
                [
                    'title' => 'Email',
                    'body' => 'Email digunakan untuk pengiriman informasi order dan link download setelah pembayaran disetujui admin.',
                ],
                [
                    'title' => 'WhatsApp atau kontak pembeli',
                    'body' => 'Nomor WhatsApp digunakan bila diperlukan untuk konfirmasi order, verifikasi pembayaran, atau bantuan terkait kendala pembelian.',
                ],
                [
                    'title' => 'Data order dan invoice',
                    'body' => 'Data order seperti nomor invoice, produk yang dibeli, waktu transaksi, dan status pembayaran digunakan untuk pencatatan transaksi serta pelacakan status order.',
                ],
                [
                    'title' => 'Bukti pembayaran',
                    'body' => 'Bukti pembayaran digunakan untuk proses verifikasi manual oleh admin agar pembayaran dapat disetujui dengan lebih akurat.',
                ],
                [
                    'title' => 'Tujuan penggunaan data',
                    'body' => 'Data pembeli digunakan untuk memproses checkout, verifikasi pembayaran, mengirim link download, menangani bantuan pelanggan, dan menjaga keamanan operasional layanan.',
                ],
                [
                    'title' => 'Keamanan akses download',
                    'body' => 'Kami tidak menampilkan token download mentah atau jalur file private kepada publik. Akses file diberikan melalui mekanisme sistem yang aman sesuai status order.',
                ],
                [
                    'title' => 'Penyimpanan dan pembaruan data',
                    'body' => 'Data disimpan selama diperlukan untuk operasional layanan, administrasi transaksi, dan keamanan sistem. Kebijakan ini dapat diperbarui dari waktu ke waktu sesuai kebutuhan layanan.',
                ],
                [
                    'title' => 'Bantuan dan pertanyaan',
                    'body' => 'Jika Anda memiliki pertanyaan terkait data order atau penggunaan data pribadi dalam transaksi Anda, silakan hubungi admin melalui kanal bantuan yang tersedia di website.',
                ],
            ] as $section)
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <h2 class="text-xl font-black text-slate-900">{{ $section['title'] }}</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ $section['body'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-slate-50 py-14">
    <div class="mx-auto max-w-5xl px-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-8 text-center">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Akses Cepat</p>
            <h3 class="mt-3 text-3xl font-black text-slate-900 md:text-4xl">Halaman penting lainnya</h3>
            <div class="mt-6 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="{{ route('products.index') }}" class="rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white hover:bg-blue-700">Lihat Produk</a>
                <a href="{{ route('public.order-tracking.index') }}" class="rounded-2xl bg-slate-900 px-6 py-3 text-sm font-bold text-white hover:bg-slate-700">Cek Order</a>
                <a href="{{ route('public.faq') }}" class="rounded-2xl border border-slate-300 bg-white px-6 py-3 text-sm font-bold text-slate-900 hover:border-blue-600 hover:text-blue-600">FAQ</a>
            </div>
        </div>
    </div>
</section>
@endsection
