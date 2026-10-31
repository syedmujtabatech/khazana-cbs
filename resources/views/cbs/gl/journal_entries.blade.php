@extends('layouts.cbs')

@section('title', 'GL Journal Book')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-book-content text-primary me-2"></i>Double-Entry Journal Vouchers</h4>
        <span class="text-muted">Forensic Accounting Audit Trail: Immutable Debits and Credits with Storno Tracking</span>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('cbs.gl.journal') }}" class="row g-3">
            <div class="col-md-5">
                <label class="form-label small fw-semibold">Batch Reference</label>
                <input type="text" name="batch" class="form-control" placeholder="Search by batch ref (e.g. GLB-...)" value="{{ request('batch') }}">
            </div>
            <div class="col-md-5">
                <label class="form-label small fw-semibold">GL Account</label>
                <select name="account_id" class="form-select">
                    <option value="">All GL Accounts</option>
                    @foreach($glAccounts as $g)
                        <option value="{{ $g->id }}" {{ request('account_id') == $g->id ? 'selected' : '' }}>
                            {{ $g->account_code }} - {{ $g->account_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100"><i class="bx bx-filter-alt me-1"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Journal Table -->
<div class="card">
    <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Batch / Date</th>
                    <th>GL Account</th>
                    <th>Branch</th>
                    <th>Entry Type</th>
                    <th>Amount</th>
                    <th>Narration</th>
                    <th>Authorizer</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entries as $entry)
                    <tr class="{{ $entry->is_reversal ? 'table-warning' : '' }}">
                        <td>
                            <span class="font-monospace fw-bold text-dark d-block">{{ $entry->batch_reference }}</span>
                            <small class="text-muted">{{ $entry->value_date ? $entry->value_date->format('d M Y') : 'N/A' }}</small>
                            @if($entry->is_reversal)
                                <span class="badge bg-danger small">STORNO REVERSAL</span>
                            @endif
                        </td>
                        <td>
                            <span class="font-monospace fw-bold text-primary">{{ $entry->account->account_code ?? 'GL' }}</span>
                            <span class="fw-semibold text-dark d-block">{{ $entry->account->account_name ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <span>{{ $entry->branch->name ?? 'Head Office' }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $entry->entry_type === 'debit' ? 'bg-label-primary' : 'bg-label-success' }} text-uppercase">
                                {{ $entry->entry_type }}
                            </span>
                        </td>
                        <td class="currency-pkr fw-bold {{ $entry->entry_type === 'debit' ? 'text-primary' : 'text-success' }}">
                            PKR {{ number_format($entry->amount, 2) }}
                        </td>
                        <td>
                            <span class="small text-truncate d-inline-block" style="max-width: 250px;" title="{{ $entry->narration }}">
                                {{ $entry->narration }}
                            </span>
                        </td>
                        <td>
                            <small class="text-muted">{{ $entry->creator->name ?? 'System' }}</small>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No journal vouchers match criteria.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-end">
        {{ $entries->links() }}
    </div>
</div>
@endsection
