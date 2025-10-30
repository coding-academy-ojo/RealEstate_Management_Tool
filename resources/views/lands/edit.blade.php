@extends('layouts.app')

@section('title', 'Edit Land Parcel: ' . $land->plot_key)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('lands.index') }}">Lands</a></li>
    <li class="breadcrumb-item"><a href="{{ route('lands.show', $land) }}">{{ $land->plot_key }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    @php
        // Prepare current zoning status IDs
        $currentZoningNames = !empty($land->zoning) ? explode(',', $land->zoning) : [];
        $selectedZoningIds = [];
        if (!empty($currentZoningNames)) {
            foreach ($zoningStatuses as $status) {
                if (in_array(trim($status->name_ar), array_map('trim', $currentZoningNames))) {
                    $selectedZoningIds[] = $status->id;
                }
            }
        }
    @endphp

    <style>
        /* Zoning Wrapper - Main Container */
        .zoning-wrapper {
            border: 1px solid #dee2e6 !important;
            border-radius: 10px !important;
            background: #ffffff !important;
            overflow: hidden !important;
        }

        .zoning-search-box {
            padding: 12px 15px !important;
            background: #f8f9fa !important;
            border-bottom: 1px solid #dee2e6 !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
        }

        .zoning-search-box i {
            color: #6c757d !important;
            font-size: 1rem !important;
        }

        .zoning-search-box input {
            flex: 1 !important;
            border: none !important;
            background: white !important;
            padding: 6px 12px !important;
            border-radius: 6px !important;
            font-size: 0.9rem !important;
            outline: none !important;
            border: 1px solid #dee2e6 !important;
            transition: all 0.2s !important;
        }

        .zoning-search-box input:focus {
            border-color: #ff7900 !important;
            box-shadow: 0 0 0 2px rgba(255, 121, 0, 0.1) !important;
        }

        .zoning-selected-area {
            padding: 12px 15px !important;
            background: #fff9f5 !important;
            border-bottom: 1px solid #dee2e6 !important;
        }

        .selected-header {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            margin-bottom: 10px !important;
        }

        .selected-header span {
            font-size: 0.85rem !important;
            font-weight: 600 !important;
            color: #495057 !important;
        }

        .btn-add-zoning {
            background: #ff7900 !important;
            color: white !important;
            border: none !important;
            padding: 5px 12px !important;
            border-radius: 6px !important;
            font-size: 0.85rem !important;
            cursor: pointer !important;
            display: flex !important;
            align-items: center !important;
            gap: 5px !important;
            transition: all 0.2s !important;
        }

        .btn-add-zoning:hover {
            background: #e66d00 !important;
            transform: translateY(-1px) !important;
            color: white !important;
        }

        .btn-add-zoning i {
            font-size: 0.9rem !important;
        }

        .selected-badges {
            min-height: 32px !important;
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 6px !important;
        }

        .selected-badges .badge-item {
            background: #ff7900 !important;
            color: white !important;
            padding: 5px 10px !important;
            border-radius: 5px !important;
            font-size: 0.85rem !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px !important;
            animation: fadeIn 0.2s !important;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .selected-badges .badge-item i {
            cursor: pointer !important;
            font-size: 1rem !important;
            opacity: 0.9 !important;
            transition: opacity 0.2s !important;
        }

        .selected-badges .badge-item i:hover {
            opacity: 1 !important;
        }

        .no-selection {
            color: #adb5bd !important;
            font-size: 0.85rem !important;
            font-style: italic !important;
        }

        .zoning-scroll-area {
            height: 200px !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            padding: 8px !important;
            background: #fafbfc !important;
        }

        .zoning-scroll-area::-webkit-scrollbar {
            width: 8px !important;
        }

        .zoning-scroll-area::-webkit-scrollbar-track {
            background: #f1f3f5 !important;
        }

        .zoning-scroll-area::-webkit-scrollbar-thumb {
            background: #ff7900 !important;
            border-radius: 10px !important;
        }

        .zoning-scroll-area::-webkit-scrollbar-thumb:hover {
            background: #e66d00 !important;
        }

        .zoning-option {
            display: flex !important;
            align-items: center !important;
            padding: 10px 12px !important;
            margin-bottom: 6px !important;
            background: white !important;
            border: 2px solid #e9ecef !important;
            border-radius: 8px !important;
            cursor: pointer !important;
            transition: all 0.2s !important;
            position: relative !important;
            width: 100% !important;
        }

        .zoning-option:hover {
            border-color: #ff7900 !important;
            background: #fff9f5 !important;
            transform: translateX(3px) !important;
        }

        .zoning-option input[type="checkbox"] {
            position: absolute !important;
            opacity: 0 !important;
            cursor: pointer !important;
            width: 0 !important;
            height: 0 !important;
        }

        .checkbox-custom {
            width: 20px !important;
            height: 20px !important;
            border: 2px solid #dee2e6 !important;
            border-radius: 5px !important;
            margin-right: 12px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.2s !important;
            flex-shrink: 0 !important;
        }

        .zoning-option input:checked~.checkbox-custom {
            background: #ff7900 !important;
            border-color: #ff7900 !important;
        }

        .zoning-option input:checked~.checkbox-custom::after {
            content: "✓" !important;
            color: white !important;
            font-size: 14px !important;
            font-weight: bold !important;
        }

        .option-text {
            flex: 1 !important;
            font-size: 0.9rem !important;
            color: #495057 !important;
            transition: all 0.2s !important;
        }

        .zoning-option:hover .option-text {
            color: #ff7900 !important;
            font-weight: 500 !important;
        }

        .zoning-option input:checked~.option-text {
            color: #ff7900 !important;
            font-weight: 600 !important;
        }

        .no-results-msg {
            display: none;
            text-align: center !important;
            padding: 40px 20px !important;
            color: #6c757d !important;
            width: 100% !important;
            background: white !important;
            border: 2px dashed #dee2e6 !important;
            border-radius: 8px !important;
            margin-top: 10px !important;
        }

        .no-results-msg.show {
            display: block !important;
        }

        .no-results-msg i {
            font-size: 2.5rem !important;
            margin-bottom: 15px !important;
            display: block !important;
            color: #ff7900 !important;
        }

        .no-results-msg p {
            margin: 0 !important;
            font-size: 1rem !important;
            font-weight: 500 !important;
            color: #495057 !important;
        }
    </style>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-square me-2 text-orange"></i>
                        Edit Land Parcel: <strong>{{ $land->plot_key }}</strong>
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('lands.update', $land) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Hidden inputs for governorate and region -->
                        <input type="hidden" name="governorate" id="governorate" value="{{ old('governorate', $land->governorate) }}">
                        <input type="hidden" name="region" id="region" value="{{ old('region', $land->region) }}">

                        <!-- Site Selection -->
                        <div class="mb-4">
                            <label for="site_id" class="form-label fw-bold">
                                Site <span class="text-danger">*</span>
                            </label>
                            <select name="site_id" id="site_id" class="form-select @error('site_id') is-invalid @enderror" required>
                                <option value="">-- Select Site --</option>
                                @foreach ($sites as $site)
                                    <option value="{{ $site->id }}" {{ old('site_id', $land->site_id) == $site->id ? 'selected' : '' }}>
                                        {{ $site->code }} - {{ $site->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('site_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3 text-orange">
                            <i class="bi bi-info-circle me-2"></i>Complete Land Information
                        </h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="directorate" class="form-label fw-bold">
                                    Directorate (المديرية) <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="directorate" id="directorate"
                                    class="form-control @error('directorate') is-invalid @enderror"
                                    value="{{ old('directorate', $land->directorate) }}" required>
                                @error('directorate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="directorate_number" class="form-label fw-bold">
                                    Directorate Number (رقم المديرية) <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="directorate_number" id="directorate_number"
                                    class="form-control @error('directorate_number') is-invalid @enderror"
                                    value="{{ old('directorate_number', $land->directorate_number) }}" required>
                                @error('directorate_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="village" class="form-label fw-bold">
                                    Village (القرية)
                                </label>
                                <input type="text" name="village" id="village"
                                    class="form-control @error('village') is-invalid @enderror"
                                    value="{{ old('village', $land->village) }}">
                                @error('village')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="village_number" class="form-label fw-bold">
                                    Village Number (رقم القرية)
                                </label>
                                <input type="text" name="village_number" id="village_number"
                                    class="form-control @error('village_number') is-invalid @enderror"
                                    value="{{ old('village_number', $land->village_number) }}">
                                @error('village_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="basin" class="form-label fw-bold">
                                    Basin (الحوض) <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="basin" id="basin"
                                    class="form-control @error('basin') is-invalid @enderror" value="{{ old('basin', $land->basin) }}"
                                    required>
                                @error('basin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="basin_number" class="form-label fw-bold">
                                    Basin Number (رقم الحوض) <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="basin_number" id="basin_number"
                                    class="form-control @error('basin_number') is-invalid @enderror"
                                    value="{{ old('basin_number', $land->basin_number) }}" required>
                                @error('basin_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="neighborhood" class="form-label fw-bold">
                                    Neighborhood (الحي)
                                </label>
                                <input type="text" name="neighborhood" id="neighborhood"
                                    class="form-control @error('neighborhood') is-invalid @enderror"
                                    value="{{ old('neighborhood', $land->neighborhood) }}">
                                @error('neighborhood')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="neighborhood_number" class="form-label fw-bold">
                                    Neighborhood Number (رقم الحي)
                                </label>
                                <input type="text" name="neighborhood_number" id="neighborhood_number"
                                    class="form-control @error('neighborhood_number') is-invalid @enderror"
                                    value="{{ old('neighborhood_number', $land->neighborhood_number) }}">
                                @error('neighborhood_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="plot_number" class="form-label fw-bold">
                                    Plot Number (رقم القطعة) <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="plot_number" id="plot_number"
                                    class="form-control @error('plot_number') is-invalid @enderror"
                                    value="{{ old('plot_number', $land->plot_number) }}" required>
                                @error('plot_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="plot_key" class="form-label fw-bold">
                                    Plot Key (مفتاح القطعة) <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="plot_key" id="plot_key"
                                    class="form-control @error('plot_key') is-invalid @enderror"
                                    value="{{ old('plot_key', $land->plot_key) }}" required>
                                @error('plot_key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="area_m2" class="form-label fw-bold">
                                    Area (m²) <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.01" name="area_m2" id="area_m2"
                                    class="form-control @error('area_m2') is-invalid @enderror"
                                    value="{{ old('area_m2', $land->area_m2) }}" required min="0">
                                @error('area_m2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Site area will be auto-updated</small>
                            </div>
                        </div>

                        <!-- Zoning Status Section -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Zoning Status (التنظيم)</label>
                            <div class="zoning-wrapper">
                                <!-- Search Bar -->
                                <div class="zoning-search-box">
                                    <i class="bi bi-search"></i>
                                    <input type="text" id="zoningSearch" placeholder="Search zoning status...">
                                </div>

                                <!-- Selected Items Display -->
                                <div class="zoning-selected-area">
                                    <div class="selected-header">
                                        <span>Selected Items</span>
                                        <button type="button" class="btn-add-zoning" data-bs-toggle="modal"
                                            data-bs-target="#addZoningModal">
                                            <i class="bi bi-plus-circle"></i> Add New
                                        </button>
                                    </div>
                                    <div id="badgeContainer" class="selected-badges">
                                        <em class="no-selection">No items selected</em>
                                    </div>
                                </div>

                                <!-- Scrollable Checkbox List -->
                                <div class="zoning-scroll-area">
                                    <div id="noResults" class="no-results-msg">
                                        <i class="bi bi-search"></i>
                                        <p>No zoning status found</p>
                                    </div>
                                    @foreach ($zoningStatuses as $status)
                                        <label class="zoning-option" data-name="{{ strtolower($status->name_ar) }}">
                                            <input type="checkbox" class="zoning-checkbox" name="zoning_statuses[]"
                                                value="{{ $status->id }}" id="zoning_{{ $status->id }}"
                                                {{ in_array($status->id, old('zoning_statuses', $selectedZoningIds)) ? 'checked' : '' }}>
                                            <span class="checkbox-custom"></span>
                                            <span class="option-text">{{ $status->name_ar }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            @error('zoning_statuses')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="map_location" class="form-label fw-bold">
                                    Google Maps URL
                                </label>
                                <input type="url" name="map_location" id="map_location"
                                    class="form-control @error('map_location') is-invalid @enderror"
                                    value="{{ old('map_location', $land->map_location) }}"
                                    placeholder="Paste Google Maps URL here (e.g., https://maps.google.com/?q=31.9539,35.9106)">
                                @error('map_location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Coordinates will be automatically extracted</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="latitude" class="form-label fw-bold">
                                    Latitude (خط العرض)
                                </label>
                                <input type="number" step="0.0000001" name="latitude" id="latitude"
                                    class="form-control @error('latitude') is-invalid @enderror"
                                    value="{{ old('latitude', $land->latitude) }}" placeholder="e.g., 31.9539">
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="longitude" class="form-label fw-bold">
                                    Longitude (خط الطول)
                                </label>
                                <input type="number" step="0.0000001" name="longitude" id="longitude"
                                    class="form-control @error('longitude') is-invalid @enderror"
                                    value="{{ old('longitude', $land->longitude) }}" placeholder="e.g., 35.9106">
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3 text-orange">
                            <i class="bi bi-file-earmark-text me-2"></i>Documents
                        </h5>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="ownership_doc" class="form-label fw-bold">
                                    سند الملكية
                                </label>
                                <input type="file" name="ownership_doc" id="ownership_doc"
                                    class="form-control @error('ownership_doc') is-invalid @enderror"
                                    accept=".jpg,.jpeg,.pdf">
                                @error('ownership_doc')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">JPG or PDF only (Max: 10MB)</small>
                                @if ($land->ownership_doc)
                                    <div class="mt-2">
                                        <a href="{{ route('lands.documents.show', [$land, 'ownership']) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> View Current Document
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="site_plan" class="form-label fw-bold">
                                    مخطط الموقع
                                </label>
                                <input type="file" name="site_plan" id="site_plan"
                                    class="form-control @error('site_plan') is-invalid @enderror"
                                    accept=".jpg,.jpeg,.pdf">
                                @error('site_plan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">JPG or PDF only (Max: 10MB)</small>
                                @if ($land->site_plan)
                                    <div class="mt-2">
                                        <a href="{{ route('lands.documents.show', [$land, 'site-plan']) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> View Current Document
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="zoning_plan" class="form-label fw-bold">
                                    مخطط تنظيمي
                                </label>
                                <input type="file" name="zoning_plan" id="zoning_plan"
                                    class="form-control @error('zoning_plan') is-invalid @enderror"
                                    accept=".jpg,.jpeg,.pdf">
                                @error('zoning_plan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">JPG or PDF only (Max: 10MB)</small>
                                @if ($land->zoning_plan)
                                    <div class="mt-2">
                                        <a href="{{ route('lands.documents.show', [$land, 'zoning-plan']) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> View Current Document
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Note:</strong> Upload new documents only if you want to replace the existing ones.
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <a href="{{ route('lands.show', $land) }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-orange">
                                <i class="bi bi-check-circle me-1"></i> Update Land
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Adding New Zoning Status -->
    <div class="modal fade" id="addZoningModal" tabindex="-1" aria-labelledby="addZoningModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addZoningModalLabel">
                        <i class="bi bi-plus-circle me-2 text-orange"></i>
                        Add New Zoning Status
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="new_zoning_name" class="form-label fw-bold">
                            Zoning Status Name (Arabic) <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="new_zoning_name" class="form-control"
                            placeholder="Example: سكن تجاري">
                        <div class="invalid-feedback" id="zoning_error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-orange" id="saveNewZoning">
                        <i class="bi bi-check-circle me-1"></i> Save
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const siteSelect = document.getElementById('site_id');
            const governorateInput = document.getElementById('governorate');
            const regionInput = document.getElementById('region');

            // All sites data from backend
            const allSites = @json($sites);

            // Governorate full names mapping
            const governorateNames = {
                'AM': 'Amman',
                'IR': 'Irbid',
                'MF': 'Mafraq',
                'AJ': 'Ajloun',
                'JA': 'Jerash',
                'BA': 'Balqa',
                'ZA': 'Zarqa',
                'MA': 'Madaba',
                'AQ': 'Aqaba',
                'KA': 'Karak',
                'TF': 'Tafileh',
                'MN': 'Ma\'an'
            };

            // Governorate to Region mapping
            const governorateToRegion = {
                'AM': 'Capital',
                'IR': 'North',
                'MF': 'North',
                'AJ': 'North',
                'JA': 'North',
                'BA': 'Middle',
                'ZA': 'Middle',
                'MA': 'Middle',
                'AQ': 'South',
                'KA': 'South',
                'TF': 'South',
                'MN': 'South'
            };

            // When site is selected, auto-fill governorate and region
            siteSelect.addEventListener('change', function() {
                const selectedSiteId = this.value;

                if (selectedSiteId) {
                    const selectedSite = allSites.find(site => site.id == selectedSiteId);
                    if (selectedSite) {
                        governorateInput.value = governorateNames[selectedSite.governorate] || selectedSite.governorate;
                        regionInput.value = governorateToRegion[selectedSite.governorate] || '';
                    }
                } else {
                    governorateInput.value = '';
                    regionInput.value = '';
                }
            });

            // Trigger change on page load to set initial values
            if (siteSelect.value) {
                siteSelect.dispatchEvent(new Event('change'));
            }

            // Google Maps URL coordinate extraction
            const mapLocationInput = document.getElementById('map_location');
            const latitudeInput = document.getElementById('latitude');
            const longitudeInput = document.getElementById('longitude');

            mapLocationInput.addEventListener('blur', function() {
                const url = this.value.trim();
                if (url) {
                    extractCoordinates(url);
                }
            });

            function extractCoordinates(url) {
                try {
                    // Pattern 1: https://www.google.com/maps/place/32.4011265,36.3359156
                    let match = url.match(/place\/([-+]?\d+\.?\d*),([-+]?\d+\.?\d*)/);

                    // Pattern 2: @32.4011265,36.3359156,17z
                    if (!match) {
                        match = url.match(/@([-+]?\d+\.?\d*),([-+]?\d+\.?\d*)/);
                    }

                    // Pattern 3: ?q=32.4011265,36.3359156
                    if (!match) {
                        match = url.match(/[?&]q=([-+]?\d+\.?\d*),([-+]?\d+\.?\d*)/);
                    }

                    // Pattern 4: /maps?q=32.4011265,36.3359156
                    if (!match) {
                        match = url.match(/maps\?q=([-+]?\d+\.?\d*),([-+]?\d+\.?\d*)/);
                    }

                    // Pattern 5: 3m2!3d32.4011265!4d36.3359156 (from data parameter)
                    if (!match) {
                        const latMatch = url.match(/!3d([-+]?\d+\.?\d*)/);
                        const lngMatch = url.match(/!4d([-+]?\d+\.?\d*)/);
                        if (latMatch && lngMatch) {
                            match = [null, latMatch[1], lngMatch[1]];
                        }
                    }

                    if (match && match[1] && match[2]) {
                        const lat = parseFloat(match[1]);
                        const lng = parseFloat(match[2]);

                        // Validate coordinates (Jordan is roughly 29-33°N, 35-39°E)
                        if (lat >= 29 && lat <= 34 && lng >= 34 && lng <= 40) {
                            latitudeInput.value = lat;
                            longitudeInput.value = lng;

                            // Show success message
                            showCoordinateMessage('success', 'Coordinates extracted successfully!');
                        } else {
                            showCoordinateMessage('warning',
                                'Coordinates extracted but seem outside Jordan. Please verify.');
                            latitudeInput.value = lat;
                            longitudeInput.value = lng;
                        }
                    } else {
                        showCoordinateMessage('warning', 'Could not extract coordinates. Please enter manually.');
                    }
                } catch (error) {
                    console.error('Error extracting coordinates:', error);
                    showCoordinateMessage('error', 'Error extracting coordinates. Please enter manually.');
                }
            }

            function showCoordinateMessage(type, message) {
                // Remove any existing message
                const existingAlert = document.querySelector('.coordinate-alert');
                if (existingAlert) {
                    existingAlert.remove();
                }

                // Create new alert
                const alertDiv = document.createElement('div');
                alertDiv.className =
                    `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'warning'} alert-dismissible fade show coordinate-alert mt-2`;
                alertDiv.innerHTML = `
                    <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;

                mapLocationInput.parentElement.appendChild(alertDiv);

                // Auto-dismiss after 5 seconds
                setTimeout(() => {
                    if (alertDiv.parentElement) {
                        alertDiv.remove();
                    }
                }, 5000);
            }

            // ============ Zoning Status Checkboxes and Badges ============
            const zoningCheckboxes = document.querySelectorAll('.zoning-checkbox');
            const badgeContainer = document.getElementById('badgeContainer');

            function updateBadges() {
                badgeContainer.innerHTML = '';
                const checkedBoxes = document.querySelectorAll('.zoning-checkbox:checked');

                if (checkedBoxes.length === 0) {
                    badgeContainer.innerHTML = '<em class="no-selection">No items selected</em>';
                    return;
                }

                checkedBoxes.forEach(checkbox => {
                    const label = checkbox.closest('.zoning-option').querySelector('.option-text')
                        .textContent.trim();
                    const badge = document.createElement('span');
                    badge.className = 'badge-item';
                    badge.innerHTML = `
                        ${label}
                        <i class="bi bi-x-circle" data-checkbox-id="${checkbox.id}"></i>
                    `;
                    badgeContainer.appendChild(badge);

                    // Add click handler to remove badge
                    badge.querySelector('i').addEventListener('click', function(e) {
                        e.stopPropagation();
                        checkbox.checked = false;
                        updateBadges();
                    });
                });
            }

            zoningCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateBadges);
            });

            // Initialize badges on page load
            updateBadges();

            // Zoning Search functionality
            const zoningSearch = document.getElementById('zoningSearch');
            const noResults = document.getElementById('noResults');

            zoningSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                const zoningItems = document.querySelectorAll('.zoning-option');
                let hasResults = false;

                zoningItems.forEach((item) => {
                    const name = item.getAttribute('data-name');

                    if (searchTerm === '' || (name && name.includes(searchTerm))) {
                        item.style.cssText = 'display: flex !important;';
                        hasResults = true;
                    } else {
                        item.style.cssText = 'display: none !important;';
                    }
                });

                // Show/hide no results message
                if (noResults) {
                    if (hasResults) {
                        noResults.classList.remove('show');
                        noResults.style.display = 'none';
                    } else {
                        noResults.classList.add('show');
                        noResults.style.display = 'block';
                    }
                }
            });

            // ============ Handle New Zoning Status Creation ============
            const saveNewZoningBtn = document.getElementById('saveNewZoning');
            const newZoningNameInput = document.getElementById('new_zoning_name');
            const zoningError = document.getElementById('zoning_error');
            const addZoningModalEl = document.getElementById('addZoningModal');

            saveNewZoningBtn.addEventListener('click', async function() {
                const name = newZoningNameInput.value.trim();

                if (!name) {
                    newZoningNameInput.classList.add('is-invalid');
                    zoningError.textContent = 'Please enter zoning status name';
                    zoningError.style.display = 'block';
                    return;
                }

                newZoningNameInput.classList.remove('is-invalid');
                zoningError.style.display = 'none';
                saveNewZoningBtn.disabled = true;
                saveNewZoningBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

                try {
                    const response = await fetch('{{ route('zoning-statuses.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            name_ar: name
                        })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        // Add new checkbox to the list
                        const scrollArea = document.querySelector('.zoning-scroll-area');
                        const noResultsMsg = document.getElementById('noResults');
                        const newOption = document.createElement('label');
                        newOption.className = 'zoning-option';
                        newOption.setAttribute('data-name', data.name_ar.toLowerCase());
                        newOption.innerHTML = `
                            <input type="checkbox" class="zoning-checkbox"
                                name="zoning_statuses[]"
                                value="${data.id}"
                                id="zoning_${data.id}" checked>
                            <span class="checkbox-custom"></span>
                            <span class="option-text">${data.name_ar}</span>
                        `;

                        // Insert after noResults div
                        if (noResultsMsg.nextSibling) {
                            scrollArea.insertBefore(newOption, noResultsMsg.nextSibling);
                        } else {
                            scrollArea.appendChild(newOption);
                        }

                        // Add event listener to new checkbox
                        const newCheckbox = newOption.querySelector('.zoning-checkbox');
                        newCheckbox.addEventListener('change', updateBadges);

                        // Update badges
                        updateBadges();

                        // Clear input
                        newZoningNameInput.value = '';

                        // Close modal manually (for Boosted compatibility)
                        const modalBackdrop = document.querySelector('.modal-backdrop');
                        addZoningModalEl.classList.remove('show');
                        addZoningModalEl.style.display = 'none';
                        document.body.classList.remove('modal-open');
                        if (modalBackdrop) {
                            modalBackdrop.remove();
                        }
                    } else {
                        throw new Error(data.message || 'Error saving zoning status');
                    }
                } catch (error) {
                    newZoningNameInput.classList.add('is-invalid');
                    zoningError.textContent = error.message;
                    zoningError.style.display = 'block';
                } finally {
                    saveNewZoningBtn.disabled = false;
                    saveNewZoningBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Save';
                }
            });

            // Clear validation on input
            newZoningNameInput.addEventListener('input', function() {
                this.classList.remove('is-invalid');
                zoningError.style.display = 'none';
            });
        });
    </script>
@endpush
