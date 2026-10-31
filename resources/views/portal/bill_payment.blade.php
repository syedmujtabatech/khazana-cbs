@extends('layouts.portal')

@section('title', '1BILL Utility Payments')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="bx bx-bulb text-warning me-2"></i>1BILL Integrated Utility & Tax Payments</h4>
                <span class="text-muted">Pay Electricity, Gas, Telecom & Federal Board of Revenue (FBR) PSID Taxes</span>
            </div>
            <a href="{{ route('portal.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back
            </a>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0 fw-bold">Select Utility Company & Enter Consumer Number</h5>
            </div>
            <div class="card-body pt-4">
                <form method="POST" action="{{ route('portal.bill_payment.process') }}">
                    @csrf

                    <!-- Source Debit Account -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Debit Account <span class="text-danger">*</span></label>
                        <select name="source_account_id" class="form-select @error('source_account_id') is-invalid @enderror" required>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">
                                    {{ $acc->account_number }} - {{ $acc->account_title }} (Available: PKR {{ number_format($acc->available_balance, 2) }})
                                </option>
                            @endforeach
                        </select>
                        @error('source_account_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Biller Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Select 1BILL Biller / Company <span class="text-danger">*</span></label>
                        <select name="biller_id" class="form-select @error('biller_id') is-invalid @enderror" required>
                            @foreach($billers as $b)
                                <option value="{{ $b->id }}" {{ old('biller_id') == $b->id ? 'selected' : '' }}>
                                    {{ $b->name }} ({{ ucwords(str_replace('_', ' ', $b->category)) }})
                                </option>
                            @endforeach
                        </select>
                        @error('biller_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Consumer Number -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Consumer Reference Number / PSID <span class="text-danger">*</span></label>
                        <input type="text" name="consumer_number" class="form-control font-monospace fs-5 @error('consumer_number') is-invalid @enderror" placeholder="e.g. 0400012345678" value="{{ old('consumer_number') }}" required>
                        <div class="form-text small">Located on top right of your utility bill paper or tax challan.</div>
                        @error('consumer_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Bill Amount -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Payable Bill Amount (PKR) <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text fw-bold bg-light">PKR</span>
                            <input type="number" step="0.01" name="amount" class="form-control fw-bold fs-4 @error('amount') is-invalid @enderror" placeholder="18500.00" value="{{ old('amount') }}" required>
                        </div>
                        @error('amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <!-- PIN -->
                    <div class="mb-4 p-3 bg-light rounded border">
                        <label class="form-label fw-bold mb-1"><i class="bx bx-shield-quarter text-primary me-1"></i> Enter Transaction PIN <span class="text-danger">*</span></label>
                        <input type="password" maxlength="4" name="pin" class="form-control text-center font-monospace fs-4 fw-bold @error('pin') is-invalid @enderror" style="letter-spacing: 10px;" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;" required>
                        <div class="form-text small">Demo PIN: 1122 or 1234</div>
                        @error('pin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('portal.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-warning px-4 fw-bold text-dark">
                            <i class="bx bx-check-circle me-1"></i> Confirm & Pay Utility Bill
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Recent Bill Payments History -->
        @if($recentBills->isNotEmpty())
            <div class="card shadow-sm">
                <div class="card-header border-bottom">
                    <h6 class="card-title mb-0 fw-bold"><i class="bx bx-history text-primary me-2"></i>Recent Bill Payments</h6>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Company</th>
                                <th>Consumer #</th>
                                <th>Month</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBills as $bill)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-dark">{{ $bill->biller->name }}</span>
                                    </td>
                                    <td>
                                        <span class="font-monospace small">{{ $bill->consumer_number }}</span>
                                    </td>
                                    <td>{{ $bill->billing_month ?? 'Current' }}</td>
                                    <td class="currency-pkr fw-bold text-danger">
                                        PKR {{ number_format($bill->amount, 2) }}
                                    </td>
                                    <td>
                                        <span class="badge bg-success">Paid</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $bill->created_at->format('d M Y') }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
