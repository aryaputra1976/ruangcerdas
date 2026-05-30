<div class="topbar-custom">
    <div class="container-fluid">
        <div class="d-flex justify-content-between">

            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">
                <li>
                    <button class="button-toggle-menu nav-link">
                        <i data-feather="menu" class="noti-icon"></i>
                    </button>
                </li>

                <li class="d-none d-lg-block">
                    <h5 class="mb-0">
                        Admin Ruang Cerdas
                    </h5>
                </li>
            </ul>

            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">

                <li class="d-none d-sm-flex">
                    <button type="button" class="btn nav-link" data-toggle="fullscreen">
                        <i data-feather="maximize" class="align-middle fullscreen noti-icon"></i>
                    </button>
                </li>

                <li class="d-none d-sm-flex">
                    <button type="button" class="btn nav-link" id="light-dark-mode">
                        <i data-feather="moon" class="align-middle dark-mode"></i>
                        <i data-feather="sun" class="align-middle light-mode"></i>
                    </button>
                </li>

                <li class="dropdown notification-list topbar-dropdown">
                    <a class="nav-link dropdown-toggle nav-user me-0"
                       data-bs-toggle="dropdown"
                       href="#"
                       role="button"
                       aria-haspopup="false"
                       aria-expanded="false">

                        <span class="avatar-sm rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </span>

                        <span class="pro-user-name ms-1">
                            {{ auth()->user()->name ?? 'Admin' }}
                            <i class="mdi mdi-chevron-down"></i>
                        </span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-end profile-dropdown">

                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">
                                Selamat datang!
                            </h6>
                        </div>

                        @if (Route::has('profile.edit'))
                            <a href="{{ route('profile.edit') }}" class="dropdown-item notify-item">
                                <i class="mdi mdi-account-circle-outline fs-16 align-middle"></i>
                                <span>Profil Saya</span>
                            </a>
                        @endif

                        <a href="{{ url('/') }}" target="_blank" class="dropdown-item notify-item">
                            <i class="mdi mdi-web fs-16 align-middle"></i>
                            <span>Lihat Website</span>
                        </a>

                        <div class="dropdown-divider"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button type="submit" class="dropdown-item notify-item text-danger">
                                <i class="mdi mdi-location-exit fs-16 align-middle"></i>
                                <span>Logout</span>
                            </button>
                        </form>

                    </div>
                </li>

            </ul>

        </div>
    </div>
</div>