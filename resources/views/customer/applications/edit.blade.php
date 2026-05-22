@extends('layouts.app')

@section('title', 'Edit Application')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Application</h4>
                </div>
                <div class="card-body">
                    <form id="applicationForm" action="{{ route('customer.applications.update', $application) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Personal Details Section -->
                        <div class="mb-4">
                            <h5 class="mb-3 pb-3 border-bottom">Personal Details</h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="full_name" class="form-label">Full Name *</label>
                                        <input type="text" name="full_name" id="full_name" class="form-control @error('full_name') is-invalid @enderror" value="{{ old('full_name', $application->full_name) }}" required>
                                        @error('full_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email *</label>
                                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $application->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone *</label>
                                        <input type="tel" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $application->phone) }}" required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="date_of_birth" class="form-label">Date of Birth *</label>
                                        <input type="date" name="date_of_birth" id="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth', $application->date_of_birth) }}" required>
                                        @error('date_of_birth')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address *</label>
                                <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="2" required>{{ old('address', $application->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="identification_type" class="form-label">Identification Type *</label>
                                        <select name="identification_type" id="identification_type" class="form-select @error('identification_type') is-invalid @enderror" required>
                                            <option value="">-- Select Type --</option>
                                            <option value="passport" {{ old('identification_type', $application->identification_type) == 'passport' ? 'selected' : '' }}>Passport</option>
                                            <option value="driving_license" {{ old('identification_type', $application->identification_type) == 'driving_license' ? 'selected' : '' }}>Driving License</option>
                                            <option value="national_id" {{ old('identification_type', $application->identification_type) == 'national_id' ? 'selected' : '' }}>National ID</option>
                                            <option value="voter_id" {{ old('identification_type', $application->identification_type) == 'voter_id' ? 'selected' : '' }}>Voter ID</option>
                                        </select>
                                        @error('identification_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="identification_number" class="form-label">Identification Number *</label>
                                        <input type="text" name="identification_number" id="identification_number" class="form-control @error('identification_number') is-invalid @enderror" value="{{ old('identification_number', $application->identification_number) }}" required>
                                        @error('identification_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Product & Agent Selection -->
                        <div class="mb-4">
                            <h5 class="mb-3 pb-3 border-bottom">Product & Agent</h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="product_id" class="form-label">Product *</label>
                                        <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                                            <option value="">-- Select Product --</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" {{ old('product_id', $application->product_id) == $product->id ? 'selected' : '' }}>
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('product_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="agent_user_id" class="form-label">Agent *</label>
                                        <select name="agent_user_id" id="agent_user_id" class="form-select @error('agent_user_id') is-invalid @enderror" required>
                                            <option value="">-- Select Agent --</option>
                                            @foreach($agents as $agent)
                                                <option value="{{ $agent->id }}" {{ old('agent_user_id', $application->agent_user_id) == $agent->id ? 'selected' : '' }}>
                                                    {{ $agent->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('agent_user_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('customer.applications.show', $application) }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
