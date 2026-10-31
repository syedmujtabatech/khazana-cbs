@extends('layouts.cbs')

@section('title', 'Authorize Request - ' . $request->reference_no)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-check-shield text-primary me-2"></i>Maker-Checker Request Verification</h4>
        <span class="text-muted">Request Ref: <strong>{{ $request->reference_no }}</strong> | Status: <span class="badge {{ $request->status_badge_class }}">{{ ucfirst($request->status) }}</span></span>
    </div>
    <a href="{{ route('cbs.maker_checker.index') }}" class="btn btn-outline-secondary">
        <i class="bx bx-arrow-back me-1"></i> Back to Queue
    </a>
</div>

<div class="row">
    <!-- Main Request Details -->
    <div class="col-lg-8 mb-4">
        <div class="card mb-4">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0 fw-bold">Operation Details & Financial Payload</h5>
            </div>
            <div class="card-body pt-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="text-muted small d-block">Action Type</label>
                        <span class="badge bg-label-primary fs-6">{{ ucwords(str_replace('_', ' ', $request->action_type)) }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small d-block">Transaction Amount</label>
                        <span class="currency-pkr fw-bold fs-4 text-dark">PKR {{ number_format($request->amount, 2) }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small d-block">Initiated by Maker Officer</label>
                        <strong>{{ $request->maker->name }}</strong> ({{ $request->maker->email }})
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small d-block">Submission Timestamp</label>
                        <span>{{ $request->created_at->format('d M Y, h:i:s A') }}</span>
                    </div>
                </div>

                @if($request->maker_notes)
                    <div class="alert alert-secondary py-2 mb-4">
                        <strong>Maker Notes:</strong> {{ $request->maker_notes }}
                    </div>
                @endif

                <h6 class="fw-bold border-bottom pb-2 mb-3">Decoded Parameter Payload (Audit Trail)</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 35%;">Parameter Key</th>
                                <th>Submitted Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($request->payload as $key => $val)
                                <tr>
                                    <td class="font-monospace text-primary fw-semibold">{{ $key }}</td>
                                    <td>
                                        @if(is_array($val))
                                            <pre class="mb-0 small">{{ json_encode($val, JSON_PRETTY_PRINT) }}</pre>
                                        @else
                                            <span class="fw-semibold">{{ (string)$val }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($request->status === 'approved')
                    <div class="alert alert-success mt-4">
                        <i class="bx bx-check-double me-1"></i> <strong>Authorized & Settled by Checker:</strong> {{ $request->checker->name ?? 'Checker' }} on {{ $request->actioned_at ? $request->actioned_at->format('d M Y, h:i A') : 'N/A' }}
                        <div class="mt-1 small">Checker Notes: {{ $request->checker_notes ?? 'Approved' }}</div>
                    </div>
                @elseif($request->status === 'rejected')
                    <div class="alert alert-danger mt-4">
                        <i class="bx bx-x me-1"></i> <strong>Rejected by Checker:</strong> {{ $request->checker->name ?? 'Checker' }} on {{ $request->actioned_at ? $request->actioned_at->format('d M Y, h:i A') : 'N/A' }}
                        <div class="mt-1 small">Reason: {{ $request->checker_notes }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Checker Decision Panel -->
    <div class="col-lg-4 mb-4">
        @if($request->status === 'pending')
            <!-- Authorize Action Card -->
            <div class="card border-success border mb-4">
                <div class="card-header bg-label-success">
                    <h5 class="card-title mb-0 text-success fw-bold"><i class="bx bx-check-circle me-1"></i> Authorize & Execute</h5>
                </div>
                <div class="card-body pt-3">
                    <p class="small text-muted">
                        Authorizing will immediately execute the double-entry accounting ledger entries and credit/debit customer balances.
                    </p>
                    <form method="POST" action="{{ route('cbs.maker_checker.approve', $request->id) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Checker Verification Remarks</label>
                            <input type="text" name="checker_notes" class="form-control form-control-sm" placeholder="Verified supporting vouchers & vouchers clean." value="Verified and approved.">
                        </div>
                        <button type="submit" class="btn btn-success w-100 fw-bold">
                            <i class="bx bx-check-double me-1"></i> Authorize & Execute Transaction
                        </button>
                    </form>
                </div>
            </div>

            <!-- Reject Action Card -->
            <div class="card border-danger border">
                <div class="card-header bg-label-danger">
                    <h5 class="card-title mb-0 text-danger fw-bold"><i class="bx bx-x-circle me-1"></i> Reject Operation</h5>
                </div>
                <div class="card-body pt-3">
                    <form method="POST" action="{{ route('cbs.maker_checker.reject', $request->id) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Mandatory Rejection Reason <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control form-control-sm" rows="3" placeholder="State reason for rejecting request..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bx bx-x me-1"></i> Reject Request
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="card bg-light">
                <div class="card-body text-center py-4">
                    <i class="bx bx-lock-alt fs-1 text-muted mb-2"></i>
                    <h6 class="fw-bold">Request Concluded</h6>
                    <p class="small text-muted mb-0">This request has reached finality and cannot be modified.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
