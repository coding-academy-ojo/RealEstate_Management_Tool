@extends('layouts.app')

@section('title', 'Electricity Companies')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('electricity.overview') }}">Electricity Services</a></li>
    <li class="breadcrumb-item active">Companies</li>
@endsection

@section('content')
    <style>
        #content {
            background-color: #f8f9fa !important;
            background-image: none !important;
            position: relative;
        }

        #content::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url("{{ asset('assets/images/energie.png') }}") !important;
            background-repeat: repeat !important;
            background-size: 22px 22px !important;
            opacity: 0.18;
            pointer-events: none;
            z-index: 0;
        }

        #content>* {
            position: relative;
            z-index: 1;
        }
    </style>

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h2 class="mb-1">Electricity Companies Management</h2>
            <p class="text-muted mb-0">Curate your active and inactive electricity providers with soft delete support.</p>
        </div>
        <a href="{{ route('electricity.overview') }}" class="btn btn-outline-orange">
            <i class="bi bi-graph-up me-1"></i> Back to Overview
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>Please fix the errors below and try again.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="badge bg-light text-secondary fw-semibold mb-2">Total Companies</span>
                    <h3 class="fw-semibold mb-0">{{ number_format($stats['total'] ?? 0) }}</h3>
                    <p class="text-muted mb-0">Active plus inactive electricity providers.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="badge bg-light text-success fw-semibold mb-2">Active</span>
                    <h3 class="fw-semibold mb-0">{{ number_format($stats['active'] ?? 0) }}</h3>
                    <p class="text-muted mb-0">Currently available for new services.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="badge bg-light text-danger fw-semibold mb-2">Inactive</span>
                    <h3 class="fw-semibold mb-0">{{ number_format($stats['inactive'] ?? 0) }}</h3>
                    <p class="text-muted mb-0">Soft-deleted records awaiting restore.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('electricity.companies.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-4 col-md-6">
                        <label for="search"
                            class="form-label text-muted text-uppercase small fw-semibold mb-1">Search</label>
                        <div class="position-relative">
                            <i class="bi bi-search position-absolute"
                                style="left: 12px; top: 50%; transform: translateY(-50%); color: #999;"></i>
                            <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}"
                                class="form-control ps-5" placeholder="English or Arabic name" style="border-radius: 10px;">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <label for="status"
                            class="form-label text-muted text-uppercase small fw-semibold mb-1">Status</label>
                        <select name="status" id="status" class="form-select" style="border-radius: 10px;">
                            <option value="active" {{ ($filters['status'] ?? 'active') === 'active' ? 'selected' : '' }}>
                                Active</option>
                            <option value="inactive"
                                {{ ($filters['status'] ?? 'active') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="all" {{ ($filters['status'] ?? 'active') === 'all' ? 'selected' : '' }}>All
                            </option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <button type="submit" class="btn btn-orange w-100" style="border-radius: 10px;">
                            <i class="bi bi-funnel me-1"></i> Apply Filters
                        </button>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <a href="{{ route('electricity.companies.index') }}" class="btn btn-light w-100"
                            style="border-radius: 10px;">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0">
            <h5 class="mb-0"><i class="bi bi-plus-circle me-2 text-orange"></i>Add Electricity Company</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('electricity.companies.store') }}">
                @csrf
                <div class="row g-3 align-items-end">
                    <div class="col-lg-4">
                        <label for="name" class="form-label fw-semibold">Company Name <span
                                class="text-danger">*</span></label>
                        <input type="text" id="name" name="name"
                            class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                            placeholder="e.g., Jordan Electricity Co." required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-lg-4">
                        <label for="name_ar" class="form-label fw-semibold">Arabic Name</label>
                        <input type="text" id="name_ar" name="name_ar"
                            class="form-control @error('name_ar') is-invalid @enderror" value="{{ old('name_ar') }}"
                            placeholder="مثال: شركة كهرباء الأردن">
                        @error('name_ar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-lg-3">
                        <label for="website" class="form-label fw-semibold">Website</label>
                        <input type="url" id="website" name="website"
                            class="form-control @error('website') is-invalid @enderror" value="{{ old('website') }}"
                            placeholder="https://example.com">
                        @error('website')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-lg-1 d-grid">
                        <button type="submit" class="btn btn-orange mt-lg-4">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-lightning-charge me-2 text-warning"></i>Companies Directory</h5>
            <span class="text-muted small">{{ $companies->total() }} result(s)</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Company</th>
                            <th scope="col">Website</th>
                            <th scope="col" class="text-center">Services</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($companies as $company)
                            <tr class="{{ $company->trashed() ? 'table-warning' : '' }}">
                                <td>
                                    <div class="fw-semibold">{{ $company->name }}</div>
                                    @if ($company->name_ar)
                                        <div class="text-muted small">{{ $company->name_ar }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if ($company->website)
                                        <a href="{{ $company->website }}" target="_blank" rel="noopener"
                                            class="text-decoration-none">
                                            {{ \Illuminate\Support\Str::limit($company->website, 40) }}
                                        </a>
                                    @else
                                        <span class="badge bg-light text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="text-center fw-semibold">{{ number_format($company->services_count ?? 0) }}
                                </td>
                                <td class="text-center">
                                    @if ($company->trashed())
                                        <span class="badge bg-warning-subtle text-warning">Inactive</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#editCompanyModal{{ $company->id }}">
                                            <i class="bi bi-pencil-square me-1"></i>Edit
                                        </button>

                                        @if ($company->trashed())
                                            <form method="POST"
                                                action="{{ route('electricity.companies.restore', $company->id) }}"
                                                onsubmit="return confirm('Restore this company?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success">
                                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Restore
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST"
                                                action="{{ route('electricity.companies.destroy', $company) }}"
                                                onsubmit="return confirm('Deactivate this company? Related services remain intact.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash me-1"></i>Deactivate
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No companies found matching your
                                    filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0">
            {{ $companies->withQueryString()->links() }}
        </div>
    </div>

    @foreach ($companies as $company)
        <div class="modal fade" id="editCompanyModal{{ $company->id }}" tabindex="-1"
            aria-labelledby="editCompanyModalLabel{{ $company->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCompanyModalLabel{{ $company->id }}">
                            <i class="bi bi-pencil-square me-2"></i>Edit Company
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('electricity.companies.update', $company) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit-name-{{ $company->id }}" class="form-label fw-semibold">Company Name
                                    <span class="text-danger">*</span></label>
                                <input type="text" id="edit-name-{{ $company->id }}" name="name"
                                    class="form-control" value="{{ old('name', $company->name) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit-name-ar-{{ $company->id }}" class="form-label fw-semibold">Arabic
                                    Name</label>
                                <input type="text" id="edit-name-ar-{{ $company->id }}" name="name_ar"
                                    class="form-control" value="{{ old('name_ar', $company->name_ar) }}">
                            </div>
                            <div class="mb-3">
                                <label for="edit-website-{{ $company->id }}"
                                    class="form-label fw-semibold">Website</label>
                                <input type="url" id="edit-website-{{ $company->id }}" name="website"
                                    class="form-control" value="{{ old('website', $company->website) }}">
                                <div class="form-text">Use the full URL, including HTTPS.</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-orange">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection
