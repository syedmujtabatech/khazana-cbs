@extends('layouts.portal')

@section('title', 'My Accounts Overview')

@section('content')
<div class="row">
    <!-- Welcome Customer Header -->
    <div class="col-12 mb-4">
        <div class="card portal-header shadow-sm border-0">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <span class="badge bg-white text-dark mb-2 px-3 py-1">
                            <i class="bx bx-check-shield text-success me-1"></i> SBP Verified Banking Customer
                        </span>
                        <h3 class="text-white fw-bold mb-1">Welcome back, {{ $profile->full_name }}!</h3>
                        <p class="text-white-50 mb-0">
                            Khazana Internet Banking Portal &bull; CIF: <strong>{{ $profile->cif_number }}</strong> &bull; CNIC: <strong>{{ $profile->cnic_passport }}</strong>
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <a href="{{ route('portal.transfer') }}" class="btn btn-warning fw-bold me-2">
                            <i class="bx bx-send me-1"></i> Send Money
                        </a>
                        <a href="{{ route('portal.bill_payment') }}" class="btn btn-outline-light">
                            <i class="bx bx-bulb me-1"></i> Pay Bills
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Account Balance & Card Display -->
    <div class="col-lg-5 mb-4">
        @if($primaryAccount)
            <!-- Virtual Debit Card Representation -->
            @php
                $card = $debitCards->first();
            @endphp
            <div class="card-atm mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-bold tracking-wider fs-5">KHAZANA BANK</span>
                    <span class="badge bg-white text-dark fw-bold text-uppercase">{{ $card->card_type ?? 'Visa Debit' }}</span>
                </div>
                <div class="chip mb-3"></div>
                <div class="font-monospace fs-4 fw-bold letter-spacing-2 mb-3">
                    {{ $card->card_number_masked ?? '5241 **** **** 4892' }}
                </div>
                <div class="d-flex justify-content-between align-items-end small">
                    <div>
                        <div class="text-white-50" style="font-size: 0.65rem;">CARDHOLDER NAME</div>
                        <div class="fw-bold text-uppercase">{{ $card->embossed_name ?? $profile->full_name }}</div>
                    </div>
                    <div>
                        <div class="text-white-50" style="font-size: 0.65rem;">EXPIRES</div>
                        <div class="fw-bold font-monospace">{{ $card->expiry_month ?? '12' }}/{{ $card->expiry_year ?? '2029' }}</div>
                    </div>
                    <div>
                        <i class="bx bx-wifi fs-4 text-white-50"></i>
                    </div>
                </div>
            </div>

            <!-- Primary Account Balance Summary -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold text-uppercase">Total Available Balance</span>
                        <span class="badge bg-label-success">{{ ucfirst($primaryAccount->status) }}</span>
                    </div>
                    <h2 class="currency-pkr text-primary fw-bold mb-2">
                        PKR {{ number_format($primaryAccount->available_balance, 2) }}
                    </h2>
                    <div class="p-2 mb-3 bg-light rounded font-monospace small text-dark border">
                        <span class="text-muted d-block" style="font-size: 0.7rem;">PAKISTAN 24-CHAR IBAN:</span>
                        <strong>{{ $primaryAccount->iban }}</strong>
                    </div>

                    <div class="row g-2 text-center border-top pt-3">
                        <div class="col-4">
                            <a href="{{ route('portal.statement') }}" class="btn btn-sm btn-outline-primary w-100 py-2">
                                <i class="bx bx-receipt d-block fs-4 mb-1"></i> Statement
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="{{ route('portal.transfer') }}" class="btn btn-sm btn-outline-success w-100 py-2">
                                <i class="bx bx-transfer-alt d-block fs-4 mb-1"></i> Transfer
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="{{ route('portal.cards') }}" class="btn btn-sm btn-outline-secondary w-100 py-2">
                                <i class="bx bx-slider d-block fs-4 mb-1"></i> Limits
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Active Financing / Loans -->
        @if($activeLoans->isNotEmpty())
            <div class="card shadow-sm mb-4">
                <div class="card-header border-bottom">
                    <h6 class="card-title mb-0 fw-bold"><i class="bx bx-car text-warning me-2"></i>Active Financing Facility</h6>
                </div>
                <div class="card-body pt-3">
                    @foreach($activeLoans as $ln)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold">{{ ucwords(str_replace('_', ' ', $ln->product_type)) }}</span>
                            <span class="badge bg-label-warning">{{ $ln->markup_rate }}% Markup</span>
                        </div>
                        <div class="currency-pkr fw-bold text-dark fs-5 mb-1">
                            Outstanding: PKR {{ number_format($ln->remaining_balance, 2) }}
                        </div>
                        <small class="text-muted d-block mb-2">Monthly EMI: PKR {{ number_format($ln->monthly_installment, 0) }} (Tenor: {{ $ln->tenor_months }} Mos)</small>
                        <div class="progress" style="height: 6px;">
                            @php $lnPct = round(($ln->installments_paid / $ln->tenor_months) * 100); @endphp
                            <div class="progress-bar bg-success" style="width: {{ $lnPct }}%"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Recent Transactions & Saved Payees -->
    <div class="col-lg-7 mb-4">
        <!-- Recent Mini-Statement -->
        <div class="card shadow-sm mb-4">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold"><i class="bx bx-history text-primary me-2"></i>Recent Account Activity</h5>
                <a href="{{ route('portal.statement') }}" class="btn btn-sm btn-outline-primary">Full Statement &rarr;</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Transaction</th>
                                <th>Amount</th>
                                <th>Date / Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $txn)
                                @php
                                    $isCredit = ($primaryAccount && $txn->destination_account_id === $primaryAccount->id);
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle {{ $isCredit ? 'bg-label-success' : 'bg-label-danger' }}">
                                                    <i class="bx {{ $isCredit ? 'bx-down-arrow-alt' : 'bx-up-arrow-alt' }}"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <span class="fw-semibold text-dark d-block text-truncate" style="max-width: 200px;">
                                                    {{ $txn->narration }}
                                                </span>
                                                <small class="text-muted font-monospace">{{ $txn->transaction_reference }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="currency-pkr fw-bold {{ $isCredit ? 'text-success' : 'text-danger' }}">
                                        {{ $isCredit ? '+' : '-' }} PKR {{ number_format($txn->amount, 2) }}
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $txn->created_at->format('d M, h:i A') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $txn->status_badge_class }}">{{ ucfirst($txn->status) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No recent transactions recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Saved Payees / Beneficiaries -->
        <div class="card shadow-sm">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold"><i class="bx bx-user-check text-success me-2"></i>Saved Beneficiaries</h5>
                <a href="{{ route('portal.transfer') }}" class="btn btn-sm btn-outline-success">+ Add Payee</a>
            </div>
            <div class="card-body pt-3">
                @if($beneficiaries->isEmpty())
                    <p class="text-muted text-center py-3">No saved beneficiaries. Add a payee for instant 1LINK & Raast transfers.</p>
                @else
                    <div class="row g-2">
                        @foreach($beneficiaries as $ben)
                            <div class="col-md-6">
                                <div class="p-3 border rounded bg-light">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong class="text-dark">{{ $ben->nick_name }}</strong>
                                        <span class="badge bg-label-primary">{{ strtoupper(str_replace('_', ' ', $ben->transfer_type)) }}</span>
                                    </div>
                                    <small class="d-block text-muted">{{ $ben->beneficiary_name }} &bull; {{ $ben->bank_name }}</small>
                                    <small class="font-monospace text-primary d-block text-truncate">{{ $ben->account_or_iban }}</small>
                                    <a href="{{ route('portal.transfer') }}" class="btn btn-xs btn-outline-primary mt-2">
                                        <i class="bx bx-send me-1"></i> Send Now
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
