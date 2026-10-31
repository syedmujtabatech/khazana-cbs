@extends('layouts.cbs')

@section('title', 'Cash Withdrawal')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="bx bx-up-arrow-circle text-warning me-2"></i>Cash Withdrawal & Encashment</h4>
                <span class="text-muted">Dispense Physical Cash from Vault | Double-Entry: GL 2010/2020 &rarr; GL 1010</span>
            </div>
            <a href="{{ route('cbs.teller.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back to Till
            </a>
        </div>

        <div class="card shadow-sm border-top border-warning border-3">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0 fw-bold">Withdrawal Slip / Cheque Encashment Parameters</h5>
            </div>
            <div class="card-body pt-4">
                <form method="POST" action="{{ route('cbs.teller.withdrawal.process') }}">
                    @csrf

                    <!-- Source Account -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Source Debit Account <span class="text-danger">*</span></label>
                        <select name="bank_account_id" class="form-select @error('bank_account_id') is-invalid @enderror" required>
                            <option value="">-- Select Source Account --</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ (old('bank_account_id') == $acc->id || (isset($selectedAccount) && $selectedAccount->id == $acc->id)) ? 'selected' : '' }}>
                                    {{ $acc->account_number }} - {{ $acc->account_title }} (Available: PKR {{ number_format($acc->available_balance, 2) }})
                                </option>
                            @endforeach
                        </select>
                        @error('bank_account_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Withdrawal Amount -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Withdrawal Amount (PKR) <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text fw-bold bg-light">PKR</span>
                            <input type="number" step="0.01" name="amount" class="form-control fw-bold fs-4 @error('amount') is-invalid @enderror" placeholder="50000.00" value="{{ old('amount') }}" required>
                        </div>
                        <div class="form-text small mt-1">
                            Funds must be immediately available. Cheques &ge; PKR 100,000 route automatically to Branch Manager for Four-Eyes approval.
                        </div>
                        @error('amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <!-- Cheque Number -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Cheque Leaf Number</label>
                        <input type="text" name="cheque_number" class="form-control" placeholder="e.g. 00982101" value="{{ old('cheque_number') }}">
                        <div class="form-text small">Enter customer presented cheque leaf number for audit reconciliation.</div>
                    </div>

                    <!-- Narration -->
                    <div class="mb-4">
                        <label class="form-label">Withdrawal Narration</label>
                        <input type="text" name="narration" class="form-control" placeholder="Cash withdrawal counter slip" value="{{ old('narration') }}">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('cbs.teller.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-warning px-4 fw-bold">
                            <i class="bx bx-check me-1"></i> Verify & Dispense Cash
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
