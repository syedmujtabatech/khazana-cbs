@extends('layouts.cbs')

@section('title', 'Onboard New Customer (CIF)')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="bx bx-user-plus text-primary me-2"></i>Customer Information File (CIF) Onboarding</h4>
                <span class="text-muted">Register customer in SBP-compliant Customer Master with Biometric & AML verification</span>
            </div>
            <a href="{{ route('cbs.customers.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back to Directory
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Customer Bio-data & SBP Compliance Information</h5>
            </div>
            <div class="card-body pt-4">
                <form method="POST" action="{{ route('cbs.customers.store') }}">
                    @csrf

                    <!-- Branch Assignment -->
                    <div class="row g-3 mb-4 p-3 bg-light rounded">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Assigned Commercial Branch <span class="text-danger">*</span></label>
                            <select name="branch_id" class="form-select @error('branch_id') is-invalid @enderror" required>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>
                                        {{ $b->name }} (Code: {{ $b->code }}) - {{ $b->city }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">AML Risk Category <span class="text-danger">*</span></label>
                            <select name="risk_rating" class="form-select @error('risk_rating') is-invalid @enderror" required>
                                <option value="low" {{ old('risk_rating') == 'low' ? 'selected' : '' }}>Low Risk (Standard Resident Individual)</option>
                                <option value="medium" {{ old('risk_rating') == 'medium' ? 'selected' : '' }}>Medium Risk (High Volume Trader / Real Estate)</option>
                                <option value="high" {{ old('risk_rating') == 'high' ? 'selected' : '' }}>High Risk (Enhanced Due Diligence Mandatory)</option>
                                <option value="pep" {{ old('risk_rating') == 'pep' ? 'selected' : '' }}>Politically Exposed Person (PEP - Government/Judiciary)</option>
                            </select>
                            @error('risk_rating') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <h6 class="fw-bold text-primary mb-3"><i class="bx bx-user me-1"></i> Personal Bio-data</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-2">
                            <label class="form-label">Salutation</label>
                            <select name="salutation" class="form-select">
                                <option value="Mr.">Mr.</option>
                                <option value="Ms.">Ms.</option>
                                <option value="Mrs.">Mrs.</option>
                                <option value="Dr.">Dr.</option>
                                <option value="Senator">Senator</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
                            @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
                            @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Pakistani CNIC / Passport <span class="text-danger">*</span></label>
                            <input type="text" name="cnic_passport" class="form-control @error('cnic_passport') is-invalid @enderror" placeholder="42101-1234567-1" value="{{ old('cnic_passport') }}" required>
                            @error('cnic_passport') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">CNIC Expiry Date</label>
                            <input type="date" name="cnic_expiry_date" class="form-control" value="{{ old('cnic_expiry_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Occupation</label>
                            <input type="text" name="occupation" class="form-control" placeholder="e.g. Software Engineer, Trader" value="{{ old('occupation') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Estimated Monthly Income (PKR)</label>
                            <input type="number" step="0.01" name="monthly_income" class="form-control" placeholder="350000" value="{{ old('monthly_income') }}">
                        </div>
                    </div>

                    <!-- Contact & Digital Portal Credentials -->
                    <h6 class="fw-bold text-primary mb-3"><i class="bx bx-envelope me-1"></i> Contact & Internet Banking Credentials</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Email Address (Internet Banking Login) <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="customer@example.com" value="{{ old('email') }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mobile Contact Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="+92-300-1234567" value="{{ old('phone') }}" required>
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Residential Address <span class="text-danger">*</span></label>
                            <input type="text" name="residential_address" class="form-control @error('residential_address') is-invalid @enderror" placeholder="House/Flat number, Street, Area" value="{{ old('residential_address') }}" required>
                            @error('residential_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">City <span class="text-danger">*</span></label>
                            <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" placeholder="Karachi / Lahore / Islamabad" value="{{ old('city', 'Karachi') }}" required>
                            @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <!-- Politically Exposed Person (PEP) Checkbox -->
                    <div class="form-check form-switch mb-4 p-3 bg-label-secondary rounded">
                        <input class="form-check-input ms-0 me-3" type="checkbox" id="is_pep" name="is_pep" value="1" {{ old('is_pep') ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="is_pep">
                            Mark Customer as Politically Exposed Person (PEP)
                            <div class="text-muted fw-normal small">Enable if the customer or immediate family member holds prominent public functions in Pakistan (Ministers, Members of Parliament, Senior Military, Judiciary).</div>
                        </label>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('cbs.customers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bx bx-check me-1"></i> Register CIF & Issue Customer Master
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
