@extends('layouts.public')

@section('title', 'FAQ - Ruang Cerdas')
@section('meta_description', 'Pertanyaan umum tentang cara membeli produk digital, pembayaran manual, upload bukti pembayaran, dan akses produk di Ruang Cerdas.')
@section('robots', 'index,follow')
@section('canonical', route('public.faq'))

@section('content')
<section class="bg-slate-50 pt-3 pb-6 md:pt-4 md:pb-8">
    <div class="mx-auto max-w-5xl px-6 text-center">
        <p class="inline-flex rounded-full bg-blue-50 px-4 py-2 text-xs font-bold uppercase tracking-widest text-blue-700">FAQ</p>
        <h1 class="mt-3 text-3xl font-black tracking-tight text-slate-950 md:text-4xl">Pertanyaan yang sering ditanyakan</h1>
        <p class="mx-auto mt-2 max-w-2xl text-slate-600">Jawaban singkat soal pembelian, pembayaran, akses produk, dan Ruang Akses.</p>
        <div class="mt-4 flex flex-col items-center justify-center gap-3 sm:flex-row">
            <a href="{{ route('products.index') }}" class="rc-btn-primary px-6 py-3 text-sm">
                Lihat Produk
            </a>
            <a href="{{ route('public.order-tracking.index') }}" class="rc-btn-secondary px-6 py-3 text-sm">
                Status Order
            </a>
        </div>
    </div>
</section>

<section class="bg-white py-10 md:py-12">
    <div class="mx-auto max-w-5xl space-y-10 px-6">
        @php
            $faqGroups = [
                'Tentang Ruang Cerdas' => [
                    ['q' => 'Apa itu Ruang Cerdas?', 'a' => 'Ruang Cerdas adalah platform produk digital siap pakai seperti template kerja, ebook, tools AI, dan aplikasi praktis untuk membantu pekerjaan lebih cepat.'],
                    ['q' => 'Produk digital apa saja yang tersedia?', 'a' => 'Kami menyediakan berbagai produk digital seperti template, ebook, file ZIP, prompt AI, dan aplikasi siap pakai sesuai kategori yang tersedia di katalog produk.'],
                ],
                'Cara Pembelian' => [
                    ['q' => 'Bagaimana cara membeli produk?', 'a' => 'Pilih produk di halaman katalog, lanjut ke checkout, isi data pembeli, lalu ikuti instruksi pembayaran manual dan upload bukti pembayaran.'],
                    ['q' => 'Apakah saya perlu membuat akun?', 'a' => 'Saat ini Anda tidak perlu membuat akun. Cukup isi data pembeli yang benar saat checkout.'],
                    ['q' => 'Data apa yang harus saya isi saat checkout?', 'a' => 'Isi nama, email aktif, dan nomor WhatsApp yang dapat dihubungi agar proses verifikasi dan akses Ruang Akses berjalan lancar.'],
                ],
                'Pembayaran Manual' => [
                    ['q' => 'Apakah pembayaran otomatis?', 'a' => 'Belum. Pembayaran diproses manual dan diverifikasi oleh admin setelah Anda mengirim bukti pembayaran.'],
                    ['q' => 'Bagaimana cara upload bukti pembayaran?', 'a' => 'Setelah checkout, buka halaman upload pembayaran dari order Anda, lalu unggah bukti transfer yang jelas.'],
                    ['q' => 'Berapa lama verifikasi pembayaran?', 'a' => 'Verifikasi dilakukan secepat mungkin oleh admin. Waktu proses tergantung antrean verifikasi, biasanya dalam jam operasional.'],
                ],
                'Download Produk' => [
                    ['q' => 'Kapan produk bisa diakses?', 'a' => 'Produk bisa diakses setelah order berstatus paid atau pembayaran disetujui admin.'],
                    ['q' => 'Bagaimana cara membuka produk digital?', 'a' => 'Buka halaman Ruang Akses, lalu masukkan email pembeli dan nomor invoice yang sama seperti saat checkout.'],
                    ['q' => 'Bagaimana jika akses download kedaluwarsa?', 'a' => 'Silakan hubungi admin agar akses download dapat dicek sesuai kebijakan sistem.'],
                    ['q' => 'Apakah file bisa diakses langsung tanpa verifikasi data order?', 'a' => 'Tidak. Akses file hanya dibuka melalui mekanisme sistem yang aman setelah email pembeli dan invoice cocok.'],
                ],
                'Kupon dan Diskon' => [
                    ['q' => 'Bagaimana cara memakai kode kupon?', 'a' => 'Masukkan kode kupon pada form checkout sebelum menyelesaikan order. Jika valid, potongan harga akan dihitung otomatis.'],
                    ['q' => 'Kenapa kode kupon tidak bisa digunakan?', 'a' => 'Kode bisa tidak berlaku karena sudah kedaluwarsa, kuota habis, tidak memenuhi syarat minimum, atau tidak berlaku untuk produk yang dipilih.'],
                ],
                'Bantuan Order' => [
                    ['q' => 'Bagaimana cara melihat status order?', 'a' => 'Gunakan halaman Status Order dengan invoice dan email/WhatsApp yang sama seperti saat checkout.'],
                    ['q' => 'Apa yang harus dilakukan jika email download tidak masuk?', 'a' => 'Periksa folder spam/promosi terlebih dulu. Jika tetap tidak ada, hubungi admin untuk pengecekan dan pengiriman ulang link.'],
                    ['q' => 'Bagaimana jika bukti pembayaran ditolak?', 'a' => 'Periksa alasan penolakan pada status order Anda, lalu unggah ulang bukti yang lebih jelas sesuai instruksi.'],
                ],
            ];
        @endphp

        @foreach ($faqGroups as $groupTitle => $items)
            <div>
                <h2 class="text-2xl font-black text-slate-900 md:text-3xl">{{ $groupTitle }}</h2>
                <div class="mt-4 space-y-3">
                    @foreach ($items as $item)
                        <details class="group rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 shadow-sm">
                            <summary class="flex cursor-pointer list-none items-start justify-between gap-4 pr-1 text-base font-bold text-slate-900">
                                <span>{{ $item['q'] }}</span>
                                <span class="mt-1 text-slate-400 transition group-open:rotate-45">+</span>
                            </summary>
                            <p class="mt-3 border-t border-slate-200 pt-3 text-sm leading-7 text-slate-600">{{ $item['a'] }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</section>

<section class="bg-slate-50 py-10 md:py-12">
    <div class="mx-auto max-w-5xl px-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-8 text-center">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Bantuan</p>
            <h3 class="mt-3 text-3xl font-black text-slate-900 md:text-4xl">Masih butuh bantuan?</h3>
            <p class="mx-auto mt-3 max-w-2xl text-slate-600">
                Cek status order Anda atau hubungi admin Ruang Cerdas jika ada kendala pembayaran atau akses Ruang Akses.
            </p>
            <div class="mt-6 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="{{ route('public.order-tracking.index') }}" class="rc-btn-secondary px-6 py-3 text-sm">
                    Status Order
                </a>
                <a href="{{ route('products.index') }}" class="rc-btn-primary px-6 py-3 text-sm">
                    Lihat Produk
                </a>
                @if (!empty($supportNumber))
                    <a href="https://wa.me/{{ $supportNumber }}" target="_blank" rel="noopener noreferrer" class="rc-btn-success px-6 py-3 text-sm">
                        WhatsApp Admin
                    </a>
                @endif
            </div>
            <div class="mt-5 text-sm">
                <a href="{{ route('public.terms') }}" class="font-semibold text-blue-600 hover:text-blue-700">Syarat & Ketentuan</a>
                <span class="mx-2 text-slate-400">|</span>
                <a href="{{ route('public.privacy') }}" class="font-semibold text-blue-600 hover:text-blue-700">Kebijakan Privasi</a>
            </div>
        </div>
    </div>
</section>
@endsection
