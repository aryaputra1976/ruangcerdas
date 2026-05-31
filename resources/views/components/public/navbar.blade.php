<header class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-600 text-white font-bold">
                RC
            </div>
            <div>
                <p class="text-base font-bold leading-none">Ruang Cerdas</p>
                <p class="text-xs text-slate-500">Digital products & AI tools</p>
            </div>
        </a>

        <nav class="hidden items-center gap-8 md:flex">
            <a href="{{ route('home') }}" class="text-sm font-medium text-slate-700 hover:text-blue-600">
                Beranda
            </a>
            <a href="{{ route('products.index') }}" class="text-sm font-medium text-slate-700 hover:text-blue-600">
                Produk
            </a>
            <a href="{{ route('public.order-tracking.index') }}" class="text-sm font-medium text-slate-700 hover:text-blue-600">
                Cek Order
            </a>
            <a href="#cara-beli" class="text-sm font-medium text-slate-700 hover:text-blue-600">
                Cara Beli
            </a>
            <a href="#faq" class="text-sm font-medium text-slate-700 hover:text-blue-600">
                FAQ
            </a>
        </nav>

        <a href="{{ route('products.index') }}" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
            Lihat Produk
        </a>
    </div>
</header>
