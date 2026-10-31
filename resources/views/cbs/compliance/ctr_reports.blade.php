@extends('layouts.cbs')

@section('title', 'SBP CTR Reports (&ge; 2M PKR)')

@section('content')
<!-- Header & Actions -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-danger px-2 py-1"><i class="bx bx-shield-quarter me-1"></i> SBP FMU MANDATORY</span>
            <h4 class="fw-bold mb-0 text-dark">Cash Transaction Reports (CTR)</h4>
        </div>
        <p class="text-muted small mb-0">Statutory AML/CFT reporting for cash transactions exceeding PKR 2.0 Million under SBP BPRD Circulars</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('cbs.compliance.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i> Compliance Cockpit
        </a>
        <button onclick="window.print()" class="btn btn-danger fw-bold shadow-sm">
            <i class="bx bx-printer me-1"></i> Print CTR Disclosures
        </button>
    </div>
</div>

<!-- Summary Metrics Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #EF4444 !important;">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold text-uppercase">Total Disclosed CTR Volume</span>
                <h4 class="currency-pkr text-danger fw-bold my-1">
                    PKR {{ number_format($ctrTransactions->sum('amount'), 2) }}
                </h4>
                <small class="text-muted"><i class="bx bx-check text-success"></i> Forwarded to Financial Monitoring Unit</small>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #F59E0B !important;">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold text-uppercase">Statutory CTR Threshold</span>
                <h4 class="currency-pkr text-warning fw-bold my-1">PKR 2,000,000.00</h4>
                <small class="text-muted"><i class="bx bx-info-circle text-info"></i> Automated Trigger on Till / Counter</small>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #10B981 !important;">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold text-uppercase">Regulatory Compliance State</span>
                <h4 class="text-success fw-bold my-1">100% SBP Compliant</h4>
                <small class="text-muted"><i class="bx bx-lock-alt text-success"></i> Tamper-Proof Double-Entry Audit</small>
            </div>
        </div>
    </div>
</div>

<!-- CTR Disclosures Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="card-title mb-0 fw-bold text-dark"><i class="bx bx-file-blank text-danger me-2"></i>Official SBP CTR Logged Records</h5>
            <small class="text-muted">Total Disclosures: {{ $ctrTransactions->total() }} recorded filings</small>
        </div>
        <span class="badge bg-label-danger px-3 py-2">Real-Time Surveillance Active</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 14%;">CTR Reference</th>
                        <th>Customer / CIF Profile</th>
                        <th>Pakistani CNIC</th>
                        <th>Booking Branch</th>
                        <th class="text-end" style="width: 16%;">Cash Volume (PKR)</th>
                        <th>Logging Officer</th>
                        <th>Filing Date &amp; Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ctrTransactions as $txn)
                        @php
                            $acc = $txn->destinationAccount ?? $txn->sourceAccount;
                            $cust = $acc ? $acc->customer : null;
                        @endphp
                        <tr>
                            <td>
                                <span class="fw-bold text-danger font-monospace fs-6">{{ $txn->transaction_reference }}</span>
                                <small class="d-block text-muted">{{ ucwords(str_replace('_', ' ', $txn->transaction_type)) }}</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded-circle bg-label-danger fw-bold">
                                            {{ substr($cust ? $cust->first_name : 'C', 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="fw-bold text-dark d-block">{{ $cust ? $cust->full_name : 'Cash Desk Counter Walk-in' }}</span>
                                        <small class="text-muted font-monospace">CIF: {{ $cust->cif_number ?? 'N/A' }}</small>
                                        @if($cust && $cust->is_pep)
                                            <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">PEP Account</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="font-monospace fw-bold text-dark">{{ $cust->cnic_passport ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $txn->branch->name ?? 'Corporate Main Branch' }}</span>
                                <small class="d-block text-muted">Branch Code: {{ $txn->branch->code ?? '0010' }}</small>
                            </td>
                            <td class="text-end">
                                <span class="currency-pkr fw-bold text-danger fs-5">
                                    PKR {{ number_format($txn->amount, 2) }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $txn->maker->name ?? 'Ayesha Siddiqui' }}</span>
                                <small class="d-block text-muted">{{ ucwords(str_replace('_', ' ', $txn->maker->role ?? 'Teller')) }}</small>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $txn->created_at->format('d M Y') }}</span>
                                <small class="d-block text-muted">{{ $txn->created_at->format('h:i:s A') }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bx bx-check-circle fs-1 text-success mb-2 d-block"></i>
                                Zero cash transactions exceeding PKR 2.0 Million logged.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($ctrTransactions->hasPages())
        <div class="card-footer d-flex justify-content-end py-3 border-top bg-white">
            {{ $ctrTransactions->links() }}
        </div>
    @endif
</div>
@endsection
