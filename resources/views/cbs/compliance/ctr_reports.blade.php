@extends('layouts.cbs')

@section('title', 'SBP CTR Reports')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-file-blank text-danger me-2"></i>State Bank of Pakistan — Cash Transaction Reports (CTR)</h4>
        <span class="text-muted">Mandatory Reporting under Anti-Money Laundering Act, 2010 (Transactions &ge; PKR 2.0 Million)</span>
    </div>
    <div>
        <button onclick="window.print()" class="btn btn-outline-danger">
            <i class="bx bx-printer me-1"></i> Print CTR Disclosures
        </button>
    </div>
</div>

<div class="card">
    <div class="card-header bg-label-danger py-3">
        <h6 class="card-title mb-0 text-danger fw-bold">
            <i class="bx bx-info-circle me-1"></i> SBP FMU Compliance Threshold: PKR 2,000,000.00
        </h6>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>CTR Ref</th>
                    <th>Customer Name / CIF</th>
                    <th>CNIC / Passport</th>
                    <th>Branch</th>
                    <th>Cash Volume</th>
                    <th>Officer / Maker</th>
                    <th>Reported Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ctrTransactions as $txn)
                    @php
                        $acc = $txn->destinationAccount ?? $txn->sourceAccount;
                        $cust = $acc ? $acc->customer : null;
                    @endphp
                    <tr>
                        <td>
                            <span class="fw-bold text-danger font-monospace">{{ $txn->transaction_reference }}</span>
                        </td>
                        <td>
                            <span class="fw-semibold text-dark">{{ $cust ? $cust->full_name : 'Counter Walk-in' }}</span>
                            <small class="d-block text-muted">CIF: {{ $cust->cif_number ?? 'N/A' }}</small>
                        </td>
                        <td>
                            <span class="font-monospace fw-semibold">{{ $cust->cnic_passport ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <span>{{ $txn->branch->name ?? 'Corporate Main' }}</span>
                        </td>
                        <td class="currency-pkr fw-bold text-danger fs-6">
                            PKR {{ number_format($txn->amount, 2) }}
                        </td>
                        <td>
                            <span>{{ $txn->maker->name ?? 'Teller Desk' }}</span>
                        </td>
                        <td>
                            <small class="text-muted">{{ $txn->created_at->format('d M Y, h:i A') }}</small>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No CTR transactions recorded.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-end">
        {{ $ctrTransactions->links() }}
    </div>
</div>
@endsection
