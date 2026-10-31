@extends('layouts.cbs')

@section('title', 'Real-time Trial Balance')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-scale text-primary me-2"></i>Real-time Core General Ledger Trial Balance</h4>
        <span class="text-muted">Verification of Fundamental Accounting Equation: Total Debits = Total Credits</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('cbs.gl.journal') }}" class="btn btn-outline-secondary">
            <i class="bx bx-book-content me-1"></i> Journal Vouchers
        </a>
        <button onclick="window.print()" class="btn btn-outline-primary">
            <i class="bx bx-printer me-1"></i> Print Trial Balance
        </button>
    </div>
</div>

<!-- Ledger Balance Equality Verification Alert -->
<div class="alert {{ $isBalanced ? 'alert-success' : 'alert-danger' }} d-flex align-items-center mb-4 shadow-sm" role="alert">
    <i class="bx {{ $isBalanced ? 'bx-check-double' : 'bx-error' }} fs-1 me-3"></i>
    <div>
        <h5 class="alert-heading mb-1 fw-bold">
            {{ $isBalanced ? 'General Ledger Perfectly Balanced' : 'Trial Balance Discrepancy Detected' }}
        </h5>
        <div>
            Total Ledger Debits: <strong class="currency-pkr">PKR {{ number_format($totalDebits, 2) }}</strong> &nbsp;|&nbsp;
            Total Ledger Credits: <strong class="currency-pkr">PKR {{ number_format($totalCredits, 2) }}</strong> &nbsp;|&nbsp;
            Variance: <strong class="currency-pkr">PKR {{ number_format(abs($totalDebits - $totalCredits), 2) }}</strong>
        </div>
    </div>
</div>

<!-- Trial Balance Table -->
<div class="card shadow-sm">
    <div class="table-responsive text-nowrap">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th style="width: 12%;">GL Code</th>
                    <th>Account Title</th>
                    <th style="width: 15%;">Classification</th>
                    <th class="text-end" style="width: 20%;">Debit Balance (PKR)</th>
                    <th class="text-end" style="width: 20%;">Credit Balance (PKR)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($glAccounts as $gl)
                    @php
                        $isDebitNormal = ($gl->normal_balance === 'debit');
                    @endphp
                    <tr>
                        <td class="font-monospace fw-bold text-primary">{{ $gl->account_code }}</td>
                        <td>
                            <span class="fw-semibold text-dark">{{ $gl->account_name }}</span>
                        </td>
                        <td>
                            <span class="badge bg-label-secondary text-uppercase">{{ $gl->account_type }}</span>
                        </td>
                        <td class="text-end currency-pkr fw-semibold">
                            @if($isDebitNormal)
                                PKR {{ number_format($gl->current_balance, 2) }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end currency-pkr fw-semibold">
                            @if(!$isDebitNormal)
                                PKR {{ number_format($gl->current_balance, 2) }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light fw-bold fs-6">
                <tr>
                    <td colspan="3" class="text-end text-uppercase">Total Trial Balance:</td>
                    <td class="text-end text-primary currency-pkr fs-5">
                        PKR {{ number_format($totalDebits, 2) }}
                    </td>
                    <td class="text-end text-primary currency-pkr fs-5">
                        PKR {{ number_format($totalCredits, 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
