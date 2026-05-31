<footer class="border-t border-slate-200 bg-white">
    <div class="mx-auto max-w-7xl px-6 py-10">
        <div class="grid gap-8 md:grid-cols-3">
            <div>
                <h3 class="text-lg font-bold">Ruang Cerdas</h3>
                <p class="mt-3 text-sm leading-6 text-slate-600">
                    Produk digital, template, aplikasi, dan tools AI untuk kerja lebih cepat dan profesional.
                </p>
            </div>

            <div>
                <h4 class="font-semibold">Link Cepat</h4>
                <ul class="mt-3 space-y-2 text-sm text-slate-600">
                    <li><a href="{{ route('home') }}" class="hover:text-blue-600">Beranda</a></li>
                    <li><a href="{{ route('products.index') }}" class="hover:text-blue-600">Produk</a></li>
                    <li><a href="{{ route('public.order-tracking.index') }}" class="hover:text-blue-600">Cek Order</a></li>
                    <li><a href="{{ route('public.faq') }}" class="hover:text-blue-600">FAQ</a></li>
                    <li><a href="{{ route('public.terms') }}" class="hover:text-blue-600">Syarat & Ketentuan</a></li>
                    <li><a href="{{ route('public.privacy') }}" class="hover:text-blue-600">Kebijakan Privasi</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold">Bantuan</h4>
                <p class="mt-3 text-sm text-slate-600">
                    Butuh bantuan pembelian? Hubungi admin Ruang Cerdas melalui WhatsApp.
                </p>
                <a href="{{ route('public.order-tracking.index') }}" class="mt-3 inline-block text-sm font-semibold text-blue-600 hover:text-blue-700">
                    Cek Order
                </a>
            </div>
        </div>

        <div class="mt-8 border-t border-slate-200 pt-6 text-sm text-slate-500">
            (c) {{ date('Y') }} Ruang Cerdas. All rights reserved.
        </div>
    </div>
</footer>
