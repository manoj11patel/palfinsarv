@extends('layouts.admin')

@section('title', 'Edit Customer')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="mb-4">
    <h1>Edit Customer</h1>
</div>

<div class="row">
    <div class="col-lg-10">
        <form method="POST" action="{{ route('admin.customers.update', $customer) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Basic Customer Details -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Basic Customer Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('full_name') is-invalid @enderror" id="full_name" name="full_name" value="{{ old('full_name', $customer->full_name) }}" required>
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $customer->date_of_birth?->format('Y-m-d')) }}">
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="anniversary_date" class="form-label">Anniversary</label>
                            <input type="date" class="form-control @error('anniversary_date') is-invalid @enderror" id="anniversary_date" name="anniversary_date" value="{{ old('anniversary_date', $customer->anniversary_date?->format('Y-m-d')) }}">
                            @error('anniversary_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="spouse_name" class="form-label">Spouse Name</label>
                            <input type="text" class="form-control @error('spouse_name') is-invalid @enderror" id="spouse_name" name="spouse_name" value="{{ old('spouse_name', $customer->spouse_name) }}">
                            @error('spouse_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="spouse_dob" class="form-label">Spouse DOB</label>
                            <input type="date" class="form-control @error('spouse_dob') is-invalid @enderror" id="spouse_dob" name="spouse_dob" value="{{ old('spouse_dob', $customer->spouse_dob?->format('Y-m-d')) }}">
                            @error('spouse_dob')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="child1_name" class="form-label">Child 1 Name</label>
                            <input type="text" class="form-control @error('child1_name') is-invalid @enderror" id="child1_name" name="child1_name" value="{{ old('child1_name', $customer->child1_name) }}">
                            @error('child1_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="child1_dob" class="form-label">Child 1 DOB</label>
                            <input type="date" class="form-control @error('child1_dob') is-invalid @enderror" id="child1_dob" name="child1_dob" value="{{ old('child1_dob', $customer->child1_dob?->format('Y-m-d')) }}">
                            @error('child1_dob')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="child2_name" class="form-label">Child 2 Name</label>
                            <input type="text" class="form-control @error('child2_name') is-invalid @enderror" id="child2_name" name="child2_name" value="{{ old('child2_name', $customer->child2_name) }}">
                            @error('child2_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="child2_dob" class="form-label">Child 2 DOB</label>
                            <input type="date" class="form-control @error('child2_dob') is-invalid @enderror" id="child2_dob" name="child2_dob" value="{{ old('child2_dob', $customer->child2_dob?->format('Y-m-d')) }}">
                            @error('child2_dob')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="child3_name" class="form-label">Child 3 Name</label>
                            <input type="text" class="form-control @error('child3_name') is-invalid @enderror" id="child3_name" name="child3_name" value="{{ old('child3_name', $customer->child3_name) }}">
                            @error('child3_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="child3_dob" class="form-label">Child 3 DOB</label>
                            <input type="date" class="form-control @error('child3_dob') is-invalid @enderror" id="child3_dob" name="child3_dob" value="{{ old('child3_dob', $customer->child3_dob?->format('Y-m-d')) }}">
                            @error('child3_dob')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email ID <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $customer->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- KYC Details -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">KYC Details</h5>
                </div>
                <div class="card-body">

                    {{-- PAN --}}
                    <p class="fw-semibold text-secondary mb-2 small text-uppercase">PAN Card</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="pan_number" class="form-label">PAN Number</label>
                            <input type="text" class="form-control @error('pan_number') is-invalid @enderror" id="pan_number" name="pan_number" value="{{ old('pan_number', $customer->pan_number) }}" placeholder="ABCDE1234F">
                            @error('pan_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="pan_file" class="form-label">PAN Card Upload</label>
                            @if($customer->pan_file_path)
                                <div class="mb-1">
                                    <a href="{{ asset('storage/' . $customer->pan_file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-file-earmark"></i> View Current
                                    </a>
                                </div>
                            @endif
                            <input type="file" class="form-control @error('pan_file') is-invalid @enderror" id="pan_file" name="pan_file" accept=".pdf,.jpg,.jpeg,.png">
                            @error('pan_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-3">

                    {{-- Aadhaar --}}
                    <p class="fw-semibold text-secondary mb-2 small text-uppercase">Aadhaar Card</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="aadhar_number" class="form-label">Aadhaar Number</label>
                            <input type="text" class="form-control @error('aadhar_number') is-invalid @enderror" id="aadhar_number" name="aadhar_number" value="{{ old('aadhar_number', $customer->aadhar_number) }}" placeholder="1234-5678-9012">
                            @error('aadhar_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="aadhar_front_file" class="form-label">Aadhaar Card – Front</label>
                            @if($customer->aadhar_front_file_path)
                                <div class="mb-1">
                                    <a href="{{ asset('storage/' . $customer->aadhar_front_file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-file-earmark"></i> View Current
                                    </a>
                                </div>
                            @endif
                            <input type="file" class="form-control @error('aadhar_front_file') is-invalid @enderror" id="aadhar_front_file" name="aadhar_front_file" accept=".pdf,.jpg,.jpeg,.png">
                            @error('aadhar_front_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="aadhar_back_file" class="form-label">Aadhaar Card – Back</label>
                            @if($customer->aadhar_back_file_path)
                                <div class="mb-1">
                                    <a href="{{ asset('storage/' . $customer->aadhar_back_file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-file-earmark"></i> View Current
                                    </a>
                                </div>
                            @endif
                            <input type="file" class="form-control @error('aadhar_back_file') is-invalid @enderror" id="aadhar_back_file" name="aadhar_back_file" accept=".pdf,.jpg,.jpeg,.png">
                            @error('aadhar_back_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-3">

                    {{-- Passport --}}
                    <p class="fw-semibold text-secondary mb-2 small text-uppercase">Passport</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="passport_file" class="form-label">Passport Upload</label>
                            @if($customer->passport_file_path)
                                <div class="mb-1">
                                    <a href="{{ asset('storage/' . $customer->passport_file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-file-earmark"></i> View Current
                                    </a>
                                </div>
                            @endif
                            <input type="file" class="form-control @error('passport_file') is-invalid @enderror" id="passport_file" name="passport_file" accept=".pdf,.jpg,.jpeg,.png">
                            @error('passport_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                       
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Address</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $customer->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @php
                            $selCountry = old('country_id') ? \App\Models\Country::find(old('country_id')) : $customer->country;
                            $selState   = old('state_id')   ? \App\Models\State::find(old('state_id'))     : $customer->state;
                            $selCity    = old('city_id')    ? \App\Models\City::find(old('city_id'))       : $customer->city;
                        @endphp

                        <div class="col-md-4 mb-3">
                            <label for="country_id" class="form-label">Country</label>
                            <select id="country_id" name="country_id" class="form-select @error('country_id') is-invalid @enderror" style="width:100%">
                                @if($selCountry)
                                    <option value="{{ $selCountry->id }}" selected>{{ $selCountry->name }}</option>
                                @endif
                            </select>
                            @error('country_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="state_id" class="form-label">State / Province</label>
                            <select id="state_id" name="state_id" class="form-select @error('state_id') is-invalid @enderror" style="width:100%">
                                @if($selState)
                                    <option value="{{ $selState->id }}" selected>{{ $selState->name }}</option>
                                @endif
                            </select>
                            @error('state_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="city_id" class="form-label">City</label>
                            <select id="city_id" name="city_id" class="form-select @error('city_id') is-invalid @enderror" style="width:100%">
                                @if($selCity)
                                    <option value="{{ $selCity->id }}" selected>{{ $selCity->name }}</option>
                                @endif
                            </select>
                            @error('city_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="pincode" class="form-label">Pincode</label>
                            <input type="text" class="form-control @error('pincode') is-invalid @enderror" id="pincode" name="pincode" value="{{ old('pincode', $customer->pincode) }}">
                            @error('pincode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bank Details -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Bank Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="account_holder_name" class="form-label">Account Holder Name</label>
                            <input type="text" class="form-control @error('account_holder_name') is-invalid @enderror" id="account_holder_name" name="account_holder_name" value="{{ old('account_holder_name', $customer->account_holder_name) }}">
                            @error('account_holder_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="bank_name" class="form-label">Bank Name</label>
                            <input type="text" class="form-control @error('bank_name') is-invalid @enderror" id="bank_name" name="bank_name" value="{{ old('bank_name', $customer->bank_name) }}">
                            @error('bank_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="account_number" class="form-label">Account Number</label>
                            <input type="text" class="form-control @error('account_number') is-invalid @enderror" id="account_number" name="account_number" value="{{ old('account_number', $customer->account_number) }}">
                            @error('account_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="ifsc_code" class="form-label">IFSC Code</label>
                            <input type="text" class="form-control @error('ifsc_code') is-invalid @enderror" id="ifsc_code" name="ifsc_code" value="{{ old('ifsc_code', $customer->ifsc_code) }}">
                            @error('ifsc_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>


            <!-- Income Details -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Income Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Income Type</label>
                            <div class="d-flex gap-4 mt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="income_type" id="income_type_job" value="job"
                                        {{ old('income_type', $customer->income_type) === 'job' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="income_type_job">
                                        <i class="bi bi-briefcase me-1"></i> Job / Salaried
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="income_type" id="income_type_business" value="business"
                                        {{ old('income_type', $customer->income_type) === 'business' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="income_type_business">
                                        <i class="bi bi-shop me-1"></i> Business / Self-employed
                                    </label>
                                </div>
                            </div>
                            @error('income_type')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <label for="income_details" class="form-label">Income Details</label>
                            <textarea class="form-control @error('income_details') is-invalid @enderror"
                                id="income_details" name="income_details" rows="3"
                                placeholder="e.g., Company name, designation, annual income, business type…">{{ old('income_details', $customer->income_details) }}</textarea>
                            @error('income_details')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Document Upload -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Upload Documents</h5>
                </div>
                <div class="card-body">
                    <div id="documentsContainer">
                        <div class="row align-items-end document-item mb-3">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Document Type *</label>
                                <input type="text" name="documents[0][document_type]" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">File *</label>
                                <input type="file" name="documents[0][file]" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addDocument">Add Another Document</button>
                </div>
            </div>

            <script>
            let documentIndex = 1;
            document.getElementById('addDocument').addEventListener('click', function() {
                const container = document.getElementById('documentsContainer');
                const newItem = document.createElement('div');
                newItem.className = 'row align-items-end document-item mb-3';
                newItem.innerHTML = `
                    <div class="col-md-5 mb-2">
                        <label class="form-label">Document Type *</label>
                        <input type="text" name="documents[${documentIndex}][document_type]" class="form-control" required>
                    </div>
                    <div class="col-md-5 mb-2">
                        <label class="form-label">File *</label>
                        <input type="file" name="documents[${documentIndex}][file]" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                    <div class="col-md-2 mb-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm w-100 remove-document">
                            <i class="bi bi-trash"></i> Remove
                        </button>
                    </div>
                `;
                container.appendChild(newItem);
                documentIndex++;
            });

            document.getElementById('documentsContainer').addEventListener('click', function(e) {
                const btn = e.target.closest('.remove-document');
                if (btn) {
                    btn.closest('.document-item').remove();
                }
            });
            </script>

            <!-- Nominee Details -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Nominee Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="nominee_name" class="form-label">Nominee Name</label>
                            <input type="text" class="form-control @error('nominee_name') is-invalid @enderror" id="nominee_name" name="nominee_name" value="{{ old('nominee_name', $customer->nominee_name) }}">
                            @error('nominee_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="nominee_relationship" class="form-label">Relationship</label>
                            <input type="text" class="form-control @error('nominee_relationship') is-invalid @enderror" id="nominee_relationship" name="nominee_relationship" value="{{ old('nominee_relationship', $customer->nominee_relationship) }}" placeholder="e.g., Spouse, Parent, Child">
                            @error('nominee_relationship')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="nominee_dob" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control @error('nominee_dob') is-invalid @enderror" id="nominee_dob" name="nominee_dob"
                                value="{{ old('nominee_dob', $customer->nominee_dob?->format('Y-m-d')) }}"
                                max="{{ date('Y-m-d') }}">
                            @error('nominee_dob')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="nominee_age_display" class="form-text text-primary fw-semibold"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nominee Document</label>
                            @if($customer->nominee_document_path)
                                {{-- Document already uploaded: show checkbox + view + replace toggle --}}
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox" id="nominee_doc_uploaded" checked disabled>
                                        <label class="form-check-label text-success fw-semibold" for="nominee_doc_uploaded">
                                            Document uploaded
                                        </label>
                                    </div>
                                    <a href="{{ asset('storage/' . $customer->nominee_document_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-file-earmark"></i> View
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-warning" id="replaceNomineeDoc">
                                        <i class="bi bi-arrow-repeat"></i> Replace
                                    </button>
                                </div>
                                <div id="nomineeDocUpload" class="d-none">
                                    <input type="file" class="form-control @error('nominee_document') is-invalid @enderror" name="nominee_document" accept=".pdf,.jpg,.jpeg,.png">
                                    <div class="form-text">Uploading a new file will replace the current document.</div>
                                    @error('nominee_document')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            @else
                                {{-- No document yet: show upload input --}}
                                <input type="file" class="form-control @error('nominee_document') is-invalid @enderror" name="nominee_document" accept=".pdf,.jpg,.jpeg,.png">
                                <div class="form-text">Upload any ID proof for the nominee (PDF/JPG/PNG, max 5 MB)</div>
                                @error('nominee_document')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <script>
            (function () {
                const btn = document.getElementById('replaceNomineeDoc');
                if (btn) {
                    btn.addEventListener('click', function () {
                        document.getElementById('nomineeDocUpload').classList.remove('d-none');
                        btn.classList.add('d-none');
                    });
                }
            })();
            </script>

            <script>
            (function () {
                const dobInput = document.getElementById('nominee_dob');
                const ageDisplay = document.getElementById('nominee_age_display');

                function calcAge(dob) {
                    if (!dob) { ageDisplay.textContent = ''; return; }
                    const today = new Date();
                    const birth = new Date(dob);
                    let age = today.getFullYear() - birth.getFullYear();
                    const m = today.getMonth() - birth.getMonth();
                    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
                    ageDisplay.textContent = age >= 0 ? 'Age: ' + age + ' year' + (age !== 1 ? 's' : '') : '';
                }

                dobInput.addEventListener('change', () => calcAge(dobInput.value));
                calcAge(dobInput.value);
            })();
            </script>

            <!-- Products Selection -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Products Interested In / Taken</h5>
                </div>
                <div class="card-body">
                    <select class="btn-group d-flex @error('products') is-invalid @enderror" id="products" name="products[]" multiple>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" @if(in_array($product->id, old('products', $selectedProducts))) selected @endif>
                                {{ $product->name }} ({{ $product->category->name ?? 'Uncategorized' }})
                            </option>
                        @endforeach
                    </select>
                    @error('products')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Agent Assignment -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Agent Assignment</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="agent_user_id" class="form-label">Assign Agent</label>
                        <select class="form-select @error('agent_user_id') is-invalid @enderror" id="agent_user_id" name="agent_user_id">
                            <option value="">--No Agent (Assign to Pal Finsarv Admin)--</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}" @if(old('agent_user_id', $customer->agent_user_id) == $agent->id) selected @endif>
                                    {{ $agent->name }}
                                    @if($agent->agentProfile)
                                        ({{ $agent->agentProfile->employee_code }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('agent_user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">If no agent is selected, the customer will be assigned to Pal Finsarv Admin.</div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mb-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Update Customer
                </button>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function () {
    const GEO = {
        countries: "{{ route('admin.geo.countries') }}",
        states:    "{{ route('admin.geo.states') }}",
        cities:    "{{ route('admin.geo.cities') }}",
    };

    function makeAjax(url, extraData) {
        return {
            url,
            dataType: 'json',
            delay: 300,
            data: params => ({ q: params.term || '', page: params.page || 1, ...extraData() }),
            processResults: (data) => ({
                results: data.results,
                pagination: data.pagination ?? { more: false },
            }),
            cache: true,
        };
    }

    $('#country_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Search country…',
        allowClear: true,
        ajax: makeAjax(GEO.countries, () => ({})),
    });

    $('#state_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Select country first',
        allowClear: true,
        ajax: makeAjax(GEO.states, () => ({ country_id: $('#country_id').val() || 0 })),
    });

    $('#city_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Select state first',
        allowClear: true,
        ajax: makeAjax(GEO.cities, () => ({ state_id: $('#state_id').val() || 0 })),
    });

    // Cascade: country change → clear state and city
    $('#country_id').on('change', function () {
        $('#state_id').val(null).trigger('change');
        $('#city_id').val(null).trigger('change');
    });

    // Cascade: state change → clear city
    $('#state_id').on('change', function () {
        $('#city_id').val(null).trigger('change');
    });
});
</script>
@endpush
@endsection
