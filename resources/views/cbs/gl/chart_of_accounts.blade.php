@extends('layouts.cbs')

@section('title', 'Chart of Accounts (COA)')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-sitemap text-primary me-2"></i>Double-Entry Chart of Accounts (COA)</h4>
        <span class="text-muted">Standard General Ledger Classification: Assets, Liabilities, Equity, Revenue & Expenses</span>
    </div>
    <a href="{{ route('cbs.gl.trial_balance') }}" class="btn btn-outline-primary">
        <i class="bx bx-scale me-1"></i> View Real-time Trial Balance
    </a>
</div>

<!-- Account Classes -->
@foreach($accounts as $type => $glList)
    @php
        $badgeColor = match($type) {
            'asset' => 'primary',
            'liability' => 'info',
            'equity' => 'success',
            'revenue' => 'warning',
            'expense' => 'danger',
            default => 'secondary'
        };
    @endphp
    <div class="card mb-4">
        <div class="card-header bg-label-{{ $badgeColor }} d-flex justify-content-between align-items-center py-3">
            <h5 class="card-title mb-0 fw-bold text-uppercase text-{{ $badgeColor }}">
                <i class="bx bx-folder me-2"></i>{{ strtoupper($type) }} ACCOUNTS
            </h5>
            <span class="badge bg-{{ $badgeColor }}">{{ $glList->count() }} Ledger Codes</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 15%;">GL Code</th>
                            <th>Account Title</th>
                            <th>Sub Category</th>
                            <th>Normal Balance</th>
                            <th>Current Balance</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($glList as $gl)
                            <tr>
                                <td>
                                    <span class="font-monospace fw-bold text-primary fs-6">{{ $gl->account_code }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $gl->account_name }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-label-secondary">{{ ucwords(str_replace('_', ' ', $gl->sub_category ?? 'General')) }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $gl->normal_balance === 'debit' ? 'bg-label-primary' : 'bg-label-info' }}">
                                        {{ strtoupper($gl->normal_balance) }}
                                    </span>
                                </td>
                                <td class="currency-pkr fw-bold">
                                    PKR {{ number_format($gl->current_balance, 2) }}
                                </td>
                                <td>
                                    <span class="badge bg-label-success"><i class="bx bx-check me-1"></i> Active</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endforeach
@endsection
