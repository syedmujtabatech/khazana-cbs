@extends('layouts.cbs')

@section('title', 'Open New Bank Account')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="bx bx-plus-circle text-primary me-2"></i>Open Commercial Bank Account</h4>
                <span class="text-muted">Generates 14-digit local account number & SBP Mod-97 24-Character IBAN</span>
            </div>
            <a href="{{ route('cbs.accounts.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Account Opening Mandate & SBP Product Parameters</h5>
            </div>
            <div class="card-body pt-4">
                <form method="POST" action="{{ route('cbs.accounts.store') }}">
                    @csrf

                    <!-- Customer Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Select Customer (CIF) <span class="text-danger">*</span></label>
                        <select name="customer_profile_id" class="form-select @error('customer_profile_id') is-invalid @enderror" required>
                            <option value="">-- Select Customer CIF --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ (old('customer_profile_id') == $c->id || (isset($selectedCustomer) && $selectedCustomer->id == $c->id)) ? 'selected' : '' }}>
                                    {{ $c->cif_number }} - {{ $c->full_name }} (CNIC: {{ $c->cnic_passport }}) [{{ strtoupper($c->risk_rating) }} Risk]
                                </option>
                            @endforeach
                        </select>
                        @error('customer_profile_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text small">Don't see customer? <a href="{{ route('cbs.customers.create') }}">Onboard new CIF first</a>.</div>
                    </div>

                    <!-- Branch & Product Selection -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Commercial Branch <span class="text-danger">*</span></label>
                            <select name="branch_id" class="form-select @error('branch_id') is-invalid @enderror" required>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>
                                        {{ $b->name }} (Code: {{ $b->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Account Product <span class="text-danger">*</span></label>
                            <select name="account_product_id" class="form-select @error('account_product_id') is-invalid @enderror" required>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" {{ old('account_product_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->code }} - {{ $p->name }} ({{ $p->currency }} | Rate: {{ $p->interest_rate }}%)
                                    </option>
                                @endforeach
                            </select>
                            @error('account_product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <!-- Initial Opening Deposit -->
                    <div class="mb-4 p-3 bg-light rounded border">
                        <label class="form-label fw-bold">Initial Opening Deposit (PKR) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold">PKR</span>
                            <input type="number" step="0.01" name="initial_deposit" class="form-control @error('initial_deposit') is-invalid @enderror" placeholder="50000.00" value="{{ old('initial_deposit', '10000.00') }}" required>
                        </div>
                        <div class="form-text small">Initial deposit will post double-entry GL: Debit Branch Vault Cash (1010), Credit Customer Liability Account (2010/2020).</div>
                        @error('initial_deposit') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <!-- SBP IBAN Preview Notice -->
                    <div class="alert alert-info py-2 d-flex align-items-center mb-4">
                        <i class="bx bx-info-circle fs-4 me-2"></i>
                        <div>
                            <strong>Automatic IBAN Engine:</strong> A 24-character State Bank of Pakistan compliant IBAN starting with <code>PK..KHZN...</code> will be automatically minted upon creation using ISO 7064 Modulo-97 algorithm.
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('cbs.accounts.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="bx bx-check-circle me-1"></i> Open Account & Generate IBAN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
