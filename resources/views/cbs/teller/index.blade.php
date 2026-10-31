@extends('layouts.cbs')

@section('title', 'Teller Cash Desk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-dollar-circle text-primary me-2"></i>Teller Cash Counter Desk</h4>
        <span class="text-muted">Branch Cash Vault: <strong>{{ $branch->name }} ({{ $branch->code }})</strong></span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('cbs.teller.deposit') }}" class="btn btn-success">
            <i class="bx bx-down-arrow-alt me-1"></i> Cash Deposit
        </a>
        <a href="{{ route('cbs.teller.withdrawal') }}" class="btn btn-warning">
            <i class="bx bx-up-arrow-alt me-1"></i> Cash Withdrawal
        </a>
    </div>
</div>

<!-- Teller Till & Daily Figures -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100 bg-label-primary border-primary">
            <div class="card-body">
                <span class="text-muted small fw-semibold text-uppercase">Branch Physical Vault Cash</span>
                <h3 class="currency-pkr text-primary fw-bold my-1">PKR {{ number_format($branch->current_vault_cash, 2) }}</h3>
                <small class="text-muted">GL 1010 Vault Limit: PKR {{ number_format($branch->vault_cash_limit, 0) }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 bg-label-success border-success">
            <div class="card-body">
                <span class="text-muted small fw-semibold text-uppercase">Today's Counter Deposits</span>
                <h3 class="currency-pkr text-success fw-bold my-1">PKR {{ number_format($todayDeposits, 2) }}</h3>
                <small class="text-muted">Cash Inflow to Vault</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 bg-label-warning border-warning">
            <div class="card-body">
                <span class="text-muted small fw-semibold text-uppercase">Today's Counter Withdrawals</span>
                <h3 class="currency-pkr text-warning fw-bold my-1">PKR {{ number_format($todayWithdrawals, 2) }}</h3>
                <small class="text-muted">Cash Outflow from Till</small>
            </div>
        </div>
    </div>
</div>

<!-- Recent Counter Transactions Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold"><i class="bx bx-receipt text-primary me-2"></i>Today's Counter Cash Journal</h5>
        <span class="badge bg-label-secondary">{{ $todayTransactions->count() }} Entries</span>
    </div>
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Receipt Ref</th>
                        <th>Type</th>
                        <th>Account</th>
                        <th>Amount</th>
                        <th>Narration</th>
                        <th>CTR / Compliance</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($todayTransactions as $txn)
                        <tr>
                            <td>
                                <span class="fw-bold text-dark">{{ $txn->transaction_reference }}</span>
                            </td>
                            <td>
                                @if($txn->transaction_type === 'cash_deposit')
                                    <span class="badge bg-success"><i class="bx bx-down-arrow-alt me-1"></i> Deposit</span>
                                @elseif($txn->transaction_type === 'cash_withdrawal')
                                    <span class="badge bg-warning"><i class="bx bx-up-arrow-alt me-1"></i> Withdrawal</span>
                                @else
                                    <span class="badge bg-label-primary">{{ ucwords(str_replace('_', ' ', $txn->transaction_type)) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($txn->destinationAccount)
                                    <span class="fw-semibold text-dark">{{ $txn->destinationAccount->account_title }}</span>
                                    <small class="d-block text-muted">{{ $txn->destinationAccount->account_number }}</small>
                                @elseif($txn->sourceAccount)
                                    <span class="fw-semibold text-dark">{{ $txn->sourceAccount->account_title }}</span>
                                    <small class="d-block text-muted">{{ $txn->sourceAccount->account_number }}</small>
                                @endif
                            </td>
                            <td class="currency-pkr fw-bold">
                                PKR {{ number_format($txn->amount, 2) }}
                            </td>
                            <td>
                                <span class="small text-muted">{{ $txn->narration }}</span>
                            </td>
                            <td>
                                @if($txn->ctr_reported)
                                    <span class="badge bg-danger"><i class="bx bx-flag me-1"></i> SBP CTR Reported</span>
                                @elseif($txn->is_aml_flagged)
                                    <span class="badge bg-warning text-dark"><i class="bx bx-error me-1"></i> Flagged</span>
                                @else
                                    <span class="badge bg-label-success">Clean</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $txn->created_at->format('h:i A') }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No counter cash operations posted today yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
