<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('hando/assets/images/rc/rc_ico.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('hando/assets/images/rc/rc_ico.png') }}">
    @include('public.partials.seo')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 antialiased">

    @include('components.public.navbar')

    <main>
        @yield('content')
    </main>

    @include('components.public.footer')

</body>
</html>
