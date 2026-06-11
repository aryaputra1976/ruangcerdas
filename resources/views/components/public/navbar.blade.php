<header class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 backdrop-blur" data-mobile-nav>
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-6 py-3">
        <div class="flex items-center gap-8 lg:gap-10">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <img
                    src="{{ asset('hando/assets/images/rc/rc_ico.png') }}"
                    alt="Ruang Cerdas Logo"
                    class="h-9 w-9 rounded-xl object-cover"
                >
                <div>
                    <p class="text-base font-bold leading-none">Ruang Cerdas</p>
                    <p class="text-xs text-slate-500">Produk Digital</p>
                </div>
            </a>

            <nav class="hidden items-center gap-4 md:flex lg:gap-5">
                <a href="{{ route('products.index') }}" class="text-sm font-medium text-slate-700 hover:text-blue-600">
                    Produk
                </a>
                <a href="{{ route('public.tryouts.hub') }}" class="text-sm font-medium text-slate-700 hover:text-blue-600">
                    Tryout
                </a>
                <a href="{{ route('lead-magnets.index') }}" class="text-sm font-medium text-slate-700 hover:text-blue-600">
                    Panduan Gratis
                </a>
                <a href="{{ route('public.orders.lookup') }}" class="text-sm font-medium text-slate-700 hover:text-blue-600">
                    Cek Order
                </a>
                <a href="{{ route('public.faq') }}" class="text-sm font-medium text-slate-700 hover:text-blue-600">
                    FAQ
                </a>
            </nav>
        </div>

        <a href="{{ route('products.index') }}" class="hidden rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 md:inline-flex">
            Lihat Produk
        </a>

        <button
            type="button"
            class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white p-3 text-slate-700 shadow-sm transition hover:border-blue-200 hover:text-blue-600 md:hidden"
            aria-label="Buka menu navigasi"
            aria-expanded="false"
            aria-controls="public-mobile-menu"
            data-mobile-nav-toggle
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                <path d="M4 7h16"></path>
                <path d="M4 12h16"></path>
                <path d="M4 17h16"></path>
            </svg>
        </button>
    </div>

    <div
        id="public-mobile-menu"
        class="hidden border-t border-slate-200 bg-white md:hidden"
        data-mobile-nav-menu
    >
        <div class="mx-auto flex max-w-7xl flex-col gap-2 px-6 py-4">
            <a href="{{ route('products.index') }}" class="rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-blue-600">
                Produk
            </a>
            <a href="{{ route('public.tryouts.hub') }}" class="rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-blue-600">
                Tryout
            </a>
            <a href="{{ route('lead-magnets.index') }}" class="rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-blue-600">
                Panduan Gratis
            </a>
            <a href="{{ route('public.orders.lookup') }}" class="rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-blue-600">
                Cek Order
            </a>
            <a href="{{ route('public.faq') }}" class="rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-blue-600">
                FAQ
            </a>
            <a href="{{ route('products.index') }}" class="mt-2 inline-flex items-center justify-center rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                Lihat Produk
            </a>
        </div>
    </div>
</header>
