@extends('layouts.cbs')

@section('title', 'Teller Cash Counter Desk')

@section('content')
<!-- Header & Quick Actions -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-warning text-dark px-2 py-1"><i class="bx bx-money me-1"></i> PHYSICAL CASH TILL</span>
            <h4 class="fw-bold mb-0 text-dark">Teller Cash Counter Desk</h4>
        </div>
        <p class="text-muted small mb-0">Branch Cash Vault: <strong>{{ $branch->name }}</strong> (Code: {{ $branch->code }}) &bull; SBP Cash Inflow &amp; Outflow Till</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('cbs.teller.deposit') }}" class="btn btn-success fw-bold shadow-sm px-3">
            <i class="bx bx-down-arrow-alt me-1"></i> Cash Deposit Slip
        </a>
        <a href="{{ route('cbs.teller.withdrawal') }}" class="btn btn-warning fw-bold shadow-sm px-3 text-dark">
            <i class="bx bx-up-arrow-alt me-1"></i> Cash Withdrawal
        </a>
    </div>
</div>

<!-- Till Cash Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #2563EB !important;">
            <div class="card-body p-3">
                <span class="text-muted small fw-bold text-uppercase">Current Branch Vault Cash</span>
                <h3 class="currency-pkr text-primary fw-bold my-1">
                    PKR {{ number_format($branch->current_vault_cash, 2) }}
                </h3>
                <small class="text-muted"><i class="bx bx-vault text-primary"></i> SBP Vault Ceiling: PKR {{ number_format($branch->vault_cash_limit, 0) }}</small>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #10B981 !important;">
            <div class="card-body p-3">
                <span class="text-muted small fw-bold text-uppercase">Today's Counter Deposits</span>
                <h3 class="currency-pkr text-success fw-bold my-1">
                    PKR {{ number_format($todayDeposits, 2) }}
                </h3>
                <small class="text-muted"><i class="bx bx-down-arrow-circle text-success"></i> Cash Received at Till (GL 1010 Inflow)</small>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #F59E0B !important;">
            <div class="card-body p-3">
                <span class="text-muted small fw-bold text-uppercase">Today's Counter Withdrawals</span>
                <h3 class="currency-pkr text-warning fw-bold my-1">
                    PKR {{ number_format($todayWithdrawals, 2) }}
                </h3>
                <small class="text-muted"><i class="bx bx-up-arrow-circle text-warning"></i> Cash Dispensed to Customers (GL 1010 Outflow)</small>
            </div>
        </div>
    </div>
</div>

<!-- Today's Counter Transactions Journal -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="bx bx-receipt text-primary me-2"></i>Today's Counter Cash Journal Vouchers
            </h5>
            <small class="text-muted">Cleared cash transactions logged at this branch till</small>
        </div>
        <span class="badge bg-label-secondary">{{ $todayTransactions->count() }} Entries</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Receipt Voucher</th>
                        <th>Cash Type</th>
                        <th>Customer / Account</th>
                        <th class="text-end">Amount (PKR)</th>
                        <th>Voucher Particulars</th>
                        <th>Surveillance</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($todayTransactions as $txn)
                        <tr>
                            <td>
                                <span class="font-monospace fw-bold text-dark">{{ $txn->transaction_reference }}</span>
                            </td>
                            <td>
                                @if($txn->transaction_type === 'cash_deposit')
                                    <span class="badge bg-success"><i class="bx bx-down-arrow-alt me-1"></i> Deposit</span>
                                @elseif($txn->transaction_type === 'cash_withdrawal')
                                    <span class="badge bg-warning text-dark"><i class="bx bx-up-arrow-alt me-1"></i> Withdrawal</span>
                                @else
                                    <span class="badge bg-label-primary">{{ ucwords(str_replace('_', ' ', $txn->transaction_type)) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($txn->destinationAccount)
                                    <strong class="text-dark d-block">{{ $txn->destinationAccount->account_title }}</strong>
                                    <small class="text-muted font-monospace">{{ $txn->destinationAccount->account_number }}</small>
                                @elseif($txn->sourceAccount)
                                    <strong class="text-dark d-block">{{ $txn->sourceAccount->account_title }}</strong>
                                    <small class="text-muted font-monospace">{{ $txn->sourceAccount->account_number }}</small>
                                @endif
                            </td>
                            <td class="text-end currency-pkr fw-bold fs-6">
                                PKR {{ number_format($txn->amount, 2) }}
                            </td>
                            <td>
                                <span class="small text-muted text-truncate d-inline-block" style="max-width: 220px;" title="{{ $txn->narration }}">
                                    {{ $txn->narration }}
                                </span>
                            </td>
                            <td>
                                @if($txn->ctr_reported)
                                    <span class="badge bg-danger"><i class="bx bx-flag me-1"></i> SBP CTR (&ge;2M)</span>
                                @elseif($txn->is_aml_flagged)
                                    <span class="badge bg-warning text-dark"><i class="bx bx-error me-1"></i> Flagged</span>
                                @else
                                    <span class="badge bg-label-success"><i class="bx bx-check me-1"></i> Verified</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted font-monospace">{{ $txn->created_at->format('h:i:s A') }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bx bx-info-circle fs-1 text-primary mb-2 d-block"></i>
                                No counter cash vouchers posted today yet. Click "Cash Deposit Slip" or "Cash Withdrawal" above.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
