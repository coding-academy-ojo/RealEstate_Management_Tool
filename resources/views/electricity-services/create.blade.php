@extends('layouts.app')

@section('title', 'Create Electricity Service')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('electricity-services.index') }}">Electricity Services</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0">
                        <i class="bi bi-lightning-charge-fill me-2 text-orange"></i>
                        Create New Electricity Service Record
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('electricity-services.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Building Selection -->
                        <div class="mb-4">
                            <label for="building_id" class="form-label fw-bold">
                                Building <span class="text-danger">*</span>
                            </label>
                            <select name="building_id" id="building_id"
                                class="form-select @error('building_id') is-invalid @enderror" required>
                                <option value="">-- Select Building --</option>
                                @foreach ($buildings as $building)
                                    <option value="{{ $building->id }}"
                                        {{ old('building_id', request('building_id')) == $building->id ? 'selected' : '' }}>
                                        {{ $building->code }} - {{ $building->name }} (Site: {{ $building->site->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('building_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3 text-orange">
                            <i class="bi bi-info-circle me-2"></i>Company Information
                        </h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="company_name" class="form-label fw-bold">
                                    Company Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="company_name" id="company_name"
                                    class="form-control @error('company_name') is-invalid @enderror"
                                    value="{{ old('company_name') }}" required>
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="registration_number" class="form-label fw-bold">
                                    Registration Number <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="registration_number" id="registration_number"
                                    class="form-control @error('registration_number') is-invalid @enderror"
                                    value="{{ old('registration_number') }}" required>
                                @error('registration_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3 text-orange">
                            <i class="bi bi-speedometer2 me-2"></i>Meter Readings
                        </h5>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="previous_reading" class="form-label fw-bold">
                                    Previous Reading (kWh) <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.01" name="previous_reading" id="previous_reading"
                                    class="form-control @error('previous_reading') is-invalid @enderror"
                                    value="{{ old('previous_reading', 0) }}" required>
                                @error('previous_reading')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="current_reading" class="form-label fw-bold">
                                    Current Reading (kWh) <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.01" name="current_reading" id="current_reading"
                                    class="form-control @error('current_reading') is-invalid @enderror"
                                    value="{{ old('current_reading', 0) }}" required>
                                @error('current_reading')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="reading_date" class="form-label fw-bold">
                                    Reading Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="reading_date" id="reading_date"
                                    class="form-control @error('reading_date') is-invalid @enderror"
                                    value="{{ old('reading_date', date('Y-m-d')) }}" required>
                                @error('reading_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-calculator me-2"></i>
                            <strong>Consumption Calculation:</strong> Current Reading - Previous Reading = Electricity
                            Consumption
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3 text-orange">
                            <i class="bi bi-file-earmark-arrow-up me-2"></i>Documents & Remarks
                        </h5>

                        <div class="mb-3">
                            <label for="reset_file" class="form-label fw-bold">
                                Reset File (Optional)
                            </label>
                            <input type="file" name="reset_file" id="reset_file"
                                class="form-control @error('reset_file') is-invalid @enderror"
                                accept=".pdf,.jpg,.jpeg,.png">
                            @error('reset_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Accepted: PDF, JPG, PNG</small>
                        </div>

                        <div class="mb-4">
                            <label for="remarks" class="form-label fw-bold">Remarks</label>
                            <textarea name="remarks" id="remarks" rows="3" class="form-control @error('remarks') is-invalid @enderror">{{ old('remarks') }}</textarea>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <a href="{{ route('electricity-services.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-orange">
                                <i class="bi bi-check-circle me-1"></i> Create Electricity Service
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
