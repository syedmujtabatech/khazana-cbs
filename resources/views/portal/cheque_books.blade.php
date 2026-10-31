@extends('layouts.portal')

@section('title', 'Cheque Book Services')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-book-bookmark text-primary me-2"></i>Cheque Book Requisition & Leaf Status</h4>
        <span class="text-muted">Order CTS Compliant Magnetic Ink Cheque Books & Track Individual Leaves</span>
    </div>
</div>

<div class="row">
    <!-- Request Cheque Book Card -->
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0 fw-bold">Order New Cheque Book</h5>
            </div>
            <div class="card-body pt-4">
                <form method="POST" action="{{ route('portal.cheque_books.request') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-bold">Select Account <span class="text-danger">*</span></label>
                        <select name="bank_account_id" class="form-select" required>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">
                                    {{ $acc->account_number }} ({{ $acc->account_title }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Number of Cheque Leaves <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-4">
                                <input type="radio" class="btn-check" name="leaves" id="leaf25" value="25" checked>
                                <label class="btn btn-outline-primary w-100 py-3 text-center" for="leaf25">
                                    <div class="fs-4 fw-bold">25</div>
                                    <small>Leaves</small>
                                </label>
                            </div>
                            <div class="col-4">
                                <input type="radio" class="btn-check" name="leaves" id="leaf50" value="50">
                                <label class="btn btn-outline-primary w-100 py-3 text-center" for="leaf50">
                                    <div class="fs-4 fw-bold">50</div>
                                    <small>Leaves</small>
                                </label>
                            </div>
                            <div class="col-4">
                                <input type="radio" class="btn-check" name="leaves" id="leaf100" value="100">
                                <label class="btn btn-outline-primary w-100 py-3 text-center" for="leaf100">
                                    <div class="fs-4 fw-bold">100</div>
                                    <small>Leaves</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info py-2 small mb-4">
                        <i class="bx bx-info-circle me-1"></i> Cheque book will be encoded with standard NIFT CTS clearing MICR band and dispatched to your home branch within 2 business days.
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                        <i class="bx bx-check me-1"></i> Confirm & Request Cheque Book
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Active Cheque Books Table -->
    <div class="col-lg-7 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0 fw-bold"><i class="bx bx-list-check text-primary me-2"></i>Issued Cheque Books & Series</h5>
            </div>
            <div class="card-body">
                @forelse($chequeBooks as $cb)
                    <div class="p-3 mb-3 border rounded bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold font-monospace text-primary fs-6">{{ $cb->book_number }}</span>
                            <span class="badge bg-label-success">{{ ucfirst($cb->status) }}</span>
                        </div>
                        <div class="small text-muted mb-2">
                            Series: <strong>{{ $cb->start_leaf }}</strong> to <strong>{{ $cb->end_leaf }}</strong> ({{ $cb->total_leaves }} Leaves) &bull; Issued: {{ $cb->issued_at ? $cb->issued_at->format('d M Y') : 'Active' }}
                        </div>

                        <!-- Sample Leaves status -->
                        <div class="d-flex flex-wrap gap-1 mt-2">
                            @foreach($cb->leaves as $leaf)
                                <span class="badge {{ $leaf->status === 'unused' ? 'bg-secondary' : 'bg-success' }} font-monospace" title="Leaf #{{ $leaf->leaf_number }} - {{ ucfirst($leaf->status) }}">
                                    #{{ substr($leaf->leaf_number, -4) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center py-5">No cheque books issued yet. Submit request on the left.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
