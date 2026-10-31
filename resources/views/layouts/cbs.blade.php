<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>@yield('title', 'Cockpit') - Khazana Core Banking (خزانہ بینک)</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" />

    <!-- Core Sneat CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- Khazana Enterprise Banking Luxury Theme -->
    <link rel="stylesheet" href="{{ asset('assets/css/banking-theme.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>

    @stack('styles')
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Luxury Midnight Navy Sidebar -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu">
                <!-- Premium Bank Crest Brand -->
                <div class="app-brand demo py-3 px-3 d-flex align-items-center">
                    <a href="{{ route('cbs.dashboard') }}" class="app-brand-link d-flex align-items-center text-decoration-none">
                        <!-- Luxury Gold Bank SVG Emblem -->
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
                            <small class="bank-brand-sub"><span class="bank-brand-urdu">خزانہ بینک</span> &bull; CORE CBS</small>
                        </div>
                    </a>
                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none text-white">
                        <i class="bx bx-chevron-left bx-sm align-middle"></i>
                    </a>
                </div>

                <ul class="menu-inner py-2">
                    <!-- Core Cockpit -->
                    <li class="menu-item {{ request()->routeIs('cbs.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('cbs.dashboard') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-tachometer"></i>
                            <div>CBS Head Office Cockpit</div>
                        </a>
                    </li>

                    <li class="menu-header">
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

                    <li class="menu-header">
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
                                <span class="badge bg-danger rounded-pill ms-auto px-2">{{ $pendingMc }}</span>
                            @endif
                        </a>
                    </li>

                    <li class="menu-header">
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

                    <li class="menu-header">
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
                            <div>CTR Reports (&ge;2M PKR)</div>
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

                    <li class="menu-header">
                        <span class="menu-header-text">Day End Processing</span>
                    </li>

                    <!-- EOD Batch -->
                    <li class="menu-item {{ request()->routeIs('cbs.eod.*') ? 'active' : '' }}">
                        <a href="{{ route('cbs.eod.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-refresh"></i>
                            <div>End of Day (EOD) Batch</div>
                        </a>
                    </li>

                    <li class="menu-header">
                        <span class="menu-header-text">Digital Banking</span>
                    </li>

                    <li class="menu-item">
                        <a href="{{ route('portal.dashboard') }}" class="menu-link text-warning fw-bold" target="_blank">
                            <i class="menu-icon tf-icons bx bx-laptop text-warning"></i>
                            <div>Internet Banking &nearr;</div>
                        </a>
                    </li>
                </ul>
            </aside>
            <!-- / Sidebar -->

            <!-- Layout page -->
            <div class="layout-page">
                <!-- Top Navbar -->
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4 text-dark" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center justify-content-between w-100" id="navbar-collapse">
                        <!-- SBP RTGS Status & Branch -->
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center flex-wrap gap-2">
                                <span class="sbp-live-badge">
                                    <span class="sbp-dot"></span> SBP PRISM / RTGS Operational
                                </span>
                                <span class="branch-badge d-none d-md-inline-flex">
                                    <i class="bx bxs-business me-1 text-primary"></i> {{ auth()->user()->branch->name ?? 'Karachi Corporate Main Branch' }} ({{ auth()->user()->branch->code ?? '0010' }})
                                </span>
                            </div>
                        </div>

                        <!-- Right Options & Role Switcher -->
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- Fast Role Switcher Dropdown -->
                            <li class="nav-item dropdown me-3">
                                <button type="button" class="btn btn-role-switcher dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bx bx-transfer-alt me-1 fs-5"></i> Switch Role
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2" style="min-width: 280px; border-radius: 12px;">
                                    <li class="dropdown-header text-uppercase small fw-bold text-muted px-3 py-1">Bank Staff Access</li>
                                    <li><a class="dropdown-item rounded py-2 px-3" href="{{ route('demo.switch', 'super_admin') }}"><i class="bx bx-shield-quarter me-2 text-primary fs-5"></i> <strong>Super Admin / CTO</strong><div class="small text-muted ps-4">Head Office Governance</div></a></li>
                                    <li><a class="dropdown-item rounded py-2 px-3" href="{{ route('demo.switch', 'branch_manager') }}"><i class="bx bx-user-check me-2 text-success fs-5"></i> <strong>Branch Manager</strong><div class="small text-muted ps-4">Checker Authorization Desk</div></a></li>
                                    <li><a class="dropdown-item rounded py-2 px-3" href="{{ route('demo.switch', 'maker_officer') }}"><i class="bx bx-edit-alt me-2 text-info fs-5"></i> <strong>Maker Officer</strong><div class="small text-muted ps-4">Operations & CIF Creation</div></a></li>
                                    <li><a class="dropdown-item rounded py-2 px-3" href="{{ route('demo.switch', 'teller') }}"><i class="bx bx-money me-2 text-warning fs-5"></i> <strong>Cashier / Teller</strong><div class="small text-muted ps-4">Till Deposit & Dispense</div></a></li>
                                    <li><a class="dropdown-item rounded py-2 px-3" href="{{ route('demo.switch', 'compliance_officer') }}"><i class="bx bx-shield me-2 text-danger fs-5"></i> <strong>AML Compliance Officer</strong><div class="small text-muted ps-4">SBP CTR/STR Surveillance</div></a></li>
                                    <li><hr class="dropdown-divider my-2"></li>
                                    <li class="dropdown-header text-uppercase small fw-bold text-muted px-3 py-1">Customer Internet Banking</li>
                                    <li><a class="dropdown-item rounded py-2 px-3" href="{{ route('demo.switch', 'customer_bilal') }}"><i class="bx bx-laptop me-2 text-info fs-5"></i> <strong>Bilal Farooq</strong><div class="small text-muted ps-4">Digital Tech Customer</div></a></li>
                                    <li><a class="dropdown-item rounded py-2 px-3" href="{{ route('demo.switch', 'customer_tariq') }}"><i class="bx bx-briefcase me-2 text-primary fs-5"></i> <strong>Tariq Mehmood</strong><div class="small text-muted ps-4">Commercial Business Current</div></a></li>
                                    <li><a class="dropdown-item rounded py-2 px-3" href="{{ route('demo.switch', 'customer_pep') }}"><i class="bx bx-star me-2 text-danger fs-5"></i> <strong>Senator Malik Aslam</strong><div class="small text-muted ps-4">High Net Worth PEP Account</div></a></li>
                                </ul>
                            </li>

                            <!-- User Avatar & Logout -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow d-flex align-items-center" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online me-2">
                                        <div class="avatar-initial rounded-circle bg-primary text-white fw-bold shadow-sm">
                                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                        </div>
                                    </div>
                                    <div class="d-none d-lg-block text-start">
                                        <span class="fw-bold d-block text-dark small lh-1">{{ auth()->user()->name }}</span>
                                        <small class="badge {{ auth()->user()->role_badge_class }}" style="font-size: 0.68rem;">{{ ucwords(str_replace('_', ' ', auth()->user()->role)) }}</small>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2" style="border-radius: 12px; min-width: 220px;">
                                    <li class="px-3 py-2 border-bottom mb-2">
                                        <span class="fw-bold d-block text-dark">{{ auth()->user()->name }}</span>
                                        <small class="text-muted">{{ auth()->user()->email }}</small>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item rounded text-danger py-2">
                                                <i class="bx bx-power-off me-2"></i> Log Out of CBS
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
                <!-- / Top Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <!-- Flash alerts -->
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

                        @if(session('info'))
                            <div class="alert alert-info alert-dismissible fade show shadow-sm border-0" role="alert" style="border-left: 4px solid #3B82F6 !important;">
                                <i class="bx bx-info-circle me-1 fs-5 align-middle"></i> {{ session('info') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('warning'))
                            <div class="alert alert-warning alert-dismissible fade show shadow-sm border-0" role="alert" style="border-left: 4px solid #F59E0B !important;">
                                <i class="bx bx-alert me-1 fs-5 align-middle"></i> {{ session('warning') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @yield('content')
                    </div>

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme border-top mt-4">
                        <div class="container-xxl d-flex flex-wrap justify-content-between py-3 flex-md-row flex-column align-items-center">
                            <div class="mb-2 mb-md-0 text-muted small">
                                &copy; 2026 <strong>Khazana Core Banking System (خزانہ بینک)</strong>. Licensed for Financial Institutions &amp; SBP Compliance.
                            </div>
                            <div>
                                <span class="badge bg-label-secondary">Double-Entry Core v2.4</span>
                                <span class="badge bg-label-success ms-1">ISO 7064 Mod-97</span>
                                <span class="badge bg-label-primary ms-1">1LINK Member</span>
                            </div>
                        </div>
                    </footer>
                </div>
            </div>
        </div>

        <div class="layout-overlay layout-menu-toggle"></div>
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
