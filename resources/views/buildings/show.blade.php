@extends('layouts.app')

@section('title', 'Building Details')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('buildings.index') }}">Buildings</a></li>
    <li class="breadcrumb-item active">{{ $building->code }}</li>
@endsection

@section('content')
    @php
    $permitDocuments = $documents->where('is_permit', true);
    $asBuiltPdfDocument = $documents->firstWhere('slug', 'as-built-drawing-pdf');
    $asBuiltCadDocument = $documents->firstWhere('slug', 'as-built-drawing-cad');
        $leaseContractDocument = $documents->firstWhere('slug', 'lease-contract');
        $availableDocuments = $documents->where('has_file', true);
        $currentUser = auth()->user();
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <h2 class="mb-0 d-flex flex-wrap align-items-center gap-2">
            <span class="code-label">{{ $building->code }}</span>
            <span class="fw-semibold">{{ $building->name }}</span>
        </h2>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('buildings.index') }}" class="btn btn-light">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            @if ($currentUser?->isSuperAdmin())
                <a href="{{ route('buildings.edit', $building) }}" class="btn btn-orange">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
                <form action="{{ route('buildings.destroy', $building) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Are you sure you want to delete this building?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Main Information -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2 text-orange"></i>Building Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-4">
                            <div class="text-muted text-uppercase small fw-semibold">Code</div>
                            <div class="fw-semibold">{{ $building->code }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted text-uppercase small fw-semibold">Name</div>
                            <div>{{ $building->name }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted text-uppercase small fw-semibold">Area (m²)</div>
                            <div>{{ number_format($building->area_m2, 2) }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted text-uppercase small fw-semibold">Tenure</div>
                            <div>
                                @if ($building->tenure_type === 'rental')
                                    <span class="badge bg-warning-subtle text-warning fw-semibold">Rental</span>
                                @else
                                    <span class="badge bg-success-subtle text-success fw-semibold">Owned</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted text-uppercase small fw-semibold">Site</div>
                            <div>
                                <a href="{{ route('sites.show', $building->site) }}"
                                    class="text-decoration-none fw-semibold">
                                    {{ $building->site->code }} — {{ $building->site->name }}
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted text-uppercase small fw-semibold">Remarks</div>
                            <div>{{ $building->remarks ?: 'No remarks provided.' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="text-muted text-uppercase small fw-semibold">As-Built Documents</div>
                            @if (($asBuiltPdfDocument && $asBuiltPdfDocument['has_file']) || ($asBuiltCadDocument && $asBuiltCadDocument['has_file']))
                                <div class="d-flex flex-column gap-2">
                                    @if ($asBuiltPdfDocument && $asBuiltPdfDocument['has_file'])
                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                            <span class="fw-semibold">PDF: {{ $asBuiltPdfDocument['file_name'] }}</span>
                                            <a href="{{ $asBuiltPdfDocument['inline_url'] }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye me-1"></i>View
                                            </a>
                                            <a href="{{ $asBuiltPdfDocument['download_url'] }}"
                                                class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-download me-1"></i>Download
                                            </a>
                                        </div>
                                    @endif
                                    @if ($asBuiltCadDocument && $asBuiltCadDocument['has_file'])
                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                            <span class="fw-semibold">CAD: {{ $asBuiltCadDocument['file_name'] }}</span>
                                            <a href="{{ $asBuiltCadDocument['download_url'] }}"
                                                class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-download me-1"></i>Download
                                            </a>
                                            <span class="document-meta text-muted small">Preview unavailable</span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">Not uploaded.</span>
                            @endif
                        </div>

                        @if ($building->tenure_type === 'rental')
                            <div class="col-12">
                                <hr class="my-3">
                                <h6 class="text-orange mb-3">
                                    <i class="bi bi-file-earmark-text me-2"></i>Lease Details
                                </h6>
                                <div class="row gy-3">
                                    <div class="col-md-4">
                                        <div class="text-muted text-uppercase small fw-semibold">Lease Start</div>
                                        <div>{{ optional($building->lease_start_date)->format('Y-m-d') ?? '—' }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-muted text-uppercase small fw-semibold">Lease End</div>
                                        <div>{{ optional($building->lease_end_date)->format('Y-m-d') ?? '—' }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-muted text-uppercase small fw-semibold">Contract Value</div>
                                        <div>{{ $building->contract_value !== null ? number_format($building->contract_value, 2) . ' JOD' : '—' }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-muted text-uppercase small fw-semibold">Annual Increase</div>
                                        <div>{{ $building->annual_increase_rate !== null ? rtrim(rtrim(number_format($building->annual_increase_rate, 2), '0'), '.') . '%' : '—' }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-muted text-uppercase small fw-semibold">Increase Effective</div>
                                        <div>{{ optional($building->increase_effective_date)->format('Y-m-d') ?? '—' }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-muted text-uppercase small fw-semibold">Lease Contract</div>
                                        @if ($leaseContractDocument && $leaseContractDocument['has_file'])
                                            <div class="d-flex flex-wrap align-items-center gap-2">
                                                <a href="{{ $leaseContractDocument['inline_url'] }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye me-1"></i>View
                                                </a>
                                                <a href="{{ $leaseContractDocument['download_url'] }}"
                                                    class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-download me-1"></i>Download
                                                </a>
                                                <span class="document-meta">{{ $leaseContractDocument['file_name'] }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted">No contract uploaded</span>
                                        @endif
                                    </div>
                                    <div class="col-12">
                                        <div class="text-muted text-uppercase small fw-semibold">Special Conditions</div>
                                        <div>{{ $building->special_conditions ?: 'No special conditions documented.' }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <hr class="my-4">

                    <h6 class="text-orange mb-3">
                        <i class="bi bi-file-earmark-check me-2"></i>Permit Overview
                    </h6>

                    <div class="table-responsive">
                        <table class="table align-middle permit-table mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Permit</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">File</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($permitDocuments as $permit)
                                    <tr>
                                        <td class="fw-semibold">{{ $permit['label'] }}</td>
                                        <td>
                                            @if ($permit['status_boolean'] === true)
                                                <span class="text-success fw-semibold">Yes</span>
                                            @elseif ($permit['status_boolean'] === false)
                                                <span class="text-muted">No</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($permit['has_file'])
                                                <div class="d-flex flex-wrap align-items-center gap-2">
                                                    <a href="{{ $permit['inline_url'] }}" target="_blank"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye me-1"></i>View
                                                    </a>
                                                    <a href="{{ $permit['download_url'] }}"
                                                        class="btn btn-sm btn-outline-secondary">
                                                        <i class="bi bi-download me-1"></i>Download
                                                    </a>
                                                    <span class="document-meta">{{ $permit['file_name'] }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted">No file uploaded</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lands & Services -->
        <div class="col-lg-4 mb-4">
            <!-- Associated Lands -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-map me-2 text-orange"></i>Associated Lands
                    </h6>
                </div>
                <div class="card-body">
                    @forelse($building->lands as $land)
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <a href="{{ route('lands.show', $land) }}" class="text-decoration-none fw-bold">
                                        <i class="bi bi-map text-info me-1"></i>
                                        Plot {{ $land->plot_number }}
                                    </a>
                                </div>
                                <span class="text-muted fw-semibold">Basin {{ $land->basin }}</span>
                            </div>
                            <div class="small text-muted">
                                <div><i class="bi bi-geo-alt me-1"></i>{{ $land->village ?: 'N/A' }}</div>
                                @if ($land->governorate)
                                    <div><i class="bi bi-building me-1"></i>{{ $land->governorate }}</div>
                                @endif
                                @if ($land->zoning)
                                    <div><i class="bi bi-signpost me-1"></i>Zoning: {{ $land->zoning }}</div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0 small text-center">
                            <i class="bi bi-exclamation-circle me-1"></i>No lands associated
                        </p>
                    @endforelse
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-graph-up me-2 text-orange"></i>Services & Innovations
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Water Services:</span>
                        <span class="stat-chip">
                            <i class="bi bi-droplet text-info"></i>{{ $building->waterServices->count() }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Electricity Services:</span>
                        <span class="stat-chip">
                            <i class="bi bi-lightning text-warning"></i>{{ $building->electricityServices->count() }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Renovations:</span>
                        <span class="stat-chip">
                            <i class="bi bi-lightbulb text-success"></i>{{ $building->renovations->count() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Building Images Gallery -->
    @php
        $allImages = [];

        // Add building's own images only
foreach ($building->images as $image) {
    $allImages[] = [
        'image' => $image,
        'source' => null,
        'source_label' => null,
        'editable' => true,
            ];
        }
    @endphp

    <x-image-gallery :images="$allImages" type="building" :entityId="$building->id" :canEdit="true" :showSourceTags="false"
        title="Building Images" />

    <div class="service-section service-section--water">
        <div class="service-section__header">
            <h6 class="mb-0"><i class="bi bi-droplet me-2 text-info"></i>Water Services</h6>
            <span class="service-section__count">{{ $building->waterServices->count() }} record(s)</span>
        </div>
        @if ($building->waterServices->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-sm align-middle service-section__table mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Meter Info</th>
                            <th scope="col">Latest Reading</th>
                            <th scope="col">Latest Bill</th>
                            <th scope="col">Meter Image</th>
                            <th scope="col">Remarks</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($building->waterServices as $service)
                            @php
                                $latestReading = $service->latestReading;
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $service->meter_owner_name }}</div>
                                    <div class="text-muted small">{{ $service->company_name }}</div>
                                    <div class="d-flex flex-wrap gap-2 mt-1 small">
                                        <span class="badge bg-light text-muted border">Reg: {{ $service->registration_number ?? '—' }}</span>
                                        <span class="badge bg-light text-muted border">Iron: {{ $service->iron_number ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if ($latestReading)
                                        <div class="fw-semibold">
                                            {{ number_format((float) $latestReading->current_reading, 2) }}
                                            <span class="text-muted">m³</span>
                                        </div>
                                        <small class="text-muted">{{ $latestReading->reading_date?->format('Y-m-d') ?? 'No date' }}</small>
                                    @else
                                        <span class="text-muted">No readings</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($latestReading && !is_null($latestReading->bill_amount))
                                        <div class="fw-semibold">
                                            {{ number_format((float) $latestReading->bill_amount, 2) }}
                                            <span class="text-muted">JOD</span>
                                        </div>
                                        <span class="badge rounded-pill fw-semibold {{ $latestReading->is_paid ? 'bg-success text-white' : 'bg-warning text-dark' }}">
                                            {{ $latestReading->is_paid ? 'Paid' : 'Unpaid' }}
                                        </span>
                                    @else
                                        <span class="text-muted">No bill</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($service->initial_meter_image)
                                        <a href="{{ asset('storage/' . $service->initial_meter_image) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-camera"></i> View
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($service->remarks)
                                        {{ \Illuminate\Support\Str::limit($service->remarks, 70) }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('water-services.show', $service) }}"
                                            class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted mb-0">No water services recorded.</p>
        @endif
    </div>

    <div class="service-section service-section--electricity">
        <div class="service-section__header">
            <h6 class="mb-0"><i class="bi bi-lightning me-2 text-warning"></i>Electricity Services</h6>
            <span class="service-section__count">{{ $building->electricityServices->count() }} record(s)</span>
        </div>
        @if ($building->electricityServices->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-sm align-middle service-section__table mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Company</th>
                            <th scope="col">Registration</th>
                            <th scope="col">Previous (kWh)</th>
                            <th scope="col">Current (kWh)</th>
                            <th scope="col">Usage (kWh)</th>
                            <th scope="col">Reading Date</th>
                            <th scope="col">Documents</th>
                            <th scope="col">Remarks</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($building->electricityServices as $service)
                            @php
                                $usage = null;
                                if (!is_null($service->current_reading) && !is_null($service->previous_reading)) {
                                    $usage = (float) $service->current_reading - (float) $service->previous_reading;
                                }
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $service->company_name }}</td>
                                <td>{{ $service->registration_number ?? '—' }}</td>
                                <td>{{ number_format($service->previous_reading ?? 0, 2) }}</td>
                                <td>{{ number_format($service->current_reading ?? 0, 2) }}</td>
                                <td>{{ $usage !== null ? number_format($usage, 2) : '—' }}</td>
                                <td>{{ optional($service->reading_date)->format('Y-m-d') ?? '—' }}</td>
                                <td>
                                    @if ($service->reset_file)
                                        @php $resetUrl = route('electricity-services.files.show', [$service, 'reset-file']); @endphp
                                        <div class="d-flex flex-wrap gap-2 align-items-center">
                                            <a href="{{ $resetUrl }}" target="_blank"
                                                class="btn btn-sm btn-outline-warning">
                                                <i class="bi bi-eye me-1"></i>View
                                            </a>
                                            <a href="{{ $resetUrl }}?download=1"
                                                class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-download me-1"></i>
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-muted">No uploads</span>
                                    @endif
                                </td>
                                <td>{{ $service->remarks ?: '—' }}</td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('electricity-services.show', $service) }}"
                                            class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted mb-0">No electricity services recorded.</p>
        @endif
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="bi bi-lightbulb me-2 text-orange"></i>Renovations</h5>
        </div>
        <div class="card-body">
            @forelse ($building->renovations as $innovation)
                <div class="border rounded-3 p-3 mb-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h6 class="mb-1 fw-semibold">{{ $innovation->name }}</h6>
                            <small class="text-muted">{{ $innovation->date->format('Y-m-d') }}</small>
                        </div>
                        <span class="stat-chip">
                            <i class="bi bi-currency-dollar text-success"></i>{{ number_format($innovation->cost, 2) }}
                            JOD
                        </span>
                    </div>
                    @if ($innovation->description)
                        <p class="mb-0 mt-2 text-muted">{{ $innovation->description }}</p>
                    @endif
                    <div class="mt-3 text-end">
                        <a href="{{ route('renovations.show', $innovation) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye me-1"></i>View details
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-muted mb-0 text-center">No renovations recorded.</p>
            @endforelse
        </div>
    </div>

    @if ($availableDocuments->isNotEmpty())
        <div class="document-viewer-card mt-4">
            <div class="p-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
                <h5 class="mb-0"><i class="bi bi-folder-symlink me-2 text-orange"></i>Document Viewer</h5>
                <span class="text-muted small">{{ $availableDocuments->count() }} document(s) available</span>
            </div>

            <!-- Document Tabs -->
            <div class="p-3">
                <ul class="nav nav-tabs mb-3" id="documentTabs" role="tablist">
                    @foreach ($availableDocuments as $index => $document)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ $document['slug'] }}"
                                data-bs-toggle="tab" data-bs-target="#content-{{ $document['slug'] }}" type="button"
                                role="tab" aria-controls="content-{{ $document['slug'] }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                <i
                                    class="bi bi-file-earmark-{{ $document['extension'] === 'pdf' ? 'pdf' : ($document['extension'] === 'dwg' ? 'code' : 'image') }} me-1"></i>
                                {{ $document['label'] }}
                            </button>
                        </li>
                    @endforeach
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="documentTabsContent">
                    @foreach ($availableDocuments as $index => $document)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                            id="content-{{ $document['slug'] }}" role="tabpanel"
                            aria-labelledby="tab-{{ $document['slug'] }}">

                            <div class="document-viewer-item">
                                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                                    <div>
                                        <div class="document-meta">
                                            {{ $document['file_name'] }}
                                            @if ($document['extension'])
                                                <span
                                                    class="ms-1 badge bg-light text-dark">{{ strtoupper($document['extension']) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="document-viewer-actions">
                                        <a href="{{ $document['download_url'] }}"
                                            class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-download me-1"></i>Download
                                        </a>
                                    </div>
                                </div>

                                @if (in_array($document['extension'], ['dwg', 'dxf']))
                                    {{-- DWG files cannot be previewed in browser --}}
                                    <div
                                        style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); border-radius: 16px; padding: 50px 40px; text-align: center;">
                                        <div
                                            style="background: rgba(255, 121, 0, 0.1); width: 100px; height: 100px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px;">
                                            <i class="bi bi-file-earmark-code"
                                                style="font-size: 50px; color: #ff7900;"></i>
                                        </div>
                                        <h3 style="color: #ffffff; margin-bottom: 15px; font-size: 22px;">Cannot Preview
                                            {{ strtoupper($document['extension']) }} File</h3>
                                        <p
                                            style="color: #cbd5e1; margin-bottom: 12px; font-size: 15px; line-height: 1.6; max-width: 500px; margin-left: auto; margin-right: auto;">
                                            DWG files cannot be opened in a web browser. Please download the file and open
                                            it with CAD software.
                                        </p>
                                        <p style="color: #94a3b8; margin-bottom: 35px; font-size: 14px;">
                                            File: <strong style="color: #cbd5e1;">{{ $document['file_name'] }}</strong>
                                        </p>

                                        {{-- Download Button --}}
                                        <a href="{{ $document['download_url'] }}" class="btn"
                                            style="background: #ff7900; color: white; border: none; padding: 16px 40px; border-radius: 10px; font-weight: 600; font-size: 16px; display: inline-flex; align-items: center; gap: 10px; text-decoration: none; transition: all 0.3s; box-shadow: 0 4px 15px rgba(255, 121, 0, 0.3);">
                                            <i class="bi bi-download" style="font-size: 20px;"></i>
                                            Download {{ strtoupper($document['extension']) }} File
                                        </a>

                                        {{-- Software Recommendations --}}
                                        <div
                                            style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 30px; margin-top: 35px;">
                                            <p
                                                style="color: #94a3b8; font-size: 13px; margin-bottom: 18px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                                <i class="bi bi-info-circle me-1"></i> Recommended Free Software
                                            </p>
                                            <div
                                                style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                                                <a href="https://www.autodesk.com/viewers" target="_blank"
                                                    style="background: rgba(59, 130, 246, 0.1); padding: 10px 18px; border-radius: 8px; color: #3b82f6; text-decoration: none; font-size: 13px; font-weight: 500; border: 1px solid rgba(59, 130, 246, 0.3); transition: all 0.3s;">
                                                    <i class="bi bi-star-fill me-1"></i>DWG TrueView
                                                </a>
                                                <a href="https://librecad.org/" target="_blank"
                                                    style="background: rgba(16, 185, 129, 0.1); padding: 10px 18px; border-radius: 8px; color: #10b981; text-decoration: none; font-size: 13px; font-weight: 500; border: 1px solid rgba(16, 185, 129, 0.3); transition: all 0.3s;">
                                                    <i class="bi bi-code-square me-1"></i>LibreCAD
                                                </a>
                                                <a href="https://www.freecadweb.org/" target="_blank"
                                                    style="background: rgba(139, 92, 246, 0.1); padding: 10px 18px; border-radius: 8px; color: #8b5cf6; text-decoration: none; font-size: 13px; font-weight: 500; border: 1px solid rgba(139, 92, 246, 0.3); transition: all 0.3s;">
                                                    <i class="bi bi-box me-1"></i>FreeCAD
                                                </a>
                                            </div>
                                            <p
                                                style="color: #94a3b8; font-size: 12px; margin-top: 15px; font-style: italic;">
                                                These are free applications that can open and view DWG files
                                            </p>
                                        </div>
                                    </div>
                                @elseif (in_array($document['extension'], ['pdf']))
                                    <iframe class="document-viewer-frame" src="{{ $document['inline_url'] }}"
                                        loading="lazy" frameborder="0"></iframe>
                                @elseif (in_array($document['extension'], ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                    <img src="{{ $document['inline_url'] }}" alt="{{ $document['label'] }} preview"
                                        class="document-viewer-frame" style="object-fit: contain; max-height: 600px;">
                                @else
                                    <div class="document-preview-fallback">
                                        <i class="bi bi-info-circle"></i>
                                        <p class="mb-0">Preview not available. Download to view this file locally.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endsection
