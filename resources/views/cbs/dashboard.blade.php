@extends('layouts.cbs')

@section('title', 'Head Office Banking Cockpit')

@section('content')
<div class="row">
    <!-- Welcome Banner & SBP Regulatory Status -->
    <div class="col-12 mb-4">
        <div class="card bg-primary text-white shadow-sm overflow-hidden" style="background: linear-gradient(135deg, #0d2847 0%, #1a4f8b 100%) !important;">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-success me-2 px-3 py-2"><i class="bx bx-check-double me-1"></i> SBP PRISM Operational</span>
                            <span class="badge bg-warning text-dark px-3 py-2"><i class="bx bx-buildings me-1"></i> SBP MCR Capitalized: PKR 10 Billion</span>
                        </div>
                        <h3 class="fw-bold text-white mb-1">Khazana Core Banking Engine (خزانہ کور سسٹم)</h3>
                        <p class="mb-0 text-white-50">
                            Enterprise-grade general ledger with Mod-97 IBAN engine, four-eyes maker-checker dual authorization, and SBP AML/CFT continuous monitoring.
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <a href="{{ route('cbs.teller.index') }}" class="btn btn-warning fw-bold me-2">
                            <i class="bx bx-money me-1"></i> Teller Counter
                        </a>
                        <a href="{{ route('cbs.maker_checker.index') }}" class="btn btn-outline-light">
                            <i class="bx bx-check-shield me-1"></i> Authorizations ({{ $pendingMakerChecker }})
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Key Performance Indicators (KPI Cards) -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-primary h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-wallet fs-4"></i></span>
                    </div>
                    <h5 class="ms-1 mb-0 currency-pkr">PKR {{ number_format($totalDeposits, 2) }}</h5>
                </div>
                <p class="mb-1 text-muted small fw-semibold">Total Customer Deposits</p>
                <p class="mb-0 text-success small">
                    <i class="bx bx-up-arrow-alt"></i> <span>Liability GL 2010/2020 Active</span>
                </p>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-success h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-success"><i class="bx bx-vault fs-4"></i></span>
                    </div>
                    <h5 class="ms-1 mb-0 currency-pkr">PKR {{ number_format($totalVaultCash, 2) }}</h5>
                </div>
                <p class="mb-1 text-muted small fw-semibold">Physical Cash in Vaults</p>
                <p class="mb-0 text-muted small">
                    <i class="bx bx-check"></i> <span>Asset GL 1010 Reconciled</span>
                </p>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-warning h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-transfer-alt fs-4"></i></span>
                    </div>
                    <h5 class="ms-1 mb-0 currency-pkr">PKR {{ number_format($todayVolume, 2) }}</h5>
                </div>
                <p class="mb-1 text-muted small fw-semibold">Today's Settled Volume</p>
                <p class="mb-0 text-muted small">
                    <i class="bx bx-list-check"></i> <span>{{ $todayCount }} Financial Transactions</span>
                </p>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card card-border-shadow-danger h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-shield-quarter fs-4"></i></span>
                    </div>
                    <h5 class="ms-1 mb-0">{{ $amlAlertsCount }} <small class="text-muted fs-6">Alerts</small></h5>
                </div>
                <p class="mb-1 text-muted small fw-semibold">SBP AML & CTR Reports</p>
                <p class="mb-0 text-danger small">
                    <i class="bx bx-bell"></i> <span>{{ $ctrReportedCount }} CTRs (&ge; 2M PKR) Logged</span>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Live Pending Authorizations (Maker-Checker) -->
    <div class="col-12 col-lg-7 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between pb-0">
                <div class="card-title mb-0">
                    <h5 class="m-0 me-2 fw-bold"><i class="bx bx-check-shield text-primary me-2"></i>Four-Eyes Authorization Queue</h5>
                    <small class="text-muted">High-value transactions & operations requiring Checker approval</small>
                </div>
                <a href="{{ route('cbs.maker_checker.index') }}" class="btn btn-sm btn-outline-primary">View All ({{ $pendingMakerChecker }})</a>
            </div>
            <div class="card-body mt-3">
                @if($recentMakerRequests->isEmpty())
                    <div class="text-center py-5">
                        <i class="bx bx-check-circle text-success fs-1"></i>
                        <p class="text-muted mt-2">All operational requests have been verified. Zero pending maker-checker backlogs.</p>
                    </div>
                @else
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Ref / Action</th>
                                    <th>Amount</th>
                                    <th>Maker Officer</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentMakerRequests as $req)
                                    <tr>
                                        <td>
                                            <span class="fw-semibold text-dark d-block">{{ $req->reference_no }}</span>
                                            <span class="badge bg-label-info">{{ ucwords(str_replace('_', ' ', $req->action_type)) }}</span>
                                        </td>
                                        <td class="currency-pkr fw-bold">
                                            PKR {{ number_format($req->amount, 2) }}
                                        </td>
                                        <td>
                                            <span class="d-block">{{ $req->maker->name ?? 'System' }}</span>
                                            <small class="text-muted">{{ $req->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-label-warning">Awaiting Checker</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('cbs.maker_checker.show', $req->id) }}" class="btn btn-xs btn-primary">
                                                Inspect & Authorize
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Commercial Branch Vault Summary -->
    <div class="col-12 col-lg-5 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between pb-0">
                <h5 class="card-title m-0 fw-bold"><i class="bx bx-buildings text-success me-2"></i>Branch Vault Cash Holdings</h5>
                <span class="badge bg-label-success">Real-time SBP Limit</span>
            </div>
            <div class="card-body mt-3">
                <ul class="list-group list-group-flush">
                    @foreach($branches as $b)
                        @php
                            $pct = ($b->vault_cash_limit > 0) ? round(($b->current_vault_cash / $b->vault_cash_limit) * 100) : 0;
                        @endphp
                        <li class="list-group-item px-0 py-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div>
                                    <span class="fw-bold">{{ $b->name }}</span>
                                    <span class="badge bg-label-secondary ms-1">Code: {{ $b->code }}</span>
                                </div>
                                <span class="fw-bold currency-pkr">PKR {{ number_format($b->current_vault_cash, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center small text-muted mb-1">
                                <span>Vault Utilization ({{ $pct }}%)</span>
                                <span>Limit: PKR {{ number_format($b->vault_cash_limit, 0) }}</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar {{ $pct > 80 ? 'bg-danger' : ($pct > 50 ? 'bg-warning' : 'bg-success') }}" role="progressbar" style="width: {{ $pct }}%"></div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Real-time Financial Ledger Feed -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title m-0 fw-bold"><i class="bx bx-transfer text-primary me-2"></i>Real-time Core Banking Transaction Feed</h5>
                <a href="{{ route('cbs.gl.journal') }}" class="btn btn-sm btn-outline-secondary">View Double-Entry Journal</a>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Transaction Ref</th>
                                <th>Type & Channel</th>
                                <th>Account Involved</th>
                                <th>Amount</th>
                                <th>Narration</th>
                                <th>AML / CTR Status</th>
                                <th>Status</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTransactions as $txn)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-dark">{{ $txn->transaction_reference }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-primary">{{ ucwords(str_replace('_', ' ', $txn->transaction_type)) }}</span>
                                        <small class="d-block text-muted">{{ strtoupper($txn->channel) }}</small>
                                    </td>
                                    <td>
                                        @if($txn->destinationAccount)
                                            <span class="d-block text-dark fw-semibold">{{ $txn->destinationAccount->account_title }}</span>
                                            <small class="text-muted">{{ $txn->destinationAccount->account_number }}</small>
                                        @elseif($txn->sourceAccount)
                                            <span class="d-block text-dark fw-semibold">{{ $txn->sourceAccount->account_title }}</span>
                                            <small class="text-muted">{{ $txn->sourceAccount->account_number }}</small>
                                        @else
                                            <span class="text-muted">Interbank / Counter</span>
                                        @endif
                                    </td>
                                    <td class="currency-pkr fw-bold">
                                        PKR {{ number_format($txn->amount, 2) }}
                                    </td>
                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width: 250px;" title="{{ $txn->narration }}">
                                            {{ $txn->narration }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($txn->ctr_reported)
                                            <span class="badge bg-danger"><i class="bx bx-flag me-1"></i> SBP CTR (&ge;2M)</span>
                                        @elseif($txn->is_aml_flagged)
                                            <span class="badge bg-warning text-dark"><i class="bx bx-error me-1"></i> AML Flagged</span>
                                        @else
                                            <span class="badge bg-label-success">Clean / Passed</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $txn->status_badge_class }}">{{ ucfirst($txn->status) }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $txn->created_at->format('d M, h:i A') }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
