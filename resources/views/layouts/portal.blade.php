<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>@yield('title', 'Internet Banking') - Khazana Digital Banking (خزانہ ڈیجیٹل)</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        .portal-header { background: linear-gradient(135deg, #071e3d 0%, #1f4287 100%); color: #fff; }
        .currency-pkr { font-family: 'Public Sans', monospace; font-weight: 700; }
        .card-atm {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: #fff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 10px 25px rgba(30, 60, 114, 0.3);
            position: relative;
            overflow: hidden;
        }
        .card-atm::after {
            content: '';
            position: absolute;
            right: -50px;
            bottom: -50px;
            width: 180px;
            height: 180px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }
        .chip {
            width: 48px;
            height: 36px;
            background: linear-gradient(135deg, #fce043 0%, #fb7ba2 100%);
            border-radius: 6px;
            display: inline-block;
        }
    </style>
    @stack('styles')
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Sidebar -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo py-3">
                    <a href="{{ route('portal.dashboard') }}" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <span class="badge bg-success p-2 me-2">
                                <i class="bx bx-shield-quarter fs-4 text-white"></i>
                            </span>
                        </span>
                        <span class="app-brand-text demo menu-text fw-bolder ms-2 text-dark">خزانہ ڈیجیٹل</span>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">
                    <li class="menu-item {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('portal.dashboard') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div>Accounts & Balance</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('portal.statement') ? 'active' : '' }}">
                        <a href="{{ route('portal.statement') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-receipt"></i>
                            <div>Account Statement</div>
                        </a>
                    </li>

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Payments & Transfers</span>
                    </li>

                    <li class="menu-item {{ request()->routeIs('portal.transfer') ? 'active' : '' }}">
                        <a href="{{ route('portal.transfer') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-send"></i>
                            <div>Instant Funds Transfer</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('portal.bill_payment') ? 'active' : '' }}">
                        <a href="{{ route('portal.bill_payment') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-bulb"></i>
                            <div>1BILL Utility Payments</div>
                        </a>
                    </li>

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Self-Service Banking</span>
                    </li>

                    <li class="menu-item {{ request()->routeIs('portal.cards') ? 'active' : '' }}">
                        <a href="{{ route('portal.cards') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-credit-card"></i>
                            <div>Debit Card Controls</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('portal.cheque_books') ? 'active' : '' }}">
                        <a href="{{ route('portal.cheque_books') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-book-bookmark"></i>
                            <div>Cheque Book Services</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('portal.loan_simulator') ? 'active' : '' }}">
                        <a href="{{ route('portal.loan_simulator') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-calculator"></i>
                            <div>Consumer Loan Calculator</div>
                        </a>
                    </li>

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Staff Control</span>
                    </li>

                    <li class="menu-item">
                        <a href="{{ route('cbs.dashboard') }}" class="menu-link text-info fw-bold">
                            <i class="menu-icon tf-icons bx bx-arrow-back"></i>
                            <div>Back to CBS Admin &larr;</div>
                        </a>
                    </li>
                </ul>
            </aside>
            <!-- / Sidebar -->

            <!-- Layout page -->
            <div class="layout-page">
                <!-- Navbar -->
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center justify-content-between w-100" id="navbar-collapse">
                        <div class="navbar-nav align-items-center">
                            <span class="badge bg-label-success py-2">
                                <i class="bx bx-lock-alt me-1"></i> 256-bit SBP Encrypted Session
                            </span>
                            @if(auth()->user()->customerProfile)
                                <span class="badge bg-label-primary ms-2 py-2 d-none d-md-inline-block">
                                    CIF: {{ auth()->user()->customerProfile->cif_number }}
                                </span>
                            @endif
                        </div>

                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- Quick Switcher -->
                            <li class="nav-item dropdown me-3">
                                <a class="nav-link dropdown-toggle hide-arrow btn btn-sm btn-outline-success px-3" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <i class="bx bx-transfer-alt me-1"></i> Switch Profile
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                    <li class="dropdown-header text-uppercase small fw-bold">Customer Profiles</li>
                                    <li><a class="dropdown-item" href="{{ route('demo.switch', 'customer_bilal') }}"><i class="bx bx-user me-2 text-primary"></i> Bilal Farooq (Software Architect)</a></li>
                                    <li><a class="dropdown-item" href="{{ route('demo.switch', 'customer_tariq') }}"><i class="bx bx-user me-2 text-success"></i> Tariq Mehmood (Business Current)</a></li>
                                    <li><a class="dropdown-item" href="{{ route('demo.switch', 'customer_pep') }}"><i class="bx bx-star me-2 text-danger"></i> Senator Malik Aslam (PEP Account)</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li class="dropdown-header text-uppercase small fw-bold">Bank Staff Access</li>
                                    <li><a class="dropdown-item" href="{{ route('demo.switch', 'super_admin') }}"><i class="bx bx-shield-quarter me-2 text-danger"></i> Return to Super Admin CBS</a></li>
                                </ul>
                            </li>

                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <div class="avatar-initial rounded-circle bg-label-success fw-bold">
                                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                        </div>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <div class="dropdown-item">
                                            <span class="fw-semibold d-block">{{ auth()->user()->name }}</span>
                                            <small class="text-muted">Digital Banking Customer</small>
                                        </div>
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
                    <div class="container-xxl flex-grow-1 container-p-y">
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

                        @yield('content')
                    </div>

                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0">
                                © 2026 <strong>Khazana Bank Limited</strong>. Protected by State Bank of Pakistan Deposit Protection Corporation (DPC).
                            </div>
                            <div>
                                <span class="badge bg-label-primary">1LINK Member</span>
                                <span class="badge bg-label-success ms-1">Raast Enabled</span>
                            </div>
                        </div>
                    </footer>
                </div>
            </div>
        </div>
    </div>

    <!-- Core JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    @stack('scripts')
</body>
</html>
