@extends('layouts.public')

@section('title', 'Kebijakan Privasi - Ruang Cerdas')
@section('meta_description', 'Kebijakan privasi terkait penggunaan data pembeli, email, WhatsApp, pembayaran manual, dan link download di Ruang Cerdas.')
@section('robots', 'index,follow')
@section('canonical', route('public.privacy'))

@section('content')
<section class="bg-slate-50 py-16 md:py-20">
    <div class="mx-auto max-w-5xl px-6 text-center">
        <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-bold uppercase tracking-widest text-blue-700">LEGAL</span>
        <h1 class="mt-4 text-3xl font-black text-slate-950 md:text-5xl">Kebijakan Privasi</h1>
        <p class="mx-auto mt-4 max-w-3xl text-slate-600">Penjelasan penggunaan data pembeli pada proses pembelian dan pengiriman produk digital Ruang Cerdas.</p>
        <p class="mt-3 text-sm text-slate-500">Terakhir diperbarui: 2026</p>
    </div>
</section>

<section class="bg-white py-12 md:py-16">
    <div class="mx-auto max-w-5xl px-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-8 space-y-6">
            <h2 class="text-2xl font-black text-slate-900">Pengantar</h2>
            <p class="text-slate-600">Kami menjaga data pembeli dengan prinsip seperlunya untuk menjalankan layanan.</p>
            <h3 class="text-xl font-bold text-slate-900">Data yang dikumpulkan</h3>
            <p class="text-slate-600">Data yang dikumpulkan dapat meliputi nama pembeli, email, nomor WhatsApp, data order/invoice, dan bukti pembayaran.</p>
            <h3 class="text-xl font-bold text-slate-900">Nama pembeli</h3>
            <p class="text-slate-600">Digunakan untuk identifikasi order dan komunikasi layanan.</p>
            <h3 class="text-xl font-bold text-slate-900">Email</h3>
            <p class="text-slate-600">Digunakan untuk pengiriman informasi order dan link download setelah pembayaran disetujui.</p>
            <h3 class="text-xl font-bold text-slate-900">Nomor WhatsApp</h3>
            <p class="text-slate-600">Digunakan bila diperlukan untuk konfirmasi atau bantuan order.</p>
            <h3 class="text-xl font-bold text-slate-900">Data order/invoice</h3>
            <p class="text-slate-600">Disimpan untuk pencatatan transaksi, verifikasi, dan pelacakan status order.</p>
            <h3 class="text-xl font-bold text-slate-900">Bukti pembayaran</h3>
            <p class="text-slate-600">Digunakan untuk verifikasi manual oleh admin.</p>
            <h3 class="text-xl font-bold text-slate-900">Tujuan penggunaan data</h3>
            <p class="text-slate-600">Data digunakan untuk memproses pembelian, memverifikasi pembayaran, mengirim link download, dan menangani bantuan pelanggan.</p>
            <h3 class="text-xl font-bold text-slate-900">Pengiriman link download</h3>
            <p class="text-slate-600">Link download dikirim ke email pembeli setelah pembayaran disetujui admin.</p>
            <h3 class="text-xl font-bold text-slate-900">Verifikasi pembayaran</h3>
            <p class="text-slate-600">Verifikasi dilakukan secara manual berdasarkan bukti pembayaran yang diunggah.</p>
            <h3 class="text-xl font-bold text-slate-900">Bantuan pelanggan</h3>
            <p class="text-slate-600">Data kontak digunakan untuk menindaklanjuti kendala order atau download.</p>
            <h3 class="text-xl font-bold text-slate-900">Penyimpanan data</h3>
            <p class="text-slate-600">Data disimpan selama diperlukan untuk operasional layanan, keamanan, dan administrasi transaksi.</p>
            <h3 class="text-xl font-bold text-slate-900">Keamanan data</h3>
            <p class="text-slate-600">Kami berupaya menerapkan langkah teknis dan operasional yang wajar untuk melindungi data pembeli.</p>
            <h3 class="text-xl font-bold text-slate-900">Pembagian data kepada pihak ketiga</h3>
            <p class="text-slate-600">Data tidak dibagikan secara sembarangan, kecuali jika diperlukan untuk operasional layanan atau kewajiban hukum yang berlaku.</p>
            <h3 class="text-xl font-bold text-slate-900">Cookie atau data teknis</h3>
            <p class="text-slate-600">Situs dapat menggunakan data teknis dasar untuk fungsi layanan dan keamanan sistem.</p>
            <h3 class="text-xl font-bold text-slate-900">Hak pembeli</h3>
            <p class="text-slate-600">Pembeli dapat menghubungi admin untuk permintaan klarifikasi data terkait order yang dimiliki.</p>
            <h3 class="text-xl font-bold text-slate-900">Perubahan kebijakan privasi</h3>
            <p class="text-slate-600">Kebijakan ini dapat diperbarui dari waktu ke waktu. Versi terbaru akan tersedia di halaman ini.</p>
            <h3 class="text-xl font-bold text-slate-900">Kontak</h3>
            <p class="text-slate-600">Jika ada pertanyaan privasi, silakan hubungi tim Ruang Cerdas melalui kontak resmi yang tersedia.</p>
        </div>
    </div>
</section>

<section class="bg-slate-50 py-14">
    <div class="mx-auto max-w-5xl px-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-8 text-center">
            <h3 class="text-2xl font-black text-slate-900">Akses cepat</h3>
            <div class="mt-6 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="{{ route('products.index') }}" class="rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white hover:bg-blue-700">Lihat Produk</a>
                <a href="{{ route('public.order-tracking.index') }}" class="rounded-2xl bg-slate-900 px-6 py-3 text-sm font-bold text-white hover:bg-slate-700">Cek Order</a>
                <a href="{{ route('public.faq') }}" class="rounded-2xl border border-slate-300 bg-white px-6 py-3 text-sm font-bold text-slate-900 hover:border-blue-600 hover:text-blue-600">FAQ</a>
            </div>
        </div>
    </div>
</section>
@endsection
