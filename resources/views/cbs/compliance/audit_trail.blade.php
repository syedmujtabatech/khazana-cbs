@extends('layouts.cbs')

@section('title', 'Forensic Security Audit Trail')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-history text-primary me-2"></i>Forensic Security & System Audit Trail</h4>
        <span class="text-muted">Immutable system audit logs recording authentication, maker-checker, and ledger events</span>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('cbs.compliance.audit') }}" class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Search Event Description or IP</label>
                <input type="text" name="search" class="form-control" placeholder="Search description, event or IP..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Severity</label>
                <select name="severity" class="form-select">
                    <option value="">All Severities</option>
                    <option value="info" {{ request('severity') == 'info' ? 'selected' : '' }}>Info</option>
                    <option value="notice" {{ request('severity') == 'notice' ? 'selected' : '' }}>Notice</option>
                    <option value="warning" {{ request('severity') == 'warning' ? 'selected' : '' }}>Warning</option>
                    <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>Critical</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100"><i class="bx bx-filter-alt me-1"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Logs Table -->
<div class="card">
    <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Timestamp</th>
                    <th>User</th>
                    <th>Event Type</th>
                    <th>Severity</th>
                    <th>IP Address & Agent</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $l)
                    <tr>
                        <td>
                            <small class="text-muted font-monospace">{{ $l->created_at->format('Y-m-d H:i:s') }}</small>
                        </td>
                        <td>
                            <span class="fw-semibold text-dark">{{ $l->user->name ?? 'System Process' }}</span>
                            @if($l->user)
                                <small class="d-block text-muted">{{ ucwords(str_replace('_', ' ', $l->user->role)) }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-label-secondary font-monospace">{{ $l->event_type }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $l->severity === 'critical' ? 'bg-danger' : ($l->severity === 'warning' ? 'bg-warning' : 'bg-info') }}">
                                {{ ucfirst($l->severity) }}
                            </span>
                        </td>
                        <td>
                            <span class="font-monospace small d-block">{{ $l->ip_address }}</span>
                            <small class="text-muted text-truncate d-inline-block" style="max-width: 150px;" title="{{ $l->user_agent }}">{{ $l->user_agent }}</small>
                        </td>
                        <td>
                            <span class="small">{{ $l->description }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No audit trail records match criteria.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-end">
        {{ $logs->links() }}
    </div>
</div>
@endsection
