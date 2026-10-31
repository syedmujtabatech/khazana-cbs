@extends('layouts.cbs')

@section('title', 'AML / CFT Compliance Cockpit')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-shield-quarter text-danger me-2"></i>SBP AML / CFT Regulatory Cockpit</h4>
        <span class="text-muted">Anti-Money Laundering & Combating Financing of Terrorism Surveillance Engine</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('cbs.compliance.ctr') }}" class="btn btn-danger">
            <i class="bx bx-file-blank me-1"></i> CTR Reports (&gt;2M PKR)
        </a>
        <a href="{{ route('cbs.compliance.pep') }}" class="btn btn-outline-danger">
            <i class="bx bx-user-voice me-1"></i> PEP Registry
        </a>
    </div>
</div>

<!-- Risk Metrics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100 border-danger border">
            <div class="card-body">
                <span class="text-muted small fw-semibold text-uppercase">SBP CTR Disclosures (&ge; 2M PKR)</span>
                <h3 class="text-danger fw-bold my-1">{{ $ctrCount }} <small class="text-muted fs-6">Reports</small></h3>
                <small class="text-muted">Mandatory Cash Transaction Reports to Financial Monitoring Unit (FMU)</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-warning border">
            <div class="card-body">
                <span class="text-muted small fw-semibold text-uppercase">Total Flagged Surveillance Events</span>
                <h3 class="text-warning fw-bold my-1">{{ $totalFlagged }} <small class="text-muted fs-6">Alerts</small></h3>
                <small class="text-muted">Velocity spikes & high-risk customer activities</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-primary border">
            <div class="card-body">
                <span class="text-muted small fw-semibold text-uppercase">Politically Exposed Persons (PEP)</span>
                <h3 class="text-primary fw-bold my-1">{{ $pepCount }} <small class="text-muted fs-6">Profiles</small></h3>
                <small class="text-muted">Enhanced Due Diligence (EDD) monitored customers</small>
            </div>
        </div>
    </div>
</div>

<!-- Flagged Transactions Table -->
<div class="card mb-4">
    <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold"><i class="bx bx-error text-danger me-2"></i>Active AML Surveillance Flags</h5>
        <span class="badge bg-danger">{{ $flaggedTransactions->count() }} Triggered</span>
    </div>
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Transaction Ref</th>
                        <th>Account / Customer</th>
                        <th>Amount</th>
                        <th>Rule Triggered / AML Reason</th>
                        <th>Report Status</th>
                        <th>Date / Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($flaggedTransactions as $txn)
                        <tr>
                            <td>
                                <span class="fw-bold font-monospace text-dark">{{ $txn->transaction_reference }}</span>
                                <small class="d-block text-muted">{{ ucwords(str_replace('_', ' ', $txn->transaction_type)) }}</small>
                            </td>
                            <td>
                                @php
                                    $acc = $txn->destinationAccount ?? $txn->sourceAccount;
                                @endphp
                                @if($acc)
                                    <span class="fw-semibold text-dark">{{ $acc->account_title }}</span>
                                    <small class="d-block text-muted">A/C: {{ $acc->account_number }}</small>
                                @else
                                    <span class="text-muted">Counter Slip</span>
                                @endif
                            </td>
                            <td class="currency-pkr fw-bold fs-6">
                                PKR {{ number_format($txn->amount, 2) }}
                            </td>
                            <td>
                                <span class="badge bg-label-danger text-wrap text-start" style="max-width: 380px;">
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
                                <small class="text-muted">{{ $txn->created_at->format('d M Y, h:i A') }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No AML alerts active at this moment.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Forensic Security Audit Stream -->
<div class="card">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold"><i class="bx bx-history text-primary me-2"></i>Live Forensic Security & Action Audit Stream</h5>
        <a href="{{ route('cbs.compliance.audit') }}" class="btn btn-xs btn-outline-primary">Full Audit Trail &rarr;</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Time</th>
                        <th>User</th>
                        <th>Event Type</th>
                        <th>Severity</th>
                        <th>IP Address</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentAudits as $log)
                        <tr>
                            <td><small class="text-muted">{{ $log->created_at->format('d M h:i:s A') }}</small></td>
                            <td><span class="fw-semibold">{{ $log->user->name ?? 'System Engine' }}</span></td>
                            <td><span class="badge bg-label-secondary font-monospace">{{ $log->event_type }}</span></td>
                            <td>
                                <span class="badge {{ $log->severity === 'critical' ? 'bg-danger' : ($log->severity === 'warning' ? 'bg-warning' : 'bg-info') }}">
                                    {{ ucfirst($log->severity) }}
                                </span>
                            </td>
                            <td><small class="font-monospace">{{ $log->ip_address }}</small></td>
                            <td><small>{{ $log->description }}</small></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
