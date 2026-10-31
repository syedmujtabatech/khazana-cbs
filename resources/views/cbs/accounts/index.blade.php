@extends('layouts.cbs')

@section('title', 'Bank Accounts & SBP IBAN Engine')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-credit-card-front text-primary me-2"></i>Bank Accounts & SBP IBAN Master</h4>
        <span class="text-muted">Demand Deposits, PLS Savings, Term Deposits (TDR) and Foreign Currency Accounts</span>
    </div>
    <a href="{{ route('cbs.accounts.create') }}" class="btn btn-primary">
        <i class="bx bx-plus me-1"></i> Open New Account
    </a>
</div>

<!-- Search & Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('cbs.accounts.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Search Account #, IBAN or Title</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search Account number or IBAN..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Commercial Branch</label>
                <select name="branch_id" class="form-select">
                    <option value="">All Branches</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="frozen" {{ request('status') == 'frozen' ? 'selected' : '' }}>Frozen</option>
                    <option value="dormant" {{ request('status') == 'dormant' ? 'selected' : '' }}>Dormant</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2 w-100"><i class="bx bx-filter-alt me-1"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Accounts Table -->
<div class="card">
    <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Account Title & CIF</th>
                    <th>Account Number & SBP IBAN</th>
                    <th>Product Type</th>
                    <th>Branch</th>
                    <th>Available Balance</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($accounts as $acc)
                    <tr>
                        <td>
                            <span class="fw-bold text-dark d-block">{{ $acc->account_title }}</span>
                            <small class="text-muted">CIF: {{ $acc->customer->cif_number ?? 'N/A' }} ({{ $acc->customer->cnic_passport ?? 'N/A' }})</small>
                        </td>
                        <td>
                            <span class="font-monospace fw-bold text-primary d-block">{{ $acc->account_number }}</span>
                            <span class="font-monospace text-muted small" style="font-size: 0.75rem;">{{ $acc->iban }}</span>
                        </td>
                        <td>
                            <span class="badge bg-label-primary">{{ $acc->product->name ?? 'Standard' }}</span>
                            <small class="d-block text-muted">{{ strtoupper($acc->currency) }}</small>
                        </td>
                        <td>
                            <span>{{ $acc->branch->name ?? 'Head Office' }}</span>
                            <small class="d-block text-muted">Code: {{ $acc->branch->code ?? '0010' }}</small>
                        </td>
                        <td class="currency-pkr fw-bold fs-6">
                            PKR {{ number_format($acc->available_balance, 2) }}
                            @if($acc->accrued_profit > 0)
                                <small class="d-block text-success" style="font-size: 0.75rem;">+PKR {{ number_format($acc->accrued_profit, 2) }} profit</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $acc->status_badge_class }}">{{ ucfirst($acc->status) }}</span>
                        </td>
                        <td>
                            <a href="{{ route('cbs.accounts.show', $acc->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bx bx-show me-1"></i> Details
                            </a>
                            <a href="{{ route('cbs.teller.deposit', ['account_id' => $acc->id]) }}" class="btn btn-sm btn-outline-success">
                                <i class="bx bx-down-arrow-alt"></i> Deposit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No accounts match the criteria.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-end">
        {{ $accounts->links() }}
    </div>
</div>
@endsection
