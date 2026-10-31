@extends('layouts.cbs')

@section('title', 'Maker-Checker Queue')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-check-shield text-primary me-2"></i>Four-Eyes Maker-Checker Queue</h4>
        <span class="text-muted">Dual Control Authorization: Segregation of Duties for Financial & Administrative Operations</span>
    </div>
</div>

<!-- Status Filter Tabs -->
<ul class="nav nav-pills mb-4">
    <li class="nav-item">
        <a class="nav-link {{ $status === 'pending' ? 'active' : '' }}" href="{{ route('cbs.maker_checker.index', ['status' => 'pending']) }}">
            <i class="bx bx-time me-1"></i> Pending Authorization 
            @if($pendingCount > 0)
                <span class="badge bg-danger ms-1">{{ $pendingCount }}</span>
            @endif
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $status === 'approved' ? 'active' : '' }}" href="{{ route('cbs.maker_checker.index', ['status' => 'approved']) }}">
            <i class="bx bx-check-double me-1"></i> Approved & Executed ({{ $approvedCount }})
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $status === 'rejected' ? 'active' : '' }}" href="{{ route('cbs.maker_checker.index', ['status' => 'rejected']) }}">
            <i class="bx bx-x-circle me-1"></i> Rejected ({{ $rejectedCount }})
        </a>
    </li>
</ul>

<!-- Requests Table -->
<div class="card">
    <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Reference</th>
                    <th>Action Type</th>
                    <th>Amount (PKR)</th>
                    <th>Initiated By (Maker)</th>
                    <th>Branch</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                    <tr>
                        <td>
                            <span class="fw-bold text-dark font-monospace">{{ $req->reference_no }}</span>
                        </td>
                        <td>
                            <span class="badge bg-label-info">{{ ucwords(str_replace('_', ' ', $req->action_type)) }}</span>
                            @if($req->maker_notes)
                                <small class="d-block text-muted text-truncate" style="max-width: 200px;">{{ $req->maker_notes }}</small>
                            @endif
                        </td>
                        <td class="currency-pkr fw-bold">
                            @if($req->amount > 0)
                                PKR {{ number_format($req->amount, 2) }}
                            @else
                                <span class="text-muted">N/A (Admin)</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-semibold text-dark">{{ $req->maker->name ?? 'System' }}</span>
                            <small class="d-block text-muted">{{ ucwords(str_replace('_', ' ', $req->maker->role ?? '')) }}</small>
                        </td>
                        <td>
                            <span>{{ $req->branch->name ?? 'Head Office' }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $req->status_badge_class }}">{{ ucfirst($req->status) }}</span>
                        </td>
                        <td>
                            <small class="text-muted">{{ $req->created_at->format('d M, h:i A') }}</small>
                        </td>
                        <td>
                            <a href="{{ route('cbs.maker_checker.show', $req->id) }}" class="btn btn-sm btn-primary">
                                <i class="bx bx-show me-1"></i> Inspect & Authorize
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bx bx-check-circle fs-1 text-success d-block mb-2"></i>
                            No requests in '{{ ucfirst($status) }}' status.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-end">
        {{ $requests->links() }}
    </div>
</div>
@endsection
