<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ruang Cerdas')</title>

    <meta name="description" content="@yield('meta_description', 'Produk digital, template, aplikasi, dan tools AI untuk kerja lebih cepat dan profesional.')">

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