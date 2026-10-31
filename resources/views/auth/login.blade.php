<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Bank Staff & Digital Banking Login - Khazana Core Banking (خزانہ بینک)</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/banking-theme.css') }}" />

    <style>
        body { 
            background: linear-gradient(135deg, #0B192C 0%, #0F2038 50%, #081220 100%) !important; 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card { 
            border-radius: 16px !important; 
            box-shadow: 0 20px 45px rgba(0,0,0,0.4) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            background: #FFFFFF !important;
        }
        .btn-persona { 
            font-size: 0.8rem; 
            font-weight: 500;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .btn-persona:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .login-crest {
            width: 58px;
            height: 58px;
            background: linear-gradient(135deg, #D97706 0%, #F59E0B 50%, #B45309 100%);
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(245, 158, 11, 0.4);
            border: 2px solid rgba(255, 255, 255, 0.4);
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-9 col-lg-6 col-xl-5">
                <!-- Login Card -->
                <div class="card login-card p-3 p-sm-4">
                    <div class="card-body">
                        <!-- Luxury Logo & Header -->
                        <div class="text-center mb-4">
                            <div class="login-crest mb-3">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 2L2 7V9H22V7L12 2Z" fill="white"/>
                                    <path d="M4 11H7V18H4V11Z" fill="white" fill-opacity="0.9"/>
                                    <path d="M10.5 11H13.5V18H10.5V11Z" fill="white" fill-opacity="0.9"/>
                                    <path d="M17 11H20V18H17V11Z" fill="white" fill-opacity="0.9"/>
                                    <path d="M2 19H22V22H2V19Z" fill="white"/>
                                    <circle cx="12" cy="5.5" r="1.2" fill="#F59E0B"/>
                                </svg>
                            </div>
                            <h3 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">KHAZANA BANK</h3>
                            <div class="text-muted small fw-semibold">خزانہ کور بینکنگ سسٹم &bull; Enterprise CBS Platform</div>
                            <div class="mt-2">
                                <span class="badge" style="background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; font-size: 0.72rem;">
                                    <i class="bx bx-check-shield me-1"></i> State Bank of Pakistan PRISM / RTGS Active
                                </span>
                            </div>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger py-2 small mb-3">
                                <i class="bx bx-error-circle me-1"></i> {{ $errors->first() }}
                            </div>
                        @endif

                        <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold small text-dark">Staff Email / Customer ID</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bx bx-envelope"></i></span>
                                    <input type="text" class="form-control" id="email" name="email" value="{{ old('email', 'admin@khazanabank.pk') }}" placeholder="user@khazanabank.pk" required autofocus />
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-dark" for="password">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bx bx-lock-alt"></i></span>
                                    <input type="password" id="password" class="form-control" name="password" value="Bank@1234" placeholder="••••••••••••" required />
                                </div>
                            </div>

                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember-me" name="remember" checked />
                                    <label class="form-check-label small text-muted" for="remember-me"> Keep session open </label>
                                </div>
                                <span class="badge bg-label-info small">256-Bit SSL</span>
                            </div>

                            <div class="mb-3">
                                <button class="btn btn-primary d-grid w-100 py-2 fw-bold shadow-sm" type="submit" style="background: linear-gradient(135deg, #1E3A8A 0%, #2563EB 100%); border: none;">
                                    <i class="bx bx-log-in me-1"></i> Sign In to Core Banking
                                </button>
                            </div>
                        </form>

                        <div class="text-center my-3 position-relative">
                            <hr class="my-0">
                            <span class="px-2 small text-muted bg-white position-relative" style="top: -10px; font-weight: 600;">INSTANT 1-CLICK ROLE ACCESS</span>
                        </div>

                        <!-- Fast Persona Buttons -->
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="{{ route('demo.switch', 'super_admin') }}" class="btn btn-persona w-100 text-start p-2 d-flex align-items-center">
                                    <div class="avatar avatar-xs me-2">
                                        <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-shield-quarter"></i></span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark lh-1" style="font-size: 0.78rem;">Super Admin</div>
                                        <small class="text-muted" style="font-size: 0.65rem;">Head Office CTO</small>
                                    </div>
                                </a>
                            </div>

                            <div class="col-6">
                                <a href="{{ route('demo.switch', 'branch_manager') }}" class="btn btn-persona w-100 text-start p-2 d-flex align-items-center">
                                    <div class="avatar avatar-xs me-2">
                                        <span class="avatar-initial rounded bg-label-success"><i class="bx bx-user-check"></i></span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark lh-1" style="font-size: 0.78rem;">Manager</div>
                                        <small class="text-muted" style="font-size: 0.65rem;">Checker Desk</small>
                                    </div>
                                </a>
                            </div>

                            <div class="col-6">
                                <a href="{{ route('demo.switch', 'maker_officer') }}" class="btn btn-persona w-100 text-start p-2 d-flex align-items-center">
                                    <div class="avatar avatar-xs me-2">
                                        <span class="avatar-initial rounded bg-label-info"><i class="bx bx-edit"></i></span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark lh-1" style="font-size: 0.78rem;">Maker Officer</div>
                                        <small class="text-muted" style="font-size: 0.65rem;">Operations</small>
                                    </div>
                                </a>
                            </div>

                            <div class="col-6">
                                <a href="{{ route('demo.switch', 'teller') }}" class="btn btn-persona w-100 text-start p-2 d-flex align-items-center">
                                    <div class="avatar avatar-xs me-2">
                                        <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-money"></i></span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark lh-1" style="font-size: 0.78rem;">Cashier / Teller</div>
                                        <small class="text-muted" style="font-size: 0.65rem;">Till Counter</small>
                                    </div>
                                </a>
                            </div>

                            <div class="col-6">
                                <a href="{{ route('demo.switch', 'compliance_officer') }}" class="btn btn-persona w-100 text-start p-2 d-flex align-items-center">
                                    <div class="avatar avatar-xs me-2">
                                        <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-shield"></i></span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark lh-1" style="font-size: 0.78rem;">AML Officer</div>
                                        <small class="text-muted" style="font-size: 0.65rem;">SBP CTR &amp; STR</small>
                                    </div>
                                </a>
                            </div>

                            <div class="col-6">
                                <a href="{{ route('demo.switch', 'customer_bilal') }}" class="btn btn-persona w-100 text-start p-2 d-flex align-items-center">
                                    <div class="avatar avatar-xs me-2">
                                        <span class="avatar-initial rounded bg-label-secondary"><i class="bx bx-laptop"></i></span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark lh-1" style="font-size: 0.78rem;">Customer Bilal</div>
                                        <small class="text-muted" style="font-size: 0.65rem;">Internet Banking</small>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="text-center mt-3 small text-muted" style="font-size: 0.75rem;">
                            Default Credentials: <code>admin@khazanabank.pk</code> / <code>Bank@1234</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
</body>
</html>
