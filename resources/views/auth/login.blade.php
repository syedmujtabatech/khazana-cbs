<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Bank Staff & Digital Banking Login - Khazana Core Banking (خزانہ بینک)</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        body { background: linear-gradient(135deg, #0d2847 0%, #173b6c 50%, #1f4287 100%); min-height: 100vh; }
        .card { border-radius: 16px; box-shadow: 0 15px 35px rgba(0,0,0,0.25); }
        .btn-persona { font-size: 0.82rem; font-weight: 500; }
    </style>
</head>

<body>
    <div class="container-xxl py-5">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner row justify-content-center">
                <div class="col-12 col-md-8 col-lg-5">
                    <!-- Login Card -->
                    <div class="card p-4">
                        <div class="card-body">
                            <!-- Logo & Header -->
                            <div class="app-brand justify-content-center mb-4 text-center">
                                <a href="{{ url('/') }}" class="app-brand-link gap-2 text-decoration-none">
                                    <span class="badge bg-primary p-3 rounded-circle shadow-sm">
                                        <i class="bx bxs-bank fs-1 text-white"></i>
                                    </span>
                                </a>
                                <h3 class="fw-bold mt-3 mb-0 text-dark">خزانہ بینکنگ سسٹم</h3>
                                <div class="text-muted small">Khazana Core Banking & Digital Finance Engine</div>
                                <span class="badge bg-label-success mt-2">
                                    <i class="bx bx-check-shield me-1"></i> SBP PRISM / RTGS Compliant
                                </span>
                            </div>

                            @if($errors->any())
                                <div class="alert alert-danger py-2 small">
                                    <i class="bx bx-error-circle me-1"></i> {{ $errors->first() }}
                                </div>
                            @endif

                            <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">Staff Official Email / Customer ID</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                        <input type="text" class="form-control" id="email" name="email" value="{{ old('email', 'admin@khazanabank.pk') }}" placeholder="user@khazanabank.pk" required autofocus />
                                    </div>
                                </div>

                                <div class="mb-3 form-password-toggle">
                                    <div class="d-flex justify-content-between">
                                        <label class="form-label fw-semibold" for="password">Password</label>
                                    </div>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-lock-alt"></i></span>
                                        <input type="password" id="password" class="form-control" name="password" value="Bank@1234" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required />
                                    </div>
                                </div>

                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remember-me" name="remember" checked />
                                        <label class="form-check-label small" for="remember-me"> Keep terminal active </label>
                                    </div>
                                    <span class="badge bg-label-info small">AES-256 Bit</span>
                                </div>

                                <div class="mb-3">
                                    <button class="btn btn-primary d-grid w-100 py-2 fw-bold" type="submit">
                                        <i class="bx bx-log-in me-1"></i> Sign In to Core System
                                    </button>
                                </div>
                            </form>

                            <div class="divider my-4">
                                <div class="divider-text text-uppercase small text-muted">Instant 1-Click Role Access</div>
                            </div>

                            <!-- Fast Persona Switch Buttons -->
                            <div class="row g-2">
                                <div class="col-6">
                                    <a href="{{ route('demo.switch', 'super_admin') }}" class="btn btn-outline-primary w-100 btn-persona text-start py-2">
                                        <i class="bx bx-shield-quarter me-1 text-primary"></i> Super Admin
                                        <div class="text-muted" style="font-size: 0.7rem;">Head Office CTO</div>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('demo.switch', 'branch_manager') }}" class="btn btn-outline-success w-100 btn-persona text-start py-2">
                                        <i class="bx bx-user-check me-1 text-success"></i> Branch Manager
                                        <div class="text-muted" style="font-size: 0.7rem;">Checker Officer</div>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('demo.switch', 'maker_officer') }}" class="btn btn-outline-info w-100 btn-persona text-start py-2">
                                        <i class="bx bx-edit me-1 text-info"></i> Maker Officer
                                        <div class="text-muted" style="font-size: 0.7rem;">Operations Desk</div>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('demo.switch', 'teller') }}" class="btn btn-outline-warning w-100 btn-persona text-start py-2">
                                        <i class="bx bx-money me-1 text-warning"></i> Cashier / Teller
                                        <div class="text-muted" style="font-size: 0.7rem;">Cash Deposit/Wdl</div>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('demo.switch', 'compliance_officer') }}" class="btn btn-outline-danger w-100 btn-persona text-start py-2">
                                        <i class="bx bx-shield me-1 text-danger"></i> AML Officer
                                        <div class="text-muted" style="font-size: 0.7rem;">SBP CTR/STR Audit</div>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('demo.switch', 'customer_bilal') }}" class="btn btn-outline-secondary w-100 btn-persona text-start py-2">
                                        <i class="bx bx-laptop me-1 text-dark"></i> Customer Portal
                                        <div class="text-muted" style="font-size: 0.7rem;">Internet Banking</div>
                                    </a>
                                </div>
                            </div>

                            <div class="text-center mt-4 small text-muted">
                                Default Password for all demo accounts: <code>Bank@1234</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
