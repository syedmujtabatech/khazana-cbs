@extends('layouts.cbs')

@section('title', 'Head Office Banking Cockpit')

@section('content')
<!-- Executive Welcome Banner -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm overflow-hidden" style="background: linear-gradient(135deg, #0B192C 0%, #172A45 50%, #1E3A8A 100%); color: #FFFFFF; border-radius: 14px;">
            <div class="card-body p-4 position-relative">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                            <span class="badge" style="background: rgba(16, 185, 129, 0.2); color: #34D399; border: 1px solid rgba(52, 211, 153, 0.4);">
                                <span class="sbp-dot me-1"></span> SBP PRISM / RTGS Active
                            </span>
                            <span class="badge" style="background: rgba(245, 158, 11, 0.2); color: #FBBF24; border: 1px solid rgba(251, 191, 36, 0.4);">
                                <i class="bx bxs-shield-check me-1"></i> SBP MCR Capitalized: PKR 10 Billion
                            </span>
                            <span class="badge" style="background: rgba(59, 130, 246, 0.2); color: #93C5FD; border: 1px solid rgba(147, 197, 253, 0.4);">
                                1LINK Switch Connected
                            </span>
                        </div>
                        <h2 class="fw-bold text-white mb-1" style="letter-spacing: -0.5px;">Khazana Core Banking Cockpit</h2>
                        <p class="mb-0 text-white-50" style="font-size: 0.95rem; max-width: 680px;">
                            خزانہ کور بینکنگ سسٹم — Real-time double-entry general ledger, SBP Mod-97 IBAN engine, four-eyes dual control, and automated AML/CFT cash surveillance.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                        <div class="d-flex justify-content-lg-end gap-2 flex-wrap">
                            <a href="{{ route('cbs.teller.index') }}" class="btn btn-warning fw-bold px-3 py-2 text-dark shadow-sm">
                                <i class="bx bx-money me-1"></i> Cash Desk (Till)
                            </a>
                            <a href="{{ route('cbs.maker_checker.index') }}" class="btn btn-outline-light px-3 py-2">
                                <i class="bx bx-check-shield me-1"></i> Approvals ({{ $pendingMakerChecker }})
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Primary Financial KPIs (4 Cards) -->
<div class="row g-3 mb-4">
    <!-- Card 1: Total Customer Deposits -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #2563EB !important;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-bold text-uppercase">Customer Deposits</span>
                    <div class="avatar avatar-sm">
                        <span class="avatar-initial rounded-circle bg-label-primary"><i class="bx bx-wallet fs-5"></i></span>
                    </div>
                </div>
                <h4 class="currency-pkr text-primary fw-bold mb-1">
                    PKR {{ number_format($totalDeposits, 2) }}
                </h4>
                <div class="small text-muted d-flex align-items-center">
                    <i class="bx bx-up-arrow-alt text-success me-1"></i> <span>Liability GL 2010/2020 Active</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Vault Cash Holdings -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #10B981 !important;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-bold text-uppercase">Branch Vault Cash</span>
                    <div class="avatar avatar-sm">
                        <span class="avatar-initial rounded-circle bg-label-success"><i class="bx bx-vault fs-5"></i></span>
                    </div>
                </div>
                <h4 class="currency-pkr text-success fw-bold mb-1">
                    PKR {{ number_format($totalVaultCash, 2) }}
                </h4>
                <div class="small text-muted d-flex align-items-center">
                    <i class="bx bx-check-double text-success me-1"></i> <span>Asset GL 1010 Reconciled</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Today's Volume -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #F59E0B !important;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-bold text-uppercase">Today's Settled Volume</span>
                    <div class="avatar avatar-sm">
                        <span class="avatar-initial rounded-circle bg-label-warning"><i class="bx bx-transfer-alt fs-5"></i></span>
                    </div>
                </div>
                <h4 class="currency-pkr text-warning fw-bold mb-1">
                    PKR {{ number_format($todayVolume, 2) }}
                </h4>
                <div class="small text-muted d-flex align-items-center">
                    <i class="bx bx-receipt text-warning me-1"></i> <span>{{ $todayCount }} Cleared Transactions</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: SBP AML & CTR Alerts -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #EF4444 !important;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-bold text-uppercase">SBP AML / CTR Alerts</span>
                    <div class="avatar avatar-sm">
                        <span class="avatar-initial rounded-circle bg-label-danger"><i class="bx bx-shield-quarter fs-5"></i></span>
                    </div>
                </div>
                <h4 class="text-danger fw-bold mb-1">
                    {{ $amlAlertsCount }} <small class="text-muted fs-6">Alerts</small>
                </h4>
                <div class="small text-muted d-flex align-items-center">
                    <i class="bx bx-file-blank text-danger me-1"></i> <span>{{ $ctrReportedCount }} CTRs (&ge; 2M PKR) Logged</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Four-Eyes Authorization Queue -->
    <div class="col-12 col-xl-7 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bx bx-check-shield text-primary me-2"></i>Four-Eyes Maker-Checker Queue
                    </h5>
                    <small class="text-muted">High-value transactions &amp; operations awaiting Checker authorization</small>
                </div>
                <a href="{{ route('cbs.maker_checker.index') }}" class="btn btn-sm btn-outline-primary">
                    View All ({{ $pendingMakerChecker }})
                </a>
            </div>
            <div class="card-body p-0">
                @if($recentMakerRequests->isEmpty())
                    <div class="text-center py-5">
                        <div class="avatar avatar-lg mx-auto mb-2">
                            <span class="avatar-initial rounded-circle bg-label-success"><i class="bx bx-check fs-2"></i></span>
                        </div>
                        <h6 class="fw-bold mb-1">All Operational Tasks Authorized</h6>
                        <p class="text-muted small mb-0">Zero pending requests in Checker review pipeline.</p>
                    </div>
                @else
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Ref / Action</th>
                                    <th>Transaction Amount</th>
                                    <th>Maker Officer</th>
                                    <th>Status</th>
                                    <th>Decision</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentMakerRequests as $req)
                                    <tr>
                                        <td>
                                            <span class="font-monospace fw-bold text-dark d-block">{{ $req->reference_no }}</span>
                                            <span class="badge bg-label-info">{{ ucwords(str_replace('_', ' ', $req->action_type)) }}</span>
                                        </td>
                                        <td class="currency-pkr fw-bold fs-6">
                                            PKR {{ number_format($req->amount, 2) }}
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-dark d-block">{{ $req->maker->name ?? 'System' }}</span>
                                            <small class="text-muted">{{ $req->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-label-warning"><i class="bx bx-time me-1"></i> Awaiting Checker</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('cbs.maker_checker.show', $req->id) }}" class="btn btn-sm btn-primary">
                                                Inspect &amp; Authorize
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

    <!-- Branch Vault Liquidity Summary -->
    <div class="col-12 col-xl-5 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bx bx-buildings text-success me-2"></i>Branch Vault Cash Holdings
                    </h5>
                    <small class="text-muted">GL 1010 Real-time Physical Cash Limits</small>
                </div>
                <span class="badge bg-label-success">SBP Monitored</span>
            </div>
            <div class="card-body p-3">
                <ul class="list-group list-group-flush">
                    @foreach($branches as $b)
                        @php
                            $pct = ($b->vault_cash_limit > 0) ? round(($b->current_vault_cash / $b->vault_cash_limit) * 100) : 0;
                        @endphp
                        <li class="list-group-item px-2 py-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div>
                                    <strong class="text-dark d-block">{{ $b->name }}</strong>
                                    <small class="text-muted font-monospace">Branch Code: {{ $b->code }} &bull; {{ $b->city }}</small>
                                </div>
                                <span class="currency-pkr fw-bold text-dark fs-6">
                                    PKR {{ number_format($b->current_vault_cash, 2) }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center small text-muted my-1">
                                <span>Vault Utilization ({{ $pct }}%)</span>
                                <span>Limit: PKR {{ number_format($b->vault_cash_limit, 0) }}</span>
                            </div>
                            <div class="progress" style="height: 7px; border-radius: 4px;">
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
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bx bx-transfer text-primary me-2"></i>Real-time Core Banking Transaction Feed
                    </h5>
                    <small class="text-muted">Latest cleared financial entries across all channels (Teller, IBFT, Raast, 1BILL)</small>
                </div>
                <a href="{{ route('cbs.gl.journal') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bx bx-book-content me-1"></i> Double-Entry Journal Book
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Transaction Ref</th>
                                <th>Type &amp; Channel</th>
                                <th>Account Involved</th>
                                <th class="text-end">Amount (PKR)</th>
                                <th>Particulars / Narration</th>
                                <th>AML / CTR Status</th>
                                <th>Status</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTransactions as $txn)
                                <tr>
                                    <td>
                                        <span class="font-monospace fw-bold text-dark">{{ $txn->transaction_reference }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-primary">{{ ucwords(str_replace('_', ' ', $txn->transaction_type)) }}</span>
                                        <small class="d-block text-muted">{{ strtoupper($txn->channel) }}</small>
                                    </td>
                                    <td>
                                        @if($txn->destinationAccount)
                                            <span class="fw-semibold text-dark d-block">{{ $txn->destinationAccount->account_title }}</span>
                                            <small class="text-muted font-monospace">{{ $txn->destinationAccount->account_number }}</small>
                                        @elseif($txn->sourceAccount)
                                            <span class="fw-semibold text-dark d-block">{{ $txn->sourceAccount->account_title }}</span>
                                            <small class="text-muted font-monospace">{{ $txn->sourceAccount->account_number }}</small>
                                        @else
                                            <span class="text-muted">Interbank / Counter</span>
                                        @endif
                                    </td>
                                    <td class="text-end currency-pkr fw-bold fs-6">
                                        PKR {{ number_format($txn->amount, 2) }}
                                    </td>
                                    <td>
                                        <span class="text-truncate d-inline-block small" style="max-width: 250px;" title="{{ $txn->narration }}">
                                            {{ $txn->narration }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($txn->ctr_reported)
                                            <span class="badge bg-danger"><i class="bx bx-flag me-1"></i> SBP CTR (&ge;2M)</span>
                                        @elseif($txn->is_aml_flagged)
                                            <span class="badge bg-warning text-dark"><i class="bx bx-error me-1"></i> AML Flagged</span>
                                        @else
                                            <span class="badge bg-label-success"><i class="bx bx-check me-1"></i> Passed</span>
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
