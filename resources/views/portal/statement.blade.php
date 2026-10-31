@extends('layouts.portal')

@section('title', 'Official Account Statement')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-receipt text-primary me-2"></i>Official Account Statement</h4>
        <span class="text-muted">Certified Banking E-Statement for Tax, Visa & Accounting Verification</span>
    </div>
    <div>
        <button onclick="window.print()" class="btn btn-outline-primary">
            <i class="bx bx-printer me-1"></i> Print / Save as PDF
        </button>
    </div>
</div>

<!-- Statement Filter Card -->
<div class="card mb-4 no-print">
    <div class="card-body">
        <form method="GET" action="{{ route('portal.statement') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Select Account</label>
                <select name="account_id" class="form-select">
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ $selectedAccount->id == $acc->id ? 'selected' : '' }}>
                            {{ $acc->account_number }} ({{ $acc->account_title }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">From Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">To Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100"><i class="bx bx-filter-alt me-1"></i> Generate</button>
            </div>
        </form>
    </div>
</div>

<!-- Printable Statement Layout -->
<div class="card shadow-sm p-4 border" id="printableStatement">
    <!-- Bank Official Letterhead -->
    <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0">خزانہ بینک لمیٹڈ</h3>
            <h5 class="text-primary fw-bold mb-1">KHAZANA BANK LIMITED</h5>
            <small class="text-muted d-block">Core Banking Operations &bull; Head Office, Karachi</small>
            <small class="text-muted">Swift BIC: <strong>KHZNPKKA</strong> &bull; Member: 1LINK &amp; SBP PRISM</small>
        </div>
        <div class="text-end">
            <span class="badge bg-label-success fs-6 mb-2">Verified E-Statement</span>
            <div class="small text-muted">Statement Date: <strong>{{ date('d F Y') }}</strong></div>
            <div class="small text-muted">Currency: <strong>PKR (Pakistani Rupee)</strong></div>
        </div>
    </div>

    <!-- Customer & Account Mandate Details -->
    <div class="row g-3 mb-4 p-3 bg-light rounded">
        <div class="col-md-6">
            <div class="small text-muted">Account Holder Title:</div>
            <h5 class="fw-bold mb-1 text-dark">{{ $selectedAccount->account_title }}</h5>
            <div class="small text-muted">Pakistani CNIC / Passport: <strong>{{ $profile->cnic_passport }}</strong></div>
            <div class="small text-muted">Customer CIF ID: <strong>{{ $profile->cif_number }}</strong></div>
            <div class="small text-muted">Address: {{ $profile->residential_address }}, {{ $profile->city }}</div>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="small text-muted">Account Number:</div>
            <div class="font-monospace fw-bold text-primary fs-5 mb-1">{{ $selectedAccount->account_number }}</div>
            <div class="small text-muted">24-Character SBP IBAN:</div>
            <div class="font-monospace fw-bold text-dark small mb-1">{{ $selectedAccount->iban }}</div>
            <div class="small text-muted">Available Balance: <strong class="currency-pkr text-success fs-5">PKR {{ number_format($selectedAccount->available_balance, 2) }}</strong></div>
        </div>
    </div>

    <!-- Ledger Lines Table -->
    <div class="table-responsive text-nowrap mb-4">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th style="width: 15%;">Date & Time</th>
                    <th style="width: 15%;">Reference</th>
                    <th>Narration / Particulars</th>
                    <th class="text-end" style="width: 15%;">Debit (Withdrawal)</th>
                    <th class="text-end" style="width: 15%;">Credit (Deposit)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $txn)
                    @php
                        $isCredit = ($txn->destination_account_id === $selectedAccount->id);
                    @endphp
                    <tr>
                        <td class="font-monospace small">{{ $txn->posted_at ? $txn->posted_at->format('d M Y, h:i A') : $txn->created_at->format('d M Y, h:i A') }}</td>
                        <td class="font-monospace small fw-bold text-primary">{{ $txn->transaction_reference }}</td>
                        <td>
                            <span class="fw-semibold text-dark">{{ $txn->narration }}</span>
                            <small class="d-block text-muted">{{ ucwords(str_replace('_', ' ', $txn->transaction_type)) }} ({{ strtoupper($txn->channel) }})</small>
                        </td>
                        <td class="text-end currency-pkr {{ !$isCredit ? 'text-danger fw-bold' : 'text-muted' }}">
                            {{ !$isCredit ? 'PKR ' . number_format($txn->amount, 2) : '-' }}
                        </td>
                        <td class="text-end currency-pkr {{ $isCredit ? 'text-success fw-bold' : 'text-muted' }}">
                            {{ $isCredit ? 'PKR ' . number_format($txn->amount, 2) : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No transactions found for the selected period.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Statement Footer & Watermark -->
    <div class="border-top pt-3 small text-muted d-flex justify-content-between align-items-center">
        <div>
            This is a computer-generated bank statement from Khazana Core Banking Engine (Khazana-CBS) and does not require a physical signature.
        </div>
        <div>
            Page 1 of 1
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    #printableStatement, #printableStatement * { visibility: visible; }
    #printableStatement { position: absolute; left: 0; top: 0; width: 100%; border: none !important; box-shadow: none !important; }
    .no-print { display: none !important; }
}
</style>
@endsection
