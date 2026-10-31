@extends('layouts.cbs')

@section('title', 'Customer 360° Profile - ' . $customer->full_name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-user-check text-primary me-2"></i>Customer 360&deg; Master Profile</h4>
        <span class="text-muted">CIF Record: <strong>{{ $customer->cif_number }}</strong></span>
    </div>
    <div>
        <a href="{{ route('cbs.accounts.create', ['customer_id' => $customer->id]) }}" class="btn btn-success me-2">
            <i class="bx bx-plus me-1"></i> Open New Account
        </a>
        <a href="{{ route('cbs.customers.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <!-- Profile Bio Card -->
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center pt-4">
                <div class="avatar avatar-xl mx-auto mb-3">
                    <span class="avatar-initial rounded-circle bg-label-primary fs-2 fw-bold">
                        {{ substr($customer->first_name, 0, 1) }}{{ substr($customer->last_name, 0, 1) }}
                    </span>
                </div>
                <h5 class="fw-bold mb-1">{{ $customer->full_name }}</h5>
                <p class="text-muted small mb-2">{{ $customer->occupation ?? 'Bank Customer' }}</p>
                <div class="mb-3">
                    @if($customer->is_pep)
                        <span class="badge bg-danger"><i class="bx bx-star me-1"></i> PEP (Politically Exposed)</span>
                    @else
                        <span class="badge bg-label-success">Standard Resident</span>
                    @endif
                    <span class="badge bg-label-info ms-1">{{ strtoupper($customer->risk_rating) }} Risk</span>
                </div>

                <hr class="my-3">

                <div class="text-start">
                    <p class="mb-2"><strong>Pakistani CNIC:</strong> <span class="font-monospace text-primary float-end">{{ $customer->cnic_passport }}</span></p>
                    <p class="mb-2"><strong>Date of Birth:</strong> <span class="float-end">{{ $customer->date_of_birth ? $customer->date_of_birth->format('d M Y') : 'N/A' }}</span></p>
                    <p class="mb-2"><strong>Gender:</strong> <span class="float-end text-capitalize">{{ $customer->gender }}</span></p>
                    <p class="mb-2"><strong>Monthly Income:</strong> <span class="float-end currency-pkr">PKR {{ number_format($customer->monthly_income, 2) }}</span></p>
                    <p class="mb-2"><strong>Email:</strong> <span class="float-end">{{ $customer->user->email ?? 'N/A' }}</span></p>
                    <p class="mb-2"><strong>Mobile Phone:</strong> <span class="float-end">{{ $customer->user->phone ?? 'N/A' }}</span></p>
                    <p class="mb-0"><strong>Address:</strong> <br><small class="text-muted">{{ $customer->residential_address }}, {{ $customer->city }}</small></p>
                </div>
            </div>
            <div class="card-footer bg-light border-top">
                <div class="d-flex justify-content-between align-items-center small">
                    <span class="text-muted">Biometric NADRA Verification:</span>
                    <span class="badge bg-success"><i class="bx bx-check me-1"></i> Verified</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Accounts & Associated Products -->
    <div class="col-md-8 mb-4">
        <!-- Accounts List -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold"><i class="bx bx-credit-card-front text-primary me-2"></i>Active Bank Accounts ({{ $customer->accounts->count() }})</h5>
                <a href="{{ route('cbs.accounts.create', ['customer_id' => $customer->id]) }}" class="btn btn-xs btn-primary">+ Open Account</a>
            </div>
            <div class="card-body">
                @if($customer->accounts->isEmpty())
                    <p class="text-muted py-3 text-center">No bank accounts opened under this CIF yet.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Account Title / Number</th>
                                    <th>Product & Branch</th>
                                    <th>Available Balance</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer->accounts as $acc)
                                    <tr>
                                        <td>
                                            <span class="fw-bold text-dark d-block">{{ $acc->account_title }}</span>
                                            <span class="font-monospace text-primary small d-block">{{ $acc->account_number }}</span>
                                            <small class="text-muted font-monospace" style="font-size: 0.75rem;">IBAN: {{ $acc->iban }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-label-info">{{ $acc->product->name ?? 'Current' }}</span>
                                            <small class="d-block text-muted">{{ $acc->branch->name ?? 'Main Branch' }}</small>
                                        </td>
                                        <td class="currency-pkr fw-bold">
                                            PKR {{ number_format($acc->available_balance, 2) }}
                                        </td>
                                        <td>
                                            <span class="badge {{ $acc->status_badge_class }}">{{ ucfirst($acc->status) }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('cbs.accounts.show', $acc->id) }}" class="btn btn-xs btn-outline-primary">
                                                Statement & Details
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

        <!-- Debit Cards & Cheques -->
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header pb-2">
                        <h6 class="card-title mb-0 fw-bold"><i class="bx bx-credit-card text-success me-2"></i>Issued Debit Cards</h6>
                    </div>
                    <div class="card-body">
                        @forelse($customer->debitCards as $card)
                            <div class="p-3 mb-2 rounded bg-light border">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold">{{ strtoupper($card->card_type) }}</span>
                                    <span class="badge {{ $card->status_badge_class }}">{{ ucfirst($card->status) }}</span>
                                </div>
                                <div class="font-monospace fw-semibold fs-6 mb-1">{{ $card->card_number_masked }}</div>
                                <div class="small text-muted d-flex justify-content-between">
                                    <span>EXP: {{ $card->expiry_month }}/{{ $card->expiry_year }}</span>
                                    <span>ATM Limit: PKR {{ number_format($card->daily_atm_limit, 0) }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted small py-2 mb-0">No active debit cards issued.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header pb-2">
                        <h6 class="card-title mb-0 fw-bold"><i class="bx bx-car text-warning me-2"></i>Financing & Advances</h6>
                    </div>
                    <div class="card-body">
                        @forelse($customer->loans as $loan)
                            <div class="p-3 mb-2 rounded bg-light border">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold">{{ ucwords(str_replace('_', ' ', $loan->product_type)) }}</span>
                                    <span class="badge bg-label-success">{{ ucfirst($loan->status) }}</span>
                                </div>
                                <div class="small mb-1">Ref: <span class="font-monospace">{{ $loan->loan_reference }}</span></div>
                                <div class="currency-pkr fw-bold mb-1">Balance: PKR {{ number_format($loan->remaining_balance, 2) }}</div>
                                <div class="small text-muted">Monthly Installment: PKR {{ number_format($loan->monthly_installment, 0) }} ({{ $loan->installments_paid }}/{{ $loan->tenor_months }} paid)</div>
                            </div>
                        @empty
                            <p class="text-muted small py-2 mb-0">No consumer or auto loans on file.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
