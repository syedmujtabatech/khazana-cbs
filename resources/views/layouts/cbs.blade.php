<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>@yield('title', 'Cockpit') - Khazana Core Banking System (خزانہ بینک)</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>

    <style>
        .bank-badge { font-family: 'Public Sans', sans-serif; font-weight: 600; letter-spacing: 0.5px; }
        .currency-pkr { font-family: 'Public Sans', monospace; font-weight: 600; }
        .bg-bank-navy { background-color: #0d2847 !important; }
        .menu-inner .menu-item.active > .menu-link { background: linear-gradient(72.47deg, #0d2847 0%, #1a4f8b 100%) !important; box-shadow: 0 4px 10px rgba(13, 40, 71, 0.3); }
        .app-brand-text { font-size: 1.25rem; font-weight: 700; color: #0d2847 !important; }
        .security-stamp { border-left: 3px solid #28a745; padding-left: 10px; }
    </style>
    @stack('styles')
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu Sidebar -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo py-3">
                    <a href="{{ route('cbs.dashboard') }}" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <span class="badge bg-primary p-2 me-2">
                                <i class="bx bxs-bank fs-4 text-white"></i>
                            </span>
                        </span>
                        <span class="app-brand-text demo menu-text fw-bolder ms-2">خزانہ CBS</span>
                    </a>
                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                        <i class="bx bx-chevron-left bx-sm align-middle"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">
                    <!-- Core Cockpit -->
                    <li class="menu-item {{ request()->routeIs('cbs.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('cbs.dashboard') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-tachometer"></i>
                            <div>CBS Head Office Cockpit</div>
                        </a>
                    </li>

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Banking Operations</span>
                    </li>

                    <!-- Customer Master -->
                    <li class="menu-item {{ request()->routeIs('cbs.customers.*') ? 'active' : '' }}">
                        <a href="{{ route('cbs.customers.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-id-card"></i>
                            <div>Customer Master (CIF)</div>
                        </a>
                    </li>

                    <!-- Accounts Engine -->
                    <li class="menu-item {{ request()->routeIs('cbs.accounts.*') ? 'active' : '' }}">
                        <a href="{{ route('cbs.accounts.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-credit-card-front"></i>
                            <div>Accounts & SBP IBAN</div>
                        </a>
                    </li>

                    <!-- Teller Cash Counter -->
                    <li class="menu-item {{ request()->routeIs('cbs.teller.*') ? 'active' : '' }}">
                        <a href="{{ route('cbs.teller.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-dollar-circle"></i>
                            <div>Teller Cash Desk</div>
                        </a>
                    </li>

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Four-Eyes Governance</span>
                    </li>

                    <!-- Maker-Checker Queue -->
                    <li class="menu-item {{ request()->routeIs('cbs.maker_checker.*') ? 'active' : '' }}">
                        <a href="{{ route('cbs.maker_checker.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-check-shield"></i>
                            <div>Maker-Checker Queue</div>
                            @php
                                $pendingMc = \App\Models\MakerCheckerRequest::where('status', 'pending')->count();
                            @endphp
                            @if($pendingMc > 0)
                                <div class="badge bg-danger rounded-pill ms-auto">{{ $pendingMc }}</div>
                            @endif
                        </a>
                    </li>

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">General Ledger (GL)</span>
                    </li>

                    <!-- Chart of Accounts -->
                    <li class="menu-item {{ request()->routeIs('cbs.gl.coa') ? 'active' : '' }}">
                        <a href="{{ route('cbs.gl.coa') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-sitemap"></i>
                            <div>Chart of Accounts (COA)</div>
                        </a>
                    </li>

                    <!-- Trial Balance -->
                    <li class="menu-item {{ request()->routeIs('cbs.gl.trial_balance') ? 'active' : '' }}">
                        <a href="{{ route('cbs.gl.trial_balance') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-scale"></i>
                            <div>Real-time Trial Balance</div>
                        </a>
                    </li>

                    <!-- Journal Audit -->
                    <li class="menu-item {{ request()->routeIs('cbs.gl.journal') ? 'active' : '' }}">
                        <a href="{{ route('cbs.gl.journal') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-book-content"></i>
                            <div>GL Journal Book</div>
                        </a>
                    </li>

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">SBP Compliance & Risk</span>
                    </li>

                    <!-- AML / CFT -->
                    <li class="menu-item {{ request()->routeIs('cbs.compliance.index') ? 'active' : '' }}">
                        <a href="{{ route('cbs.compliance.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-shield-quarter"></i>
                            <div>AML / CFT Cockpit</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('cbs.compliance.ctr') ? 'active' : '' }}">
                        <a href="{{ route('cbs.compliance.ctr') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-file-blank"></i>
                            <div>CTR Reports (&gt;2M PKR)</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('cbs.compliance.pep') ? 'active' : '' }}">
                        <a href="{{ route('cbs.compliance.pep') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-user-voice"></i>
                            <div>PEP & High Risk Matrix</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('cbs.compliance.audit') ? 'active' : '' }}">
                        <a href="{{ route('cbs.compliance.audit') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-history"></i>
                            <div>Forensic Security Trail</div>
                        </a>
                    </li>

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Day End Processing</span>
                    </li>

                    <!-- EOD Batch -->
                    <li class="menu-item {{ request()->routeIs('cbs.eod.*') ? 'active' : '' }}">
                        <a href="{{ route('cbs.eod.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-refresh"></i>
                            <div>End of Day (EOD) Batch</div>
                        </a>
                    </li>

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Digital Channels</span>
                    </li>

                    <li class="menu-item">
                        <a href="{{ route('portal.dashboard') }}" class="menu-link text-primary fw-bold" target="_blank">
                            <i class="menu-icon tf-icons bx bx-globe"></i>
                            <div>Internet Banking Portal &nearr;</div>
                        </a>
                    </li>
                </ul>
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center justify-content-between w-100" id="navbar-collapse">
                        <!-- SBP RTGS System Indicator -->
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center">
                                <span class="badge bg-label-success me-2 py-2">
                                    <span class="badge badge-dot bg-success me-1"></span> SBP RTGS / PRISM Online
                                </span>
                                <span class="badge bg-label-primary py-2 d-none d-md-inline-block">
                                    <i class="bx bx-buildings me-1"></i> {{ auth()->user()->branch->name ?? 'Karachi Corporate Main' }}
                                </span>
                            </div>
                        </div>

                        <!-- Right navbar options & Fast Switcher -->
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- Fast Demo Switcher -->
                            <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3">
                                <a class="nav-link dropdown-toggle hide-arrow btn btn-sm btn-outline-primary px-3" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <i class="bx bx-transfer-alt me-1"></i> Switch Role
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                    <li class="dropdown-header text-uppercase small fw-bold">Executive / Operations</li>
                                    <li><a class="dropdown-item" href="{{ route('demo.switch', 'super_admin') }}"><i class="bx bx-shield-quarter me-2 text-primary"></i> Super Admin / CTO</a></li>
                                    <li><a class="dropdown-item" href="{{ route('demo.switch', 'branch_manager') }}"><i class="bx bx-user-check me-2 text-success"></i> Branch Manager (Checker)</a></li>
                                    <li><a class="dropdown-item" href="{{ route('demo.switch', 'maker_officer') }}"><i class="bx bx-edit-alt me-2 text-info"></i> Operations Officer (Maker)</a></li>
                                    <li><a class="dropdown-item" href="{{ route('demo.switch', 'teller') }}"><i class="bx bx-money me-2 text-warning"></i> Cash Desk Teller</a></li>
                                    <li><a class="dropdown-item" href="{{ route('demo.switch', 'compliance_officer') }}"><i class="bx bx-shield me-2 text-danger"></i> AML Compliance Officer</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li class="dropdown-header text-uppercase small fw-bold">Customer Internet Banking</li>
                                    <li><a class="dropdown-item" href="{{ route('demo.switch', 'customer_bilal') }}"><i class="bx bx-laptop me-2 text-info"></i> Customer Bilal Farooq</a></li>
                                    <li><a class="dropdown-item" href="{{ route('demo.switch', 'customer_tariq') }}"><i class="bx bx-briefcase me-2 text-primary"></i> Customer Tariq Mehmood</a></li>
                                    <li><a class="dropdown-item" href="{{ route('demo.switch', 'customer_pep') }}"><i class="bx bx-star me-2 text-danger"></i> Senator Malik Aslam (PEP)</a></li>
                                </ul>
                            </li>

                            <!-- User Profile Dropdown -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <div class="avatar-initial rounded-circle bg-label-primary fw-bold">
                                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                        </div>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <div class="avatar-initial rounded-circle bg-label-primary fw-bold">
                                                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="fw-semibold d-block">{{ auth()->user()->name }}</span>
                                                    <small class="badge {{ auth()->user()->role_badge_class }}">{{ ucwords(str_replace('_', ' ', auth()->user()->role)) }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li><div class="dropdown-divider"></div></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bx bx-power-off me-2"></i> Log Out
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <!-- Flash messages -->
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                                <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                                <i class="bx bx-error-circle me-1"></i> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('info'))
                            <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
                                <i class="bx bx-info-circle me-1"></i> {{ session('info') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('warning'))
                            <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
                                <i class="bx bx-alert me-1"></i> {{ session('warning') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @yield('content')
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0">
                                © 2026 <strong>Khazana Core Banking System (خزانہ CBS)</strong>. Licensed for Financial Institutions & SBP Regulatory Compliance.
                            </div>
                            <div>
                                <span class="badge bg-label-secondary">Double-Entry Core v2.4</span>
                                <span class="badge bg-label-success ms-1">ISO 7064 Mod-97</span>
                            </div>
                        </div>
                    </footer>
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>

    @stack('scripts')
</body>
</html>
