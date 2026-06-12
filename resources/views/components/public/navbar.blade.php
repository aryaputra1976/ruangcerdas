@php
    $isRouteActive = function (array $patterns): bool {
        foreach ($patterns as $pattern) {
            if (request()->routeIs($pattern)) {
                return true;
            }
        }

        return false;
    };

    $navLinkClass = function (bool $active, bool $compact = false): string {
        $base = $compact
            ? 'rounded-xl px-3 py-2 text-sm font-semibold transition'
            : 'rounded-xl px-3 py-2 text-sm font-semibold transition';

        return $active
            ? $base . ' bg-blue-50 text-blue-700'
            : $base . ' text-slate-700 hover:bg-slate-50 hover:text-blue-600';
    };
@endphp

<header class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 backdrop-blur" data-mobile-nav>
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-2 md:px-6">
        <div class="flex items-center gap-4 lg:gap-6">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <img
                    src="{{ asset('hando/assets/images/rc/rc_mark.svg') }}"
                    alt="Ruang Cerdas Logo"
                    class="h-8 w-8 rounded-xl object-cover md:h-9 md:w-9"
                    width="36"
                    height="36"
                >
                <div>
                    <p class="text-sm font-bold leading-none md:text-base">Ruang Cerdas</p>
                    <p class="text-xs text-slate-500">Produk Digital</p>
                </div>
            </a>

            <nav class="hidden items-center gap-0.5 md:flex">
                <a href="{{ route('products.index') }}" class="{{ $navLinkClass($isRouteActive(['products.*'])) }}">
                    Produk
                </a>
                <a href="{{ route('public.tryouts.hub') }}" class="{{ $navLinkClass($isRouteActive(['public.tryouts.*'])) }}">
                    Tryout
                </a>
                <a href="{{ route('lead-magnets.index') }}" class="{{ $navLinkClass($isRouteActive(['lead-magnets.*'])) }}">
                    Panduan Gratis
                </a>
                <span class="mx-1 hidden h-5 w-px bg-slate-200 lg:block" aria-hidden="true"></span>
                <a href="{{ route('public.orders.lookup') }}" class="{{ $navLinkClass($isRouteActive(['public.orders.*', 'public.order-tracking.*'])) }}">
                    Status Order
                </a>
                <a href="{{ route('public.download-room.index') }}" class="{{ $navLinkClass($isRouteActive(['public.download-room.*'])) }}">
                    Ruang Akses
                </a>
                <a href="{{ route('public.faq') }}" class="{{ $navLinkClass($isRouteActive(['public.faq'])) }}">
                    FAQ
                </a>
            </nav>
        </div>

        <a href="{{ route('products.index') }}" class="rc-btn-secondary hidden px-4 py-2.5 text-sm md:inline-flex">
            Lihat Produk
        </a>

        <button
            type="button"
            class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white p-2.5 text-slate-700 shadow-sm transition hover:border-blue-200 hover:text-blue-600 md:hidden"
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
        <div class="mx-auto flex max-w-7xl flex-col gap-2 px-5 py-4">
            <p class="px-4 pt-1 text-xs font-bold uppercase tracking-widest text-slate-400">Jelajahi</p>
            <a href="{{ route('products.index') }}" class="{{ $navLinkClass($isRouteActive(['products.*'])) }}">
                Produk
            </a>
            <a href="{{ route('public.tryouts.hub') }}" class="{{ $navLinkClass($isRouteActive(['public.tryouts.*'])) }}">
                Tryout
            </a>
            <a href="{{ route('lead-magnets.index') }}" class="{{ $navLinkClass($isRouteActive(['lead-magnets.*'])) }}">
                Panduan Gratis
            </a>
            <p class="mt-2 px-4 pt-1 text-xs font-bold uppercase tracking-widest text-slate-400">Setelah Beli</p>
            <a href="{{ route('public.orders.lookup') }}" class="{{ $navLinkClass($isRouteActive(['public.orders.*', 'public.order-tracking.*'])) }}">
                Status Order
            </a>
            <a href="{{ route('public.download-room.index') }}" class="{{ $navLinkClass($isRouteActive(['public.download-room.*'])) }}">
                Ruang Akses
            </a>
            <a href="{{ route('public.faq') }}" class="{{ $navLinkClass($isRouteActive(['public.faq'])) }}">
                FAQ
            </a>
            <a href="{{ route('products.index') }}" class="rc-btn-secondary mt-2 px-4 py-3 text-sm">
                Lihat Produk
            </a>
        </div>
    </div>
</header>
