@extends('layouts.cbs')

@section('title', 'End of Day (EOD) Batch Processing')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-refresh text-primary me-2"></i>End of Day (EOD) & BOD Batch Engine</h4>
        <span class="text-muted">Daily Profit Accruals, Term Deposit Settlement, Trial Balance Reconciliation & Day-End Roll</span>
    </div>
</div>

<!-- EOD Trigger Card -->
<div class="card mb-4 bg-light border">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h5 class="fw-bold mb-2 text-dark"><i class="bx bx-cog text-primary me-2"></i>Trigger Daily System Day-End Settlement</h5>
                <p class="text-muted mb-3">
                    Executing EOD batch will calculate daily PLS profit accruals across all savings accounts, liquidate/renew matured TDR certificates, reconcile branch vault balances against General Ledger Cash (GL 1010), and verify Trial Balance integrity.
                </p>
                <div class="d-flex flex-wrap gap-3 small">
                    <div><i class="bx bx-check text-success me-1"></i> Active Deposit Accounts: <strong>{{ $activeAccountsCount }}</strong></div>
                    <div><i class="bx bx-check text-success me-1"></i> PLS Savings Accounts: <strong>{{ $savingsCount }}</strong></div>
                    <div><i class="bx bx-calendar text-primary me-1"></i> Today's Business Date: <strong>{{ date('d F Y') }}</strong></div>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <form method="POST" action="{{ route('cbs.eod.run') }}">
                    @csrf
                    <input type="hidden" name="business_date" value="{{ date('Y-m-d') }}">
                    <button type="submit" class="btn btn-primary btn-lg shadow-sm fw-bold">
                        <i class="bx bx-play-circle me-1"></i> Run EOD Batch Now
                    </button>
                </form>
                <div class="text-muted small mt-2">Requires Branch Manager or Super Admin role</div>
            </div>
        </div>
    </div>
</div>

<!-- EOD Historical Execution Logs -->
<div class="card">
    <div class="card-header border-bottom">
        <h5 class="card-title mb-0 fw-bold"><i class="bx bx-history text-primary me-2"></i>EOD Batch Settlement Run History</h5>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Batch Reference</th>
                    <th>Business Date</th>
                    <th>Triggered By</th>
                    <th>Accounts Processed</th>
                    <th>Profit Accrued</th>
                    <th>GL Balanced</th>
                    <th>Vault Discrepancy</th>
                    <th>Status</th>
                    <th>Execution Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($batchLogs as $b)
                    <tr>
                        <td>
                            <span class="font-monospace fw-bold text-dark">{{ $b->batch_reference }}</span>
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $b->business_date ? $b->business_date->format('d M Y') : 'N/A' }}</span>
                        </td>
                        <td>
                            <span>{{ $b->triggerUser->name ?? 'System' }}</span>
                        </td>
                        <td>
                            <span class="badge bg-label-info">{{ $b->accounts_processed }} Accounts</span>
                        </td>
                        <td class="currency-pkr fw-bold text-success">
                            PKR {{ number_format($b->total_profit_accrued, 2) }}
                        </td>
                        <td>
                            @if($b->gl_balanced)
                                <span class="badge bg-success"><i class="bx bx-check me-1"></i> Balanced</span>
                            @else
                                <span class="badge bg-danger"><i class="bx bx-x me-1"></i> Variance</span>
                            @endif
                        </td>
                        <td>
                            <span class="currency-pkr {{ $b->vault_discrepancy > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                                PKR {{ number_format($b->vault_discrepancy, 2) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-label-success">{{ ucfirst($b->status) }}</span>
                        </td>
                        <td>
                            <small class="text-muted">{{ $b->started_at->format('h:i:s A') }}</small>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No EOD batch runs recorded yet. Click "Run EOD Batch Now" above to execute.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-end">
        {{ $batchLogs->links() }}
    </div>
</div>
@endsection
