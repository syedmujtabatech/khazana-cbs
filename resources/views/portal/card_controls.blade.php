@extends('layouts.portal')

@section('title', 'Debit Card Controls')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-credit-card text-primary me-2"></i>Virtual & Physical Debit Card Management</h4>
        <span class="text-muted">Instant Card Lock, E-Commerce Controls, Contactless & Daily Spending Limits</span>
    </div>
</div>

<div class="row">
    @forelse($cards as $card)
        <div class="col-lg-6 mb-4">
            <!-- ATM Visual Card -->
            <div class="card-atm mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-bold tracking-wider fs-5">KHAZANA BANK</span>
                    <span class="badge bg-white text-dark fw-bold text-uppercase">{{ $card->card_type }}</span>
                </div>
                <div class="chip mb-3"></div>
                <div class="font-monospace fs-3 fw-bold letter-spacing-2 mb-3">
                    {{ $card->card_number_masked }}
                </div>
                <div class="d-flex justify-content-between align-items-end small">
                    <div>
                        <div class="text-white-50" style="font-size: 0.65rem;">EMBOSSED NAME</div>
                        <div class="fw-bold text-uppercase">{{ $card->embossed_name }}</div>
                    </div>
                    <div>
                        <div class="text-white-50" style="font-size: 0.65rem;">GOOD THRU</div>
                        <div class="fw-bold font-monospace">{{ $card->expiry_month }}/{{ $card->expiry_year }}</div>
                    </div>
                    <div>
                        <span class="badge {{ $card->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                            {{ strtoupper($card->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Card Controls Form Card -->
            <div class="card shadow-sm">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">Card Security Switches & Limits</h5>
                    <form method="POST" action="{{ route('portal.cards.toggle_block', $card->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $card->status === 'active' ? 'btn-danger' : 'btn-success' }}">
                            <i class="bx {{ $card->status === 'active' ? 'bx-lock-alt' : 'bx-lock-open-alt' }} me-1"></i>
                            {{ $card->status === 'active' ? 'Temporarily Freeze Card' : 'Unfreeze Card' }}
                        </button>
                    </form>
                </div>
                <div class="card-body pt-4">
                    <form method="POST" action="{{ route('portal.cards.update', $card->id) }}">
                        @csrf

                        <!-- Feature Toggles -->
                        <div class="list-group list-group-flush mb-4">
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <strong class="text-dark d-block">E-Commerce & Online Shopping</strong>
                                    <small class="text-muted">Allow payments on online stores, Daraz, Netflix, foodpanda</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_ecommerce_enabled" value="1" {{ $card->is_ecommerce_enabled ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <strong class="text-dark d-block">International Cross-Border Transactions</strong>
                                    <small class="text-muted">Allow payments abroad or in foreign currencies</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_international_enabled" value="1" {{ $card->is_international_enabled ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>

                        <!-- Limit Sliders / Inputs -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Daily ATM Cash Withdrawal Limit (PKR)</label>
                            <input type="number" step="5000" name="daily_atm_limit" class="form-control" value="{{ $card->daily_atm_limit }}">
                            <div class="form-text small">Maximum allowed per calendar day: PKR 500,000</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Daily POS Merchant Spending Limit (PKR)</label>
                            <input type="number" step="10000" name="daily_pos_limit" class="form-control" value="{{ $card->daily_pos_limit }}">
                            <div class="form-text small">Maximum in-store POS limit: PKR 1,000,000</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Daily E-Commerce Online Limit (PKR)</label>
                            <input type="number" step="5000" name="daily_ecom_limit" class="form-control" value="{{ $card->daily_ecom_limit }}">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold">
                            <i class="bx bx-save me-1"></i> Save Card Security Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <i class="bx bx-credit-card fs-1 text-muted"></i>
            <p class="text-muted mt-2">No debit cards currently linked to this customer profile.</p>
        </div>
    @endforelse
</div>
@endsection
