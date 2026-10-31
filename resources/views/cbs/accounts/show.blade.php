@extends('layouts.cbs')

@section('title', 'Account - ' . $account->account_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-credit-card text-primary me-2"></i>Account Details & Ledger History</h4>
        <span class="text-muted">A/C: <strong>{{ $account->account_number }}</strong> | CIF: {{ $account->customer->cif_number ?? 'N/A' }}</span>
    </div>
    <div>
        <a href="{{ route('cbs.teller.deposit', ['account_id' => $account->id]) }}" class="btn btn-success me-2">
            <i class="bx bx-down-arrow-alt me-1"></i> Fast Deposit
        </a>
        <a href="{{ route('cbs.teller.withdrawal', ['account_id' => $account->id]) }}" class="btn btn-warning me-2">
            <i class="bx bx-up-arrow-alt me-1"></i> Cash Dispense
        </a>
        <a href="{{ route('cbs.accounts.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <!-- Account Summary Card -->
    <div class="col-md-5 mb-4">
        <div class="card h-100">
            <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark">{{ $account->product->name ?? 'Commercial Deposit' }}</span>
                <span class="badge {{ $account->status_badge_class }}">{{ ucfirst($account->status) }}</span>
            </div>
            <div class="card-body pt-4">
                <div class="mb-4 text-center">
                    <span class="text-muted small text-uppercase">Available Spendable Balance</span>
                    <h2 class="currency-pkr text-primary fw-bold my-1">PKR {{ number_format($account->available_balance, 2) }}</h2>
                    <span class="badge bg-label-info">{{ $account->currency }} Currency</span>
                </div>

                <div class="p-3 mb-3 bg-light rounded border">
                    <div class="small text-muted mb-1">State Bank of Pakistan 24-Character IBAN</div>
                    <div class="font-monospace fw-bold fs-6 text-dark text-break">{{ $account->iban }}</div>
                </div>

                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Account Title:</span>
                        <span class="fw-semibold">{{ $account->account_title }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Primary Branch:</span>
                        <span class="fw-semibold">{{ $account->branch->name }} ({{ $account->branch->code }})</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Accounting Ledger Balance:</span>
                        <span class="fw-semibold currency-pkr">PKR {{ number_format($account->ledger_balance, 2) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Accrued Savings Profit:</span>
                        <span class="fw-semibold text-success currency-pkr">PKR {{ number_format($account->accrued_profit, 2) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Opened Date:</span>
                        <span>{{ $account->opened_at ? $account->opened_at->format('d M Y') : 'N/A' }}</span>
                    </li>
                </ul>

                <!-- Account Administrative Control: Freeze / Unfreeze -->
                <div class="border-top pt-3">
                    <form method="POST" action="{{ route('cbs.accounts.freeze', $account->id) }}">
                        @csrf
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="d-block text-dark">Lien & Freeze Status</strong>
                                <small class="text-muted">{{ $account->status === 'frozen' ? 'Account is currently locked from debits/credits.' : 'Operational for all teller & digital transactions.' }}</small>
                            </div>
                            <button type="submit" class="btn btn-sm {{ $account->status === 'frozen' ? 'btn-success' : 'btn-danger' }}">
                                <i class="bx {{ $account->status === 'frozen' ? 'bx-lock-open' : 'bx-lock' }} me-1"></i>
                                {{ $account->status === 'frozen' ? 'Unfreeze Account' : 'Freeze Account' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Transaction History -->
    <div class="col-md-7 mb-4">
        <div class="card h-100">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold"><i class="bx bx-history text-primary me-2"></i>Recent Transaction Ledger</h5>
                <span class="badge bg-label-secondary">Double-Entry Audited</span>
            </div>
            <div class="card-body">
                @if($transactions->isEmpty())
                    <p class="text-muted text-center py-5">No transactions recorded on this account yet.</p>
                @else
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Ref / Time</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Narration</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $txn)
                                    @php
                                        $isCredit = ($txn->destination_account_id === $account->id);
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="fw-semibold text-dark d-block">{{ $txn->transaction_reference }}</span>
                                            <small class="text-muted">{{ $txn->created_at->format('d M, h:i A') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge {{ $isCredit ? 'bg-label-success' : 'bg-label-danger' }}">
                                                {{ $isCredit ? 'Credit (+)' : 'Debit (-)' }}
                                            </span>
                                            <small class="d-block text-muted">{{ ucwords(str_replace('_', ' ', $txn->transaction_type)) }}</small>
                                        </td>
                                        <td class="currency-pkr fw-bold {{ $isCredit ? 'text-success' : 'text-danger' }}">
                                            {{ $isCredit ? '+' : '-' }} PKR {{ number_format($txn->amount, 2) }}
                                        </td>
                                        <td>
                                            <span class="text-truncate d-inline-block small" style="max-width: 180px;" title="{{ $txn->narration }}">
                                                {{ $txn->narration }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $txn->status_badge_class }}">{{ ucfirst($txn->status) }}</span>
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
</div>
@endsection
