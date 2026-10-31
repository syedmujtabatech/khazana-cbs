@extends('layouts.cbs')

@section('title', 'Customer Master File (CIF)')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-id-card text-primary me-2"></i>Customer Master Directory (CIF)</h4>
        <span class="text-muted">State Bank of Pakistan KYC & Biometric Customer Information Records</span>
    </div>
    <a href="{{ route('cbs.customers.create') }}" class="btn btn-primary">
        <i class="bx bx-user-plus me-1"></i> Onboard New Customer (CIF)
    </a>
</div>

<!-- Search & Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('cbs.customers.index') }}" class="row g-3">
            <div class="col-md-5">
                <label class="form-label small fw-semibold">Search CIF, CNIC or Name</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search by CIF, CNIC (e.g. 42101-...) or Name" value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">AML Risk Category</label>
                <select name="risk_rating" class="form-select">
                    <option value="">All Risk Ratings</option>
                    <option value="low" {{ request('risk_rating') == 'low' ? 'selected' : '' }}>Low Risk</option>
                    <option value="medium" {{ request('risk_rating') == 'medium' ? 'selected' : '' }}>Medium Risk</option>
                    <option value="high" {{ request('risk_rating') == 'high' ? 'selected' : '' }}>High Risk</option>
                    <option value="pep" {{ request('risk_rating') == 'pep' ? 'selected' : '' }}>Politically Exposed Person (PEP)</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2"><i class="bx bx-filter-alt me-1"></i> Filter</button>
                <a href="{{ route('cbs.customers.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Customers Table -->
<div class="card">
    <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>CIF Number</th>
                    <th>Customer Name</th>
                    <th>CNIC / Passport</th>
                    <th>City / Address</th>
                    <th>Accounts</th>
                    <th>PEP & Risk</th>
                    <th>KYC Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $c)
                    <tr>
                        <td>
                            <span class="fw-bold text-primary">{{ $c->cif_number }}</span>
                            <small class="d-block text-muted">{{ $c->created_at->format('d M Y') }}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded-circle bg-label-info">{{ substr($c->first_name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <span class="fw-semibold text-dark">{{ $c->full_name }}</span>
                                    <small class="d-block text-muted">{{ $c->occupation ?? 'Customer' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="font-monospace fw-semibold">{{ $c->cnic_passport }}</span>
                        </td>
                        <td>
                            <span>{{ $c->city }}</span>
                            <small class="d-block text-muted text-truncate" style="max-width: 150px;">{{ $c->residential_address }}</small>
                        </td>
                        <td>
                            <span class="badge bg-label-primary">{{ $c->accounts->count() }} Accounts</span>
                        </td>
                        <td>
                            @if($c->is_pep || $c->risk_rating === 'pep')
                                <span class="badge bg-danger"><i class="bx bx-star me-1"></i> PEP (High Risk)</span>
                            @elseif($c->risk_rating === 'high')
                                <span class="badge bg-warning text-dark">High Risk</span>
                            @else
                                <span class="badge bg-label-success">Low Risk</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-label-success"><i class="bx bx-check-circle me-1"></i> Verified</span>
                        </td>
                        <td>
                            <a href="{{ route('cbs.customers.show', $c->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bx bx-show me-1"></i> 360&deg; View
                            </a>
                            <a href="{{ route('cbs.accounts.create', ['customer_id' => $c->id]) }}" class="btn btn-sm btn-outline-success">
                                <i class="bx bx-plus me-1"></i> Open A/C
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No customer records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-end">
        {{ $customers->links() }}
    </div>
</div>
@endsection
