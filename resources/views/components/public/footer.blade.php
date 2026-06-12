<footer class="border-t border-slate-200 bg-white">
    @php
        $footerSupportMessage = 'Halo Admin Ruang Cerdas, saya butuh bantuan terkait pembelian produk digital.';
        $footerWaUrl = \App\Support\WhatsApp::waMeUrl(\App\Models\LandingSetting::query()->value('support_whatsapp'), $footerSupportMessage);
    @endphp
    <div class="mx-auto max-w-7xl px-6 py-8 md:py-10">
        <div class="grid gap-8 md:grid-cols-2 xl:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)_minmax(0,0.8fr)_minmax(0,1fr)]">
            <div>
                <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Ruang Cerdas</p>
                <h3 class="mt-3 text-lg font-bold">Produk digital praktis untuk belajar, kerja, dan seleksi ASN</h3>
                <p class="mt-3 text-sm leading-6 text-slate-600">
                    eBook, checklist, template, dan panduan siap pakai untuk belajar lebih terarah dan administrasi kerja yang lebih rapi.
                </p>
            </div>

            <div>
                <h4 class="text-sm font-bold uppercase tracking-widest text-slate-500">Jelajahi</h4>
                <ul class="mt-3 space-y-2 text-sm text-slate-600">
                    <li><a href="{{ route('home') }}" class="hover:text-blue-600">Beranda</a></li>
                    <li><a href="{{ route('products.index') }}" class="hover:text-blue-600">Produk</a></li>
                    <li><a href="{{ route('public.tryouts.hub') }}" class="hover:text-blue-600">Tryout</a></li>
                    <li><a href="{{ route('lead-magnets.index') }}" class="hover:text-blue-600">Panduan Gratis</a></li>
                    <li><a href="{{ route('articles.index') }}" class="hover:text-blue-600">Artikel</a></li>
                    <li><a href="{{ route('public.faq') }}" class="hover:text-blue-600">FAQ</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-sm font-bold uppercase tracking-widest text-slate-500">Setelah Beli</h4>
                <ul class="mt-3 space-y-2 text-sm text-slate-600">
                    <li><a href="{{ route('public.orders.lookup') }}" class="hover:text-blue-600">Status Order</a></li>
                    <li><a href="{{ route('public.download-room.index') }}" class="hover:text-blue-600">Ruang Akses</a></li>
                    <li><a href="{{ route('public.terms') }}" class="hover:text-blue-600">Syarat & Ketentuan</a></li>
                    <li><a href="{{ route('public.privacy') }}" class="hover:text-blue-600">Kebijakan Privasi</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-sm font-bold uppercase tracking-widest text-slate-500">Bantuan</h4>
                <p class="mt-3 text-sm text-slate-600">
                    Butuh bantuan pembelian atau akses produk? Hubungi admin Ruang Cerdas melalui WhatsApp.
                </p>
                <div class="mt-4 grid gap-2">
                    <a href="{{ route('public.orders.lookup') }}" class="rc-btn-neutral px-4 py-2.5 text-sm">
                        Status Order
                    </a>
                    <a href="{{ route('public.download-room.index') }}" class="rc-btn-success px-4 py-2.5 text-sm">
                        Ruang Akses
                    </a>
                    @if ($footerWaUrl)
                        <a href="{{ $footerWaUrl }}" target="_blank" rel="noopener noreferrer" class="rc-btn-success px-4 py-2.5 text-sm">
                            WhatsApp Support
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-8 border-t border-slate-200 pt-5 text-sm text-slate-500">
            (c) {{ date('Y') }} Ruang Cerdas. All rights reserved.
        </div>
    </div>
</footer>
