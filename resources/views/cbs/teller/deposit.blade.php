@extends('layouts.cbs')

@section('title', 'Fast Cash Deposit')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="bx bx-down-arrow-circle text-success me-2"></i>Cash Deposit Slip Desk</h4>
                <span class="text-muted">Direct Counter Deposit | Double-Entry Posting: GL 1010 &rarr; GL 2010/2020</span>
            </div>
            <a href="{{ route('cbs.teller.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back to Till
            </a>
        </div>

        <div class="card shadow-sm border-top border-success border-3">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0 fw-bold">Cash Deposit Voucher Parameters</h5>
            </div>
            <div class="card-body pt-4">
                <form method="POST" action="{{ route('cbs.teller.deposit.process') }}">
                    @csrf

                    <!-- Target Account -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Beneficiary Bank Account <span class="text-danger">*</span></label>
                        <select name="bank_account_id" class="form-select @error('bank_account_id') is-invalid @enderror" required>
                            <option value="">-- Select Beneficiary Account --</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ (old('bank_account_id') == $acc->id || (isset($selectedAccount) && $selectedAccount->id == $acc->id)) ? 'selected' : '' }}>
                                    {{ $acc->account_number }} - {{ $acc->account_title }} (Bal: PKR {{ number_format($acc->available_balance, 2) }}) [{{ $acc->branch->name }}]
                                </option>
                            @endforeach
                        </select>
                        @error('bank_account_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Deposit Amount -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Cash Deposit Amount (PKR) <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text fw-bold bg-light">PKR</span>
                            <input type="number" step="0.01" name="amount" id="depositAmount" class="form-control fw-bold fs-4 @error('amount') is-invalid @enderror" placeholder="100000.00" value="{{ old('amount') }}" required>
                        </div>
                        <div class="form-text small mt-1">
                            <strong>Regulatory Note:</strong> Deposits &ge; PKR 100,000 require Maker-Checker authorization. Deposits &ge; PKR 2,000,000 trigger mandatory State Bank of Pakistan (SBP) Currency Transaction Reporting (CTR).
                        </div>
                        @error('amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <!-- Depositor Info -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Depositor Name (if third-party)</label>
                            <input type="text" name="depositor_name" class="form-control" placeholder="Self or Agent Name" value="{{ old('depositor_name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Depositor CNIC</label>
                            <input type="text" name="depositor_cnic" class="form-control" placeholder="42101-XXXXXXX-X" value="{{ old('depositor_cnic') }}">
                        </div>
                    </div>

                    <!-- Narration -->
                    <div class="mb-4">
                        <label class="form-label">Deposit Narration / Voucher Memo</label>
                        <input type="text" name="narration" class="form-control" placeholder="Cash counter deposit slip" value="{{ old('narration') }}">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('cbs.teller.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-success px-4 fw-bold">
                            <i class="bx bx-check me-1"></i> Accept Cash & Post to Ledger
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
