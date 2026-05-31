<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>{{ $title ?? 'Admin Ruang Cerdas' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Ruang Cerdas Admin Panel">
    <meta name="author" content="Ruang Cerdas">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Favicon --}}
    <link rel="shortcut icon" href="{{ asset('hando/assets/images/rc/rc_ico.png') }}">

    {{-- Hando App CSS --}}
    <link href="{{ asset('hando/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />

    {{-- Hando Icons --}}
    <link href="{{ asset('hando/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

    {{-- Hando Head JS --}}
    <script src="{{ asset('hando/assets/js/head.js') }}"></script>

    <style>
        .logo-box .logo-lg span,
        .logo-box .logo-sm span {
            color: #2563eb;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .rc-logo-box {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .rc-logo-image {
            object-fit: contain;
            display: inline-block;
        }

        .rc-logo-image-sm {
            width: 42px;
            height: 42px;
            border-radius: 12px;
        }

        .rc-logo-image-lg {
            width: 42px;
            height: 42px;
            border-radius: 12px;
        }

        .rc-logo-text {
            font-weight: 800;
            color: #0f172a;
            font-size: 17px;
        }

        .rc-page-subtitle {
            color: #64748b;
            font-size: 14px;
            margin-top: 4px;
        }

        .rc-dashboard-card {
            border-radius: 16px;
        }

        .btn svg {
            vertical-align: middle;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .table-card {
            border-radius: 14px;
        }

        .table-card .table {
            margin-bottom: 0;
        }

        .table-card thead th {
            font-size: 13px;
            font-weight: 700;
            color: #475569;
            white-space: nowrap;
        }

        .table-card tbody td {
            font-size: 14px;
        }

        .rc-action-btn {
            min-width: 82px;
            justify-content: center;
        }        

        .btn svg {
            vertical-align: middle;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .table-card {
            border-radius: 14px;
        }

        .table-card .table {
            margin-bottom: 0;
        }

        .table-card thead th {
            font-size: 13px;
            font-weight: 700;
            color: #475569;
            white-space: nowrap;
        }

        .table-card tbody td {
            font-size: 14px;
        }

        .rc-action-btn {
            min-width: 82px;
            justify-content: center;
        }

        .card {
            border-radius: 16px;
        }

        .card-header {
            border-top-left-radius: 16px !important;
            border-top-right-radius: 16px !important;
        }        
    </style>

    @stack('styles')
</head>

<body data-menu-color="light" data-sidebar="default">

    <div id="app-layout">

        {{-- Topbar --}}
        <x-admin.header />

        {{-- Sidebar --}}
        <x-admin.sidebar />

        {{-- Page Content --}}
        <div class="content-page">
            <div class="content">

                <div class="container-fluid">

                    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                        <div class="flex-grow-1">
                            <h4 class="fs-18 fw-semibold m-0">
                                {{ $title ?? 'Dashboard' }}
                            </h4>

                            @isset($subtitle)
                                <div class="rc-page-subtitle">
                                    {{ $subtitle }}
                                </div>
                            @endisset
                        </div>

                        @isset($actions)
                            <div class="mt-3 mt-sm-0">
                                {{ $actions }}
                            </div>
                        @endisset
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                        </div>
                    @endif

                    @yield('content')

                </div>

            </div>

            <x-admin.footer />
        </div>

    </div>

    {{-- Vendor JS sesuai index Hando --}}
    <script src="{{ asset('hando/assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('hando/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('hando/assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('hando/assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('hando/assets/libs/waypoints/lib/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('hando/assets/libs/jquery.counterup/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('hando/assets/libs/feather-icons/feather.min.js') }}"></script>

    {{-- Hando App JS --}}
    <script src="{{ asset('hando/assets/js/app.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.feather) {
                feather.replace();
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
