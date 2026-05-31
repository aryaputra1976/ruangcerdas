@extends('layouts.public')

@section('title', 'Syarat & Ketentuan - Ruang Cerdas')
@section('meta_description', 'Syarat dan ketentuan pembelian serta penggunaan produk digital di Ruang Cerdas.')
@section('robots', 'index,follow')
@section('canonical', route('public.terms'))

@section('content')
<section class="bg-slate-50 py-16 md:py-20">
    <div class="mx-auto max-w-5xl px-6 text-center">
        <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-bold uppercase tracking-widest text-blue-700">LEGAL</span>
        <h1 class="mt-4 text-3xl font-black text-slate-950 md:text-5xl">Syarat & Ketentuan</h1>
        <p class="mx-auto mt-4 max-w-3xl text-slate-600">Aturan dasar penggunaan layanan dan pembelian produk digital di Ruang Cerdas.</p>
        <p class="mt-3 text-sm text-slate-500">Terakhir diperbarui: 2026</p>
    </div>
</section>

<section class="bg-white py-12 md:py-16">
    <div class="mx-auto max-w-5xl px-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-8 space-y-6">
            <h2 class="text-2xl font-black text-slate-900">Pengantar</h2>
            <p class="text-slate-600">Dengan menggunakan layanan Ruang Cerdas, Anda menyetujui syarat dan ketentuan ini.</p>
            <h3 class="text-xl font-bold text-slate-900">Definisi layanan</h3>
            <p class="text-slate-600">Ruang Cerdas menyediakan produk digital seperti template, ebook, file ZIP, tools AI, dan aplikasi siap pakai.</p>
            <h3 class="text-xl font-bold text-slate-900">Produk digital</h3>
            <p class="text-slate-600">Produk yang dibeli berupa file digital dan akses diberikan melalui link download aman.</p>
            <h3 class="text-xl font-bold text-slate-900">Informasi produk dan harga</h3>
            <p class="text-slate-600">Kami berupaya menampilkan informasi produk dan harga secara jelas. Perubahan harga/promosi dapat terjadi sewaktu-waktu.</p>
            <h3 class="text-xl font-bold text-slate-900">Proses pembelian</h3>
            <p class="text-slate-600">Pembeli mengisi data checkout dengan benar, lalu melanjutkan pembayaran sesuai instruksi yang tersedia.</p>
            <h3 class="text-xl font-bold text-slate-900">Pembayaran manual</h3>
            <p class="text-slate-600">Pembayaran diproses manual, bukan otomatis.</p>
            <h3 class="text-xl font-bold text-slate-900">Verifikasi pembayaran</h3>
            <p class="text-slate-600">Bukti pembayaran diverifikasi admin sebelum order disetujui.</p>
            <h3 class="text-xl font-bold text-slate-900">Pengiriman link download</h3>
            <p class="text-slate-600">Link download dikirim ke email pembeli setelah pembayaran disetujui.</p>
            <h3 class="text-xl font-bold text-slate-900">Masa berlaku link download</h3>
            <p class="text-slate-600">Link download memiliki batas masa berlaku dan batas penggunaan sesuai sistem.</p>
            <h3 class="text-xl font-bold text-slate-900">Penggunaan produk digital</h3>
            <p class="text-slate-600">Produk digunakan untuk kebutuhan pembeli sesuai tujuan yang sah.</p>
            <h3 class="text-xl font-bold text-slate-900">Larangan penyalahgunaan</h3>
            <p class="text-slate-600">Dilarang menyalahgunakan layanan, membagikan akses secara tidak sah, atau melakukan tindakan yang merugikan sistem.</p>
            <h3 class="text-xl font-bold text-slate-900">Ketersediaan layanan</h3>
            <p class="text-slate-600">Kami berupaya menjaga layanan tetap tersedia, namun pemeliharaan atau gangguan teknis dapat terjadi.</p>
            <h3 class="text-xl font-bold text-slate-900">Bantuan dan dukungan</h3>
            <p class="text-slate-600">Pembeli dapat menggunakan halaman cek order atau menghubungi admin untuk bantuan terkait order dan akses download.</p>
            <h3 class="text-xl font-bold text-slate-900">Perubahan syarat dan ketentuan</h3>
            <p class="text-slate-600">Syarat ini dapat diperbarui dari waktu ke waktu. Versi terbaru akan ditampilkan pada halaman ini.</p>
            <h3 class="text-xl font-bold text-slate-900">Kontak</h3>
            <p class="text-slate-600">Untuk pertanyaan lebih lanjut, silakan hubungi tim Ruang Cerdas melalui kanal kontak yang tersedia.</p>
        </div>
    </div>
</section>

<section class="bg-slate-50 py-14">
    <div class="mx-auto max-w-5xl px-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-8 text-center">
            <h3 class="text-2xl font-black text-slate-900">Butuh halaman lain?</h3>
            <div class="mt-6 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="{{ route('products.index') }}" class="rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white hover:bg-blue-700">Lihat Produk</a>
                <a href="{{ route('public.order-tracking.index') }}" class="rounded-2xl bg-slate-900 px-6 py-3 text-sm font-bold text-white hover:bg-slate-700">Cek Order</a>
                <a href="{{ route('public.faq') }}" class="rounded-2xl border border-slate-300 bg-white px-6 py-3 text-sm font-bold text-slate-900 hover:border-blue-600 hover:text-blue-600">FAQ</a>
            </div>
        </div>
    </div>
</section>
@endsection
