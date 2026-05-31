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
                <h4 class="font-semibold">Produk</h4>
                <ul class="mt-3 space-y-2 text-sm text-slate-600">
                    <li>Template Kantor</li>
                    <li>Template Excel</li>
                    <li>Prompt AI</li>
                    <li>Aplikasi Siap Pakai</li>
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
            © {{ date('Y') }} Ruang Cerdas. All rights reserved.
        </div>
    </div>
</footer>
