@extends('layouts.portal')

@section('title', 'Consumer Financing & Murabaha Simulator')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-calculator text-primary me-2"></i>Consumer Loan & Murabaha Simulator</h4>
        <span class="text-muted">Calculate monthly installments (EMI), KIBOR markup spread & amortization schedule</span>
    </div>
</div>

<div class="row">
    <!-- Calculator Form Card -->
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0 fw-bold">Financing Parameters</h5>
            </div>
            <div class="card-body pt-4">
                <div class="mb-4">
                    <label class="form-label fw-bold">Financing Facility Product</label>
                    <select id="loanProduct" class="form-select" onchange="updateRate()">
                        <option value="17.50" data-name="Auto Loan (Conventional)">Khazana Auto Loan (Rate: 17.50%)</option>
                        <option value="16.75" data-name="Islamic Home Murabaha">Khazana Islamic Home Murabaha (Rate: 16.75%)</option>
                        <option value="21.00" data-name="Personal Finance">Khazana Instant Personal Finance (Rate: 21.00%)</option>
                        <option value="14.50" data-name="SBP Solar Financing">SBP Concessionary Solar Energy Loan (Rate: 14.50%)</option>
                    </select>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-bold mb-0">Financing Principal (PKR)</label>
                        <span class="currency-pkr fw-bold text-primary" id="amountDisplay">PKR 3,000,000</span>
                    </div>
                    <input type="range" class="form-range" id="principalRange" min="200000" max="25000000" step="100000" value="3000000" oninput="calculateEmi()">
                    <input type="number" id="principalInput" class="form-control mt-2" value="3000000" oninput="syncFromInput()">
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-bold mb-0">Tenor Period</label>
                        <span class="fw-bold text-primary" id="tenorDisplay">36 Months (3 Years)</span>
                    </div>
                    <input type="range" class="form-range" id="tenorRange" min="12" max="84" step="12" value="36" oninput="calculateEmi()">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Annual Markup Rate (%)</label>
                    <input type="number" step="0.25" id="interestRate" class="form-control" value="17.50" oninput="calculateEmi()">
                    <div class="form-text small">KIBOR Benchmark + Commercial Bank Spread</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Calculation Results & Schedule Preview -->
    <div class="col-lg-7 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0 fw-bold"><i class="bx bx-pie-chart-alt text-success me-2"></i>Estimated Repayment Breakdown</h5>
            </div>
            <div class="card-body pt-4">
                <div class="row g-3 text-center mb-4">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded border">
                            <span class="text-muted small fw-semibold text-uppercase">Monthly Installment (EMI)</span>
                            <h3 class="currency-pkr text-primary fw-bold my-1" id="monthlyEmiDisplay">PKR 0.00</h3>
                            <small class="text-muted">Estimated per month</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded border">
                            <span class="text-muted small fw-semibold text-uppercase">Total Markup / Profit</span>
                            <h3 class="currency-pkr text-warning fw-bold my-1" id="totalInterestDisplay">PKR 0.00</h3>
                            <small class="text-muted">Profit charged over term</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded border">
                            <span class="text-muted small fw-semibold text-uppercase">Total Amount Payable</span>
                            <h3 class="currency-pkr text-success fw-bold my-1" id="totalPayableDisplay">PKR 0.00</h3>
                            <small class="text-muted">Principal + Profit</small>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold mb-2">Sample Amortization Schedule (First 6 Installments)</h6>
                <div class="table-responsive text-nowrap">
                    <table class="table table-sm table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Month #</th>
                                <th>Monthly Installment</th>
                                <th>Principal Component</th>
                                <th>Profit Component</th>
                                <th>Remaining Principal</th>
                            </tr>
                        </thead>
                        <tbody id="scheduleBody">
                            <!-- Populated dynamically via JS -->
                        </tbody>
                    </table>
                </div>

                <div class="text-center mt-4">
                    <button type="button" class="btn btn-success px-4" onclick="alert('Loan pre-eligibility application logged for customer profile!')">
                        <i class="bx bx-check-circle me-1"></i> Apply for Pre-Eligibility Assessment
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateRate() {
    const select = document.getElementById('loanProduct');
    document.getElementById('interestRate').value = select.value;
    calculateEmi();
}

function syncFromInput() {
    const val = parseFloat(document.getElementById('principalInput').value) || 0;
    document.getElementById('principalRange').value = val;
    calculateEmi();
}

function calculateEmi() {
    const P = parseFloat(document.getElementById('principalRange').value);
    document.getElementById('principalInput').value = P;
    document.getElementById('amountDisplay').innerText = 'PKR ' + P.toLocaleString();

    const N = parseInt(document.getElementById('tenorRange').value);
    const years = (N / 12).toFixed(1);
    document.getElementById('tenorDisplay').innerText = N + ' Months (' + years + ' Years)';

    const annualRate = parseFloat(document.getElementById('interestRate').value);
    const R = (annualRate / 100) / 12; // Monthly interest rate

    // Standard EMI formula: [P * R * (1+R)^N] / [(1+R)^N - 1]
    let emi = 0;
    if (R > 0) {
        emi = (P * R * Math.pow(1 + R, N)) / (Math.pow(1 + R, N) - 1);
    } else {
        emi = P / N;
    }

    const totalPayable = emi * N;
    const totalInterest = totalPayable - P;

    document.getElementById('monthlyEmiDisplay').innerText = 'PKR ' + Math.round(emi).toLocaleString();
    document.getElementById('totalInterestDisplay').innerText = 'PKR ' + Math.round(totalInterest).toLocaleString();
    document.getElementById('totalPayableDisplay').innerText = 'PKR ' + Math.round(totalPayable).toLocaleString();

    // Generate schedule
    let remaining = P;
    let html = '';
    for (let m = 1; m <= Math.min(N, 6); m++) {
        const intPart = remaining * R;
        const prinPart = emi - intPart;
        remaining = Math.max(0, remaining - prinPart);

        html += `<tr>
            <td class="font-monospace">Month ${m}</td>
            <td class="currency-pkr fw-bold">PKR ${Math.round(emi).toLocaleString()}</td>
            <td class="currency-pkr text-primary">PKR ${Math.round(prinPart).toLocaleString()}</td>
            <td class="currency-pkr text-warning">PKR ${Math.round(intPart).toLocaleString()}</td>
            <td class="currency-pkr">PKR ${Math.round(remaining).toLocaleString()}</td>
        </tr>`;
    }
    document.getElementById('scheduleBody').innerHTML = html;
}

document.addEventListener('DOMContentLoaded', calculateEmi);
</script>
@endsection
