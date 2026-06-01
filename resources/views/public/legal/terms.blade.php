@extends('layouts.public')

@section('title', 'Syarat & Ketentuan - Ruang Cerdas')
@section('meta_description', 'Syarat dan ketentuan pembelian serta penggunaan produk digital di Ruang Cerdas.')
@section('robots', 'index,follow')
@section('canonical', route('public.terms'))

@section('content')
<section class="bg-slate-50 py-14 md:py-16">
    <div class="mx-auto max-w-5xl px-6 text-center">
        <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Legal</p>
        <h1 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">Syarat & Ketentuan</h1>
        <p class="mx-auto mt-4 max-w-3xl text-slate-600">Aturan dasar penggunaan layanan dan pembelian produk digital di Ruang Cerdas.</p>
        <p class="mt-3 text-sm text-slate-500">Terakhir diperbarui: 2026</p>
    </div>
</section>

<section class="bg-white py-14 md:py-16">
    <div class="mx-auto max-w-5xl px-6">
        <div class="space-y-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
            @foreach ([
                [
                    'title' => 'Pengantar',
                    'body' => 'Dengan menggunakan layanan Ruang Cerdas, Anda menyetujui syarat dan ketentuan yang berlaku pada halaman ini.',
                ],
                [
                    'title' => 'Produk digital',
                    'body' => 'Ruang Cerdas menyediakan produk digital seperti template, ebook, file ZIP, tools AI, dan aplikasi siap pakai. Setiap produk dijelaskan sesuai informasi yang tersedia pada halaman produk.',
                ],
                [
                    'title' => 'Pembelian dan pembayaran manual',
                    'body' => 'Pembelian dilakukan melalui checkout dengan data pembeli yang benar. Pembayaran saat ini diproses manual, bukan otomatis, sehingga pembeli perlu mengikuti instruksi pembayaran yang diberikan setelah checkout.',
                ],
                [
                    'title' => 'Verifikasi admin',
                    'body' => 'Bukti pembayaran diverifikasi oleh admin sebelum order disetujui. Status order dapat dicek melalui halaman pelacakan order menggunakan invoice dan data kontak pembeli.',
                ],
                [
                    'title' => 'Akses download setelah pembayaran disetujui',
                    'body' => 'Link download dikirim ke email pembeli setelah pembayaran dinyatakan valid atau order berstatus paid. Akses download menggunakan link aman sesuai masa berlaku dan batas penggunaan sistem.',
                ],
                [
                    'title' => 'Data pembeli',
                    'body' => 'Nama, email, nomor WhatsApp, data order, dan bukti pembayaran digunakan seperlunya untuk memproses pembelian, verifikasi, dukungan pelanggan, dan pengiriman akses download.',
                ],
                [
                    'title' => 'Keamanan akses file',
                    'body' => 'Ruang Cerdas tidak menampilkan jalur file private atau token download mentah kepada publik. Akses file diberikan melalui mekanisme sistem yang aman.',
                ],
                [
                    'title' => 'Penggunaan produk',
                    'body' => 'Produk digital digunakan untuk kebutuhan yang sah sesuai deskripsi produk. Dilarang menyalahgunakan layanan atau membagikan akses secara tidak sah yang merugikan sistem dan pihak lain.',
                ],
                [
                    'title' => 'Perubahan layanan',
                    'body' => 'Informasi produk, harga, promosi, dan isi halaman hukum dapat diperbarui dari waktu ke waktu. Versi terbaru akan ditampilkan pada website Ruang Cerdas.',
                ],
                [
                    'title' => 'Bantuan',
                    'body' => 'Jika Anda membutuhkan bantuan terkait order, pembayaran, atau akses download, gunakan halaman cek order atau hubungi admin melalui kanal bantuan yang tersedia.',
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
            <h3 class="mt-3 text-3xl font-black text-slate-900 md:text-4xl">Butuh halaman lain?</h3>
            <div class="mt-6 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="{{ route('products.index') }}" class="rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white hover:bg-blue-700">Lihat Produk</a>
                <a href="{{ route('public.order-tracking.index') }}" class="rounded-2xl bg-slate-900 px-6 py-3 text-sm font-bold text-white hover:bg-slate-700">Cek Order</a>
                <a href="{{ route('public.faq') }}" class="rounded-2xl border border-slate-300 bg-white px-6 py-3 text-sm font-bold text-slate-900 hover:border-blue-600 hover:text-blue-600">FAQ</a>
            </div>
        </div>
    </div>
</section>
@endsection
