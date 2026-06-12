<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="{{ asset('hando/assets/images/rc/rc_mark.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('hando/assets/images/rc/rc_mark.svg') }}">
    @include('public.partials.seo')
    @include('public.partials.tracking-head')
    @yield('schema_jsonld')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .rc-btn-primary,
        .rc-btn-secondary,
        .rc-btn-success,
        .rc-btn-neutral {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 1rem;
            font-weight: 700;
            transition: background-color .2s ease, border-color .2s ease, color .2s ease, box-shadow .2s ease;
        }

        .rc-btn-primary {
            background: rgb(251 191 36);
            color: rgb(15 23 42);
            box-shadow: 0 18px 36px -20px rgba(251, 191, 36, .85);
        }

        .rc-btn-primary:hover {
            background: rgb(252 211 77);
        }

        .rc-btn-secondary {
            background: rgb(37 99 235);
            color: #fff;
            box-shadow: 0 18px 36px -20px rgba(37, 99, 235, .55);
        }

        .rc-btn-secondary:hover {
            background: rgb(29 78 216);
        }

        .rc-btn-success {
            background: rgb(5 150 105);
            color: #fff;
            box-shadow: 0 18px 36px -20px rgba(5, 150, 105, .55);
        }

        .rc-btn-success:hover {
            background: rgb(4 120 87);
        }

        .rc-btn-neutral {
            border: 1px solid rgb(203 213 225);
            background: #fff;
            color: rgb(51 65 85);
            box-shadow: 0 10px 24px -22px rgba(15, 23, 42, .35);
        }

        .rc-btn-neutral:hover {
            border-color: rgb(37 99 235);
            color: rgb(37 99 235);
        }

        @media (max-width: 767px) {
            body.has-mobile-sticky-cta {
                padding-bottom: 5.75rem;
            }

            body.has-mobile-sticky-cta .rc-global-whatsapp {
                bottom: 7rem;
            }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased @yield('body_class')">
    @php
        $globalSupportWhatsapp = \App\Models\LandingSetting::query()->value('support_whatsapp');
        $globalSupportMessage = 'Halo Admin Ruang Cerdas, saya butuh bantuan terkait pembelian produk digital.';
        $globalWaUrl = \App\Support\WhatsApp::waMeUrl($globalSupportWhatsapp, $globalSupportMessage);
    @endphp

    @include('public.partials.tracking-body')

    @include('components.public.navbar')

    <main>
        @yield('content')
    </main>

    @include('components.public.footer')

    @if ($globalWaUrl)
        <a href="{{ $globalWaUrl }}"
           target="_blank"
           rel="noopener noreferrer"
           class="rc-global-whatsapp fixed bottom-20 right-4 z-40 inline-flex items-center gap-2 rounded-full bg-green-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-green-700/25 transition hover:bg-green-700 md:bottom-6 md:right-6"
           aria-label="Hubungi WhatsApp support Ruang Cerdas">
            <svg viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5" aria-hidden="true">
                <path d="M20.52 3.48A11.86 11.86 0 0012.06 0C5.4 0 0 5.4 0 12.06c0 2.13.56 4.21 1.62 6.05L0 24l6.07-1.59a11.95 11.95 0 006 1.54H12c6.66 0 12.06-5.4 12.06-12.06 0-3.22-1.25-6.24-3.54-8.41zM12 21.8h-.01a9.9 9.9 0 01-5.04-1.38l-.36-.21-3.6.94.96-3.51-.23-.36a9.86 9.86 0 01-1.54-5.23C2.18 6.58 6.6 2.18 12.06 2.18c2.64 0 5.13 1.03 7 2.91a9.8 9.8 0 012.89 6.97c0 5.46-4.42 9.86-9.95 9.86zm5.41-7.39c-.3-.15-1.76-.87-2.04-.97-.28-.1-.48-.15-.69.15-.2.3-.79.97-.97 1.17-.18.2-.35.22-.65.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.47-1.75-1.64-2.05-.17-.3-.02-.46.13-.61.14-.14.3-.36.45-.54.15-.18.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.69-1.66-.94-2.28-.25-.6-.5-.52-.69-.53h-.59c-.2 0-.52.08-.79.37-.27.3-1.04 1.02-1.04 2.49s1.07 2.9 1.22 3.1c.15.2 2.08 3.18 5.04 4.45.71.3 1.27.49 1.7.62.72.23 1.38.2 1.9.12.58-.09 1.76-.72 2.01-1.42.25-.69.25-1.28.17-1.42-.08-.13-.28-.2-.58-.35z"/>
            </svg>
            <span>Butuh Bantuan?</span>
        </a>
    @endif

    <script>
        window.rcTrack = function (eventName, params) {
            const payload = params || {};

            if (typeof window.fbq === 'function' && window.rcTrackingConfig?.hasMetaPixel) {
                window.fbq('track', eventName, payload);
            }

            if (typeof window.gtag === 'function' && window.rcTrackingConfig?.hasGoogleAnalytics) {
                window.gtag('event', eventName, payload);
            }
        };
    </script>
    @stack('scripts')
</body>
</html>
