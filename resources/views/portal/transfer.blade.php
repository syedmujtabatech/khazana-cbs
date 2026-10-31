@extends('layouts.portal')

@section('title', 'Instant Funds Transfer')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="bx bx-send text-primary me-2"></i>Instant Digital Funds Transfer</h4>
                <span class="text-muted">Transfer money within Khazana, 1LINK Interbank (IBFT) or SBP Raast Real-Time</span>
            </div>
            <a href="{{ route('portal.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-header border-bottom">
                <!-- Transfer Type Selector Pills -->
                <ul class="nav nav-pills card-header-pills" id="transferTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-bold" id="internal-tab" data-bs-toggle="pill" data-bs-target="#internal-pane" type="button" role="tab" onclick="setTransferType('internal')">
                            <i class="bx bx-transfer-alt me-1"></i> Khazana Internal
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold" id="ibft-tab" data-bs-toggle="pill" data-bs-target="#ibft-pane" type="button" role="tab" onclick="setTransferType('1link_ibft')">
                            <i class="bx bx-buildings me-1"></i> 1LINK IBFT (Other Banks)
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold" id="raast-tab" data-bs-toggle="pill" data-bs-target="#raast-pane" type="button" role="tab" onclick="setTransferType('raast_instant')">
                            <i class="bx bx-zap me-1 text-warning"></i> SBP Raast Instant
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body pt-4">
                <form method="POST" action="{{ route('portal.transfer.process') }}">
                    @csrf
                    <input type="hidden" name="transfer_type" id="transfer_type" value="internal">

                    <!-- Source Account Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Select Debit Account <span class="text-danger">*</span></label>
                        <select name="source_account_id" class="form-select @error('source_account_id') is-invalid @enderror" required>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ old('source_account_id') == $acc->id ? 'selected' : '' }}>
                                    {{ $acc->account_number }} - {{ $acc->account_title }} (Available: PKR {{ number_format($acc->available_balance, 2) }})
                                </option>
                            @endforeach
                        </select>
                        @error('source_account_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Dynamic Channels -->
                    <div class="tab-content border-0 p-0 mb-4" id="transferTabContent">
                        <!-- 1. Internal Transfer Pane -->
                        <div class="tab-pane fade show active" id="internal-pane" role="tabpanel">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Destination Khazana Account Number or IBAN <span class="text-danger">*</span></label>
                                <input type="text" name="dest_account_number" class="form-control font-monospace" placeholder="e.g. 0010100201 or PK36KHZN..." value="{{ old('dest_account_number') }}">
                                <div class="form-text small">Instant zero-fee transfer to any Khazana Bank account.</div>
                            </div>
                        </div>

                        <!-- 2. 1LINK IBFT Pane -->
                        <div class="tab-pane fade" id="ibft-pane" role="tabpanel">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Destination Bank <span class="text-danger">*</span></label>
                                    <select name="beneficiary_bank" class="form-select">
                                        <option value="Habib Bank Limited (HBL)">Habib Bank Limited (HBL)</option>
                                        <option value="Meezan Bank Limited">Meezan Bank Limited (Islamic)</option>
                                        <option value="United Bank Limited (UBL)">United Bank Limited (UBL)</option>
                                        <option value="MCB Bank Limited">MCB Bank Limited</option>
                                        <option value="Bank Alfalah">Bank Alfalah</option>
                                        <option value="Standard Chartered Pakistan">Standard Chartered Pakistan</option>
                                        <option value="Faysal Bank">Faysal Bank Limited</option>
                                        <option value="Allied Bank Limited (ABL)">Allied Bank Limited (ABL)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Beneficiary IBAN or Account # <span class="text-danger">*</span></label>
                                    <input type="text" name="beneficiary_iban" class="form-control font-monospace" placeholder="PK45HABB0001009876543210" value="{{ old('beneficiary_iban') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Beneficiary Account Title <span class="text-danger">*</span></label>
                                    <input type="text" name="beneficiary_title" class="form-control" placeholder="Beneficiary Full Name" value="{{ old('beneficiary_title') }}">
                                </div>
                            </div>
                        </div>

                        <!-- 3. Raast Instant Pane -->
                        <div class="tab-pane fade" id="raast-pane" role="tabpanel">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Raast ID / Registered Mobile Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-mobile-alt"></i></span>
                                    <input type="text" name="raast_id" class="form-control" placeholder="+923001234567 or 03001234567" value="{{ old('raast_id') }}">
                                </div>
                                <div class="form-text small">State Bank of Pakistan Raast instant switch: 24/7/365 settlement with 0.00 fees.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Transfer Amount -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Transfer Amount (PKR) <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text fw-bold bg-light">PKR</span>
                            <input type="number" step="0.01" name="amount" class="form-control fw-bold fs-4 @error('amount') is-invalid @enderror" placeholder="25000.00" value="{{ old('amount') }}" required>
                        </div>
                        @error('amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <!-- Narration / Purpose of Payment -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Payment Purpose / Narration</label>
                        <input type="text" name="narration" class="form-control" placeholder="e.g. Invoice payment, Family support, Rent" value="{{ old('narration') }}">
                    </div>

                    <!-- Security Transaction PIN -->
                    <div class="mb-4 p-3 bg-light rounded border">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold mb-0"><i class="bx bx-shield-quarter text-primary me-1"></i> 4-Digit Transaction Security PIN <span class="text-danger">*</span></label>
                            <span class="badge bg-label-info">Default demo PIN: 1122 or 1234</span>
                        </div>
                        <input type="password" maxlength="4" name="pin" class="form-control text-center font-monospace fs-4 fw-bold @error('pin') is-invalid @enderror" style="letter-spacing: 10px;" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;" required>
                        @error('pin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text small">Authorizes immediate cryptographic funds settlement on SBP/1LINK switch.</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('portal.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="bx bx-send me-1"></i> Authorize & Transfer Funds
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function setTransferType(type) {
    document.getElementById('transfer_type').value = type;
}
</script>
@endsection
