<div class="app-sidebar-menu">
    <div class="h-100" data-simplebar>

        <div id="sidebar-menu">

            <div class="logo-box">

                <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('hando/assets/images/rc/rc_ico.png') }}"
                             alt="Ruang Cerdas"
                             class="rc-logo-image rc-logo-image-sm">
                    </span>

                    <span class="logo-lg">
                        <span class="rc-logo-box">
                            <img src="{{ asset('hando/assets/images/rc/rc_ico.png') }}"
                                 alt="Ruang Cerdas"
                                 class="rc-logo-image rc-logo-image-lg">
                            <span class="rc-logo-text text-white">Ruang Cerdas</span>
                        </span>
                    </span>
                </a>

                <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('hando/assets/images/rc/rc_ico.png') }}"
                             alt="Ruang Cerdas"
                             class="rc-logo-image rc-logo-image-sm">
                    </span>

                    <span class="logo-lg">
                        <span class="rc-logo-box">
                            <img src="{{ asset('hando/assets/images/rc/rc_ico.png') }}"
                                 alt="Ruang Cerdas"
                                 class="rc-logo-image rc-logo-image-lg">
                            <span class="rc-logo-text">Ruang Cerdas</span>
                        </span>
                    </span>
                </a>

            </div>

            <ul id="side-menu">

                <li class="menu-title">Menu</li>

                <li>
                    <a href="{{ route('admin.dashboard') }}"
                       class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i data-feather="home"></i>
                        <span> Dashboard </span>
                    </a>
                </li>

                <li class="menu-title mt-2">Transaksi</li>

                <li>
                    <a href="{{ route('admin.orders.index', ['status' => \App\Models\Order::STATUS_PAYMENT_UPLOADED]) }}"
                       class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                        <i data-feather="shopping-cart"></i>
                        <span> Order Masuk </span>

                        @if (($adminNotificationSummary['total_attention_count'] ?? 0) > 0)
                            <span class="badge bg-danger rounded-pill float-end">
                                {{ $adminNotificationSummary['total_attention_count'] }}
                            </span>
                        @endif
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.payment-settings.edit') }}"
                       class="{{ request()->routeIs('admin.payment-settings.*') ? 'active' : '' }}">
                        <i data-feather="credit-card"></i>
                        <span> Pembayaran </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.coupons.index') }}"
                       class="{{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                        <i data-feather="tag"></i>
                        <span> Kupon </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.reports.index') }}"
                       class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <i data-feather="bar-chart-2"></i>
                        <span> Laporan </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.analytics.products.index') }}"
                       class="{{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                        <i data-feather="trending-up"></i>
                        <span> Analytics Produk </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.activity-logs.index') }}"
                       class="{{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                        <i data-feather="activity"></i>
                        <span> Activity Log </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.customers.index') }}"
                       class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                        <i data-feather="users"></i>
                        <span> Customer </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.landing-settings.edit') }}"
                       class="{{ request()->routeIs('admin.landing-settings.*') ? 'active' : '' }}">
                        <i data-feather="layout"></i>
                        <span> Landing Page </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.testimonials.index') }}"
                       class="{{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}">
                        <i data-feather="message-circle"></i>
                        <span> Testimonial </span>
                    </a>
                </li>

                <li class="menu-title mt-2">Produk Digital</li>

                <li>
                    <a href="#sidebarProducts" data-bs-toggle="collapse">
                        <i data-feather="package"></i>
                        <span> Produk </span>
                        @if (($adminNotificationSummary['missing_product_files_count'] ?? 0) > 0)
                            <span class="badge bg-warning rounded-pill ms-1">
                                {{ $adminNotificationSummary['missing_product_files_count'] }}
                            </span>
                        @endif
                        <span class="menu-arrow"></span>
                    </a>

                    <div class="collapse {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*') ? 'show' : '' }}"
                        id="sidebarProducts">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ route('admin.products.index') }}" class="tp-link">
                                    Semua Produk
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.categories.index') }}" class="tp-link">
                                    Kategori Produk
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="menu-title mt-2">Website</li>

                <li>
                    <a href="{{ url('/') }}" target="_blank" class="tp-link">
                        <i data-feather="globe"></i>
                        <span> Lihat Website </span>
                    </a>
                </li>

            </ul>

        </div>

        <div class="clearfix"></div>

    </div>
</div>
