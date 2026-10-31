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

    <!-- Core Sneat CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- Khazana Enterprise Banking Luxury Theme -->
    <link rel="stylesheet" href="{{ asset('assets/css/banking-theme.css') }}" />

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
            <aside id="layout-menu" class="layout-menu menu-vertical menu">
                <div class="app-brand demo py-3 px-3 d-flex align-items-center">
                    <a href="{{ route('portal.dashboard') }}" class="app-brand-link d-flex align-items-center text-decoration-none">
                        <div class="bank-crest me-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2L2 7V9H22V7L12 2Z" fill="white"/>
                                <path d="M4 11H7V18H4V11Z" fill="white" fill-opacity="0.9"/>
                                <path d="M10.5 11H13.5V18H10.5V11Z" fill="white" fill-opacity="0.9"/>
                                <path d="M17 11H20V18H17V11Z" fill="white" fill-opacity="0.9"/>
                                <path d="M2 19H22V22H2V19Z" fill="white"/>
                                <circle cx="12" cy="5.5" r="1.2" fill="#F59E0B"/>
                            </svg>
                        </div>
                        <div>
                            <div class="bank-brand-title">KHAZANA BANK</div>
                            <small class="bank-brand-sub"><span class="bank-brand-urdu">خزانہ ڈیجیٹل</span> &bull; Customer Portal</small>
                        </div>
                    </a>
                </div>

                <ul class="menu-inner py-2">
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

                    <li class="menu-header">
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

                    <li class="menu-header">
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

                    <li class="menu-header">
                        <span class="menu-header-text">Staff Control</span>
                    </li>

                    <li class="menu-item">
                        <a href="{{ route('cbs.dashboard') }}" class="menu-link text-warning fw-bold">
                            <i class="menu-icon tf-icons bx bx-arrow-back text-warning"></i>
                            <div>Back to CBS Admin &larr;</div>
                        </a>
                    </li>
                </ul>
            </aside>
            <!-- / Sidebar -->

            <!-- Layout page -->
            <div class="layout-page">
                <!-- Navbar -->
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4 text-dark" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center justify-content-between w-100" id="navbar-collapse">
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center flex-wrap gap-2">
                                <span class="sbp-live-badge">
                                    <span class="sbp-dot"></span> 256-bit SBP Encrypted Session
                                </span>
                                @if(auth()->user()->customerProfile)
                                    <span class="branch-badge d-none d-md-inline-flex">
                                        <i class="bx bx-id-card me-1 text-primary"></i> CIF: {{ auth()->user()->customerProfile->cif_number }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- Quick Switcher -->
                            <li class="nav-item dropdown me-3">
                                <button type="button" class="btn btn-role-switcher dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bx bx-transfer-alt me-1 fs-5"></i> Switch Profile
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2" style="min-width: 280px; border-radius: 12px;">
                                    <li class="dropdown-header text-uppercase small fw-bold text-muted px-3 py-1">Customer Profiles</li>
                                    <li><a class="dropdown-item rounded py-2 px-3" href="{{ route('demo.switch', 'customer_bilal') }}"><i class="bx bx-laptop me-2 text-info fs-5"></i> <strong>Bilal Farooq</strong><div class="small text-muted ps-4">Software Architect</div></a></li>
                                    <li><a class="dropdown-item rounded py-2 px-3" href="{{ route('demo.switch', 'customer_tariq') }}"><i class="bx bx-briefcase me-2 text-primary fs-5"></i> <strong>Tariq Mehmood</strong><div class="small text-muted ps-4">Business Current A/C</div></a></li>
                                    <li><a class="dropdown-item rounded py-2 px-3" href="{{ route('demo.switch', 'customer_pep') }}"><i class="bx bx-star me-2 text-danger fs-5"></i> <strong>Senator Malik Aslam</strong><div class="small text-muted ps-4">PEP High Net Worth</div></a></li>
                                    <li><hr class="dropdown-divider my-2"></li>
                                    <li class="dropdown-header text-uppercase small fw-bold text-muted px-3 py-1">Bank Staff Access</li>
                                    <li><a class="dropdown-item rounded py-2 px-3" href="{{ route('demo.switch', 'super_admin') }}"><i class="bx bx-shield-quarter me-2 text-danger fs-5"></i> <strong>Return to Super Admin CBS</strong></a></li>
                                </ul>
                            </li>

                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow d-flex align-items-center" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online me-2">
                                        <div class="avatar-initial rounded-circle bg-success text-white fw-bold shadow-sm">
                                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                        </div>
                                    </div>
                                    <div class="d-none d-lg-block text-start">
                                        <span class="fw-bold d-block text-dark small lh-1">{{ auth()->user()->name }}</span>
                                        <small class="badge bg-label-success" style="font-size: 0.68rem;">Customer</small>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2" style="border-radius: 12px; min-width: 200px;">
                                    <li class="px-3 py-2 border-bottom mb-2">
                                        <span class="fw-bold d-block text-dark">{{ auth()->user()->name }}</span>
                                        <small class="text-muted">{{ auth()->user()->email }}</small>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item rounded text-danger py-2">
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
                            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert" style="border-left: 4px solid #10B981 !important;">
                                <i class="bx bx-check-circle me-1 fs-5 align-middle"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert" style="border-left: 4px solid #EF4444 !important;">
                                <i class="bx bx-error-circle me-1 fs-5 align-middle"></i> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @yield('content')
                    </div>

                    <footer class="content-footer footer bg-footer-theme border-top mt-4">
                        <div class="container-xxl d-flex flex-wrap justify-content-between py-3 flex-md-row flex-column align-items-center">
                            <div class="mb-2 mb-md-0 text-muted small">
                                &copy; 2026 <strong>Khazana Bank Limited</strong>. Protected by State Bank of Pakistan Deposit Protection Corporation (DPC).
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
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    @stack('scripts')
</body>
</html>
