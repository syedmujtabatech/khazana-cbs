@extends('layouts.cbs')

@section('title', 'AML / CFT Compliance Cockpit')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-danger px-2 py-1"><i class="bx bx-shield-quarter me-1"></i> SBP SURVEILLANCE</span>
            <h4 class="fw-bold mb-0 text-dark">AML / CFT Regulatory Cockpit</h4>
        </div>
        <p class="text-muted small mb-0">Anti-Money Laundering &amp; Combating Financing of Terrorism Continuous Monitoring Engine</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('cbs.compliance.ctr') }}" class="btn btn-danger fw-bold shadow-sm">
            <i class="bx bx-file-blank me-1"></i> CTR Reports (&ge;2M PKR)
        </a>
        <a href="{{ route('cbs.compliance.pep') }}" class="btn btn-outline-danger fw-semibold">
            <i class="bx bx-user-voice me-1"></i> PEP Registry
        </a>
    </div>
</div>

<!-- Risk Metrics Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #EF4444 !important;">
            <div class="card-body p-3">
                <span class="text-muted small fw-bold text-uppercase">SBP CTR Disclosures (&ge; 2M PKR)</span>
                <h3 class="text-danger fw-bold my-1">{{ $ctrCount }} <small class="text-muted fs-6">Reports</small></h3>
                <small class="text-muted"><i class="bx bx-check text-success"></i> Disclosed to Financial Monitoring Unit (FMU)</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #F59E0B !important;">
            <div class="card-body p-3">
                <span class="text-muted small fw-bold text-uppercase">Surveillance Flags &amp; Velocity</span>
                <h3 class="text-warning fw-bold my-1">{{ $totalFlagged }} <small class="text-muted fs-6">Alerts</small></h3>
                <small class="text-muted"><i class="bx bx-pulse text-warning"></i> 24h Aggregation Limit: PKR 5,000,000</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #2563EB !important;">
            <div class="card-body p-3">
                <span class="text-muted small fw-bold text-uppercase">Politically Exposed Persons (PEP)</span>
                <h3 class="text-primary fw-bold my-1">{{ $pepCount }} <small class="text-muted fs-6">Profiles</small></h3>
                <small class="text-muted"><i class="bx bx-star text-primary"></i> Mandatory Enhanced Due Diligence (EDD)</small>
            </div>
        </div>
    </div>
</div>

<!-- Flagged Transactions Table -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="card-title mb-0 fw-bold text-dark"><i class="bx bx-error text-danger me-2"></i>Active AML Surveillance Flags</h5>
            <small class="text-muted">Filtered by real-time risk heuristics and SBP Circular limits</small>
        </div>
        <span class="badge bg-danger px-3 py-2">{{ $flaggedTransactions->count() }} Triggered</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Transaction Ref</th>
                        <th>Account / Customer</th>
                        <th class="text-end">Amount (PKR)</th>
                        <th>Rule Triggered / AML Reason</th>
                        <th>Report Type</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($flaggedTransactions as $txn)
                        @php
                            $acc = $txn->destinationAccount ?? $txn->sourceAccount;
                            $cust = $acc ? $acc->customer : null;
                        @endphp
                        <tr>
                            <td>
                                <span class="fw-bold font-monospace text-dark d-block">{{ $txn->transaction_reference }}</span>
                                <small class="text-muted">{{ ucwords(str_replace('_', ' ', $txn->transaction_type)) }}</small>
                            </td>
                            <td>
                                @if($acc)
                                    <span class="fw-bold text-dark d-block">{{ $acc->account_title }}</span>
                                    <small class="text-muted font-monospace">A/C: {{ $acc->account_number }}</small>
                                    @if($cust && $cust->is_pep)
                                        <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">PEP</span>
                                    @endif
                                @else
                                    <span class="text-muted">Cash Counter Slip</span>
                                @endif
                            </td>
                            <td class="text-end currency-pkr fw-bold text-danger fs-6">
                                PKR {{ number_format($txn->amount, 2) }}
                            </td>
                            <td>
                                <span class="badge bg-label-danger text-wrap text-start lh-base py-2" style="max-width: 380px; font-weight: 500;">
                                    {{ $txn->aml_reason ?? 'Surveillance Threshold Exceeded' }}
                                </span>
                            </td>
                            <td>
                                @if($txn->ctr_reported)
                                    <span class="badge bg-danger"><i class="bx bx-flag me-1"></i> CTR Reported</span>
                                @else
                                    <span class="badge bg-warning text-dark"><i class="bx bx-bell me-1"></i> STR Alert</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $txn->created_at->format('d M Y') }}</span>
                                <small class="d-block text-muted">{{ $txn->created_at->format('h:i A') }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bx bx-check-circle fs-1 text-success mb-2 d-block"></i>
                                No active AML surveillance flags.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Forensic Security Audit Stream -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="card-title mb-0 fw-bold text-dark"><i class="bx bx-history text-primary me-2"></i>Live Forensic Security &amp; Action Stream</h5>
            <small class="text-muted">Real-time tamper-evident system audit logs</small>
        </div>
        <a href="{{ route('cbs.compliance.audit') }}" class="btn btn-sm btn-outline-primary">
            Full Audit Trail &rarr;
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>User</th>
                        <th>Event Type</th>
                        <th>Severity</th>
                        <th>IP Address &amp; Agent</th>
                        <th>Forensic Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentAudits as $log)
                        <tr>
                            <td><small class="text-muted font-monospace">{{ $log->created_at->format('d M, h:i:s A') }}</small></td>
                            <td><span class="fw-semibold text-dark">{{ $log->user->name ?? 'System Process' }}</span></td>
                            <td><span class="badge bg-label-secondary font-monospace">{{ $log->event_type }}</span></td>
                            <td>
                                <span class="badge {{ $log->severity === 'critical' ? 'bg-danger' : ($log->severity === 'warning' ? 'bg-warning' : 'bg-info') }}">
                                    {{ ucfirst($log->severity) }}
                                </span>
                            </td>
                            <td><small class="font-monospace text-muted">{{ $log->ip_address }}</small></td>
                            <td><span class="small">{{ $log->description }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
