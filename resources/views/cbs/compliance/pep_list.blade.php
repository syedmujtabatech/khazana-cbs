@extends('layouts.cbs')

@section('title', 'PEP & High Risk Matrix')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-user-voice text-danger me-2"></i>Politically Exposed Persons (PEP) & High-Risk Customers</h4>
        <span class="text-muted">Mandatory Enhanced Due Diligence (EDD) Registry under SBP AML/CFT Guidelines</span>
    </div>
</div>

<div class="card">
    <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>CIF Number</th>
                    <th>Customer Name & Salutation</th>
                    <th>CNIC / Passport</th>
                    <th>Occupation & Source of Funds</th>
                    <th>Classification</th>
                    <th>Total Accounts</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pepCustomers as $c)
                    <tr>
                        <td>
                            <span class="fw-bold text-primary font-monospace">{{ $c->cif_number }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded-circle bg-label-danger"><i class="bx bx-star"></i></span>
                                </div>
                                <div>
                                    <span class="fw-bold text-dark">{{ $c->full_name }}</span>
                                    <small class="d-block text-muted">{{ $c->city }}, Pakistan</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="font-monospace fw-semibold">{{ $c->cnic_passport }}</span>
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $c->occupation ?? 'Public Official' }}</span>
                            <small class="d-block text-muted">{{ $c->source_of_income ?? 'Business' }}</small>
                        </td>
                        <td>
                            @if($c->is_pep)
                                <span class="badge bg-danger"><i class="bx bx-star me-1"></i> PEP (Politically Exposed)</span>
                            @else
                                <span class="badge bg-warning text-dark">High Risk Tier-1</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-label-info">{{ $c->accounts->count() }} Accounts</span>
                        </td>
                        <td>
                            <a href="{{ route('cbs.customers.show', $c->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bx bx-show me-1"></i> Inspect 360&deg;
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No customers flagged under PEP or high-risk criteria.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-end">
        {{ $pepCustomers->links() }}
    </div>
</div>
@endsection
