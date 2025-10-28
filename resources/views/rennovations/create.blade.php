@extends('layouts.app')

@section('title', 'Create Rennovation')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('rennovations.index') }}">Rennovations</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0">
                        <i class="bi bi-lightbulb-fill me-2 text-orange"></i>
                        Create New Rennovation
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('rennovations.store') }}" method="POST">
                        @csrf

                        <h5 class="mb-3 text-orange">
                            <i class="bi bi-link-45deg me-2"></i>Related Entity
                        </h5>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="innovatable_type" class="form-label fw-bold">
                                    Entity Type <span class="text-danger">*</span>
                                </label>
                                <select name="innovatable_type" id="innovatable_type"
                                    class="form-select @error('innovatable_type') is-invalid @enderror" required
                                    {{ request('innovatable_type') ? 'disabled' : '' }}>
                                    <option value="">-- Select Entity Type --</option>
                                    <option value="Site"
                                        {{ old('innovatable_type', request('innovatable_type')) == 'Site' ? 'selected' : '' }}>
                                        Site</option>
                                    <option value="Building"
                                        {{ old('innovatable_type', request('innovatable_type')) == 'Building' ? 'selected' : '' }}>
                                        Building</option>
                                    <option value="Land"
                                        {{ old('innovatable_type', request('innovatable_type')) == 'Land' ? 'selected' : '' }}>
                                        Land</option>
                                </select>
                                @if (request('innovatable_type'))
                                    <input type="hidden" name="innovatable_type" value="{{ request('innovatable_type') }}">
                                @endif
                                @error('innovatable_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="innovatable_id" class="form-label fw-bold">
                                    Select Entity <span class="text-danger">*</span>
                                </label>
                                <select name="innovatable_id" id="innovatable_id"
                                    class="form-select @error('innovatable_id') is-invalid @enderror" required
                                    {{ request('innovatable_id') ? 'disabled' : '' }}>
                                    <option value="">-- Select entity type first --</option>
                                </select>
                                @if (request('innovatable_id'))
                                    <input type="hidden" name="innovatable_id" value="{{ request('innovatable_id') }}">
                                    <small class="text-info">
                                        <i class="bi bi-lock-fill me-1"></i>Entity is locked (coming from entity page)
                                    </small>
                                @endif
                                @error('innovatable_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3 text-orange">
                            <i class="bi bi-info-circle me-2"></i>Rennovation Details
                        </h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-bold">
                                    Rennovation Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="cost" class="form-label fw-bold">
                                    Cost (JOD) <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.01" name="cost" id="cost"
                                    class="form-control @error('cost') is-invalid @enderror" value="{{ old('cost', 0) }}"
                                    required>
                                @error('cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="date" class="form-label fw-bold">
                                    Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="date" id="date"
                                    class="form-control @error('date') is-invalid @enderror"
                                    value="{{ old('date', date('Y-m-d')) }}" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea name="description" id="description" rows="4"
                                class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Describe the renovation work performed</small>
                        </div>

                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <a href="{{ route('rennovations.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-orange">
                                <i class="bi bi-check-circle me-1"></i> Create Rennovation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const typeSelect = document.getElementById('innovatable_type');
                const entitySelect = document.getElementById('innovatable_id');

                typeSelect.addEventListener('change', function() {
                    const type = this.value;
                    entitySelect.innerHTML = '<option value="">Loading...</option>';

                    if (!type) {
                        entitySelect.innerHTML = '<option value="">-- Select entity type first --</option>';
                        return;
                    }

                    const endpoints = {
                        'Site': '/api/sites-list',
                        'Building': '/api/buildings-list',
                        'Land': '/api/lands-list'
                    };

                    fetch(endpoints[type])
                        .then(response => response.json())
                        .then(data => {
                            let html = '<option value="">-- Select ' + type + ' --</option>';
                            data.forEach(item => {
                                const displayLabel = item.display ?? item.name ?? item.code ??
                                    `${type} #${item.id}`;
                                html += `<option value="${item.id}">${displayLabel}</option>`;
                            });
                            entitySelect.innerHTML = html;

                            const oldValue = '{{ old('innovatable_id', request('innovatable_id')) }}';
                            if (oldValue) {
                                entitySelect.value = oldValue;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            entitySelect.innerHTML = '<option value="">Error loading data</option>';
                        });
                });

                if (typeSelect.value) {
                    typeSelect.dispatchEvent(new Event('change'));
                }
            });
        </script>
    @endpush
@endsection
