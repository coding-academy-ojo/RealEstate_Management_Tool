@extends('layouts.app')

@section('title', 'Re-Innovation Details')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('re-innovations.index') }}">Re-Innovations</a></li>
    <li class="breadcrumb-item active">{{ $reInnovation->name }}</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-lightbulb-fill text-warning me-2"></i>
            {{ $reInnovation->name }}
        </h2>
        <div class="btn-group">
            <a href="{{ route('re-innovations.edit', $reInnovation) }}" class="btn btn-outline-orange">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <button type="button" class="btn btn-outline-danger" onclick="openDeleteModal()">
                <i class="bi bi-trash me-1"></i> Delete
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Main Information -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-link-45deg me-2 text-orange"></i>Related Entity
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Entity Type:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge bg-info">{{ class_basename($reInnovation->innovatable_type) }}</span>
                        </div>
                    </div>

                    @if ($reInnovation->innovatable)
                        <div class="row">
                            <div class="col-md-4">
                                <strong class="text-muted">Entity:</strong>
                            </div>
                            <div class="col-md-8">
                                @php
                                    $type = class_basename($reInnovation->innovatable_type);
                                    $route = strtolower($type) . 's.show';
                                @endphp
                                <a href="{{ route($route, $reInnovation->innovatable) }}" class="text-decoration-none">
                                    <span class="badge bg-orange">
                                        {{ $reInnovation->innovatable->code ?? ($reInnovation->innovatable->name ?? 'N/A') }}
                                    </span>
                                    {{ $reInnovation->innovatable->name ?? ($reInnovation->innovatable->plot_number ?? '') }}
                                </a>
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0">Related entity has been deleted</p>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2 text-orange"></i>Innovation Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Name:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $reInnovation->name }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Cost:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge bg-success fs-6">{{ number_format($reInnovation->cost, 2) }} JOD</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Date:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $reInnovation->date ? $reInnovation->date->format('F d, Y') : 'N/A' }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <strong class="text-muted">Description:</strong>
                        </div>
                        <div class="col-md-8">
                            <p class="mb-0">{{ $reInnovation->description ?: 'No description provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Side Information -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm mb-3"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
                    <h6 class="text-white mb-3">
                        <i class="bi bi-cash-stack me-2"></i>Total Cost
                    </h6>
                    <div class="display-5 fw-bold mb-2">
                        {{ number_format($reInnovation->cost, 2) }}
                    </div>
                    <div class="text-white-50">Jordanian Dinar (JOD)</div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-calendar-event me-2 text-orange"></i>Date Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Innovation Date:</span>
                        <span
                            class="small fw-bold">{{ $reInnovation->date ? $reInnovation->date->format('Y-m-d') : 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Created:</span>
                        <span class="small">{{ $reInnovation->created_at->format('Y-m-d') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Updated:</span>
                        <span class="small">{{ $reInnovation->updated_at->format('Y-m-d') }}</span>
                    </div>
                </div>
            </div>

            @if ($reInnovation->innovatable && $reInnovation->innovatable_type === 'App\Models\Site')
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0">
                            <i class="bi bi-geo-alt me-2 text-orange"></i>Site Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Site:</strong> {{ $reInnovation->innovatable->name }}</p>
                        <p class="mb-2"><strong>Governorate:</strong>
                            {{ $reInnovation->innovatable->governorate_name_en }}</p>
                        <p class="mb-0"><strong>Area:</strong>
                            {{ number_format($reInnovation->innovatable->area_m2, 2) }} m²</p>
                    </div>
                </div>
            @endif

            @if ($reInnovation->innovatable && $reInnovation->innovatable_type === 'App\Models\Building')
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0">
                            <i class="bi bi-building me-2 text-orange"></i>Building Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Building:</strong> {{ $reInnovation->innovatable->name }}</p>
                        <p class="mb-2"><strong>Site:</strong> {{ $reInnovation->innovatable->site->name }}</p>
                        <p class="mb-0"><strong>Area:</strong>
                            {{ number_format($reInnovation->innovatable->area_m2, 2) }} m²</p>
                    </div>
                </div>
            @endif

            @if ($reInnovation->innovatable && $reInnovation->innovatable_type === 'App\Models\Land')
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0">
                            <i class="bi bi-map me-2 text-orange"></i>Land Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Plot:</strong> {{ $reInnovation->innovatable->plot_number }}</p>
                        <p class="mb-2"><strong>Basin:</strong> {{ $reInnovation->innovatable->basin }}</p>
                        <p class="mb-0"><strong>Site:</strong> {{ $reInnovation->innovatable->site->name }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">Are you sure you want to delete this re-innovation?</p>
                    <div class="alert alert-warning mb-0">
                        <strong>{{ $reInnovation->name }}</strong>
                    </div>
                    <p class="text-muted mt-2 mb-0">
                        <small>This action can be undone from the trash.</small>
                    </p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" action="{{ route('re-innovations.destroy', $reInnovation) }}" method="POST"
                        class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openDeleteModal() {
            const modalElement = document.getElementById('deleteModal');
            const modal = new boosted.Modal(modalElement);
            modal.show();
        }
    </script>
@endsection
