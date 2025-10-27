@extends('layouts.app')

@section('title', 'Edit Site')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('sites.index') }}">Sites</a></li>
    <li class="breadcrumb-item"><a href="{{ route('sites.show', $site) }}">{{ $site->name }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <style>
        /* Zoning Wrapper - Main Container */
        .zoning-wrapper {
            border: 1px solid #dee2e6 !important;
            border-radius: 10px !important;
            background: #ffffff !important;
            overflow: hidden !important;
        }

        /* Search Box */
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

        /* Selected Items Area */
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

        /* Scrollable Area */
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

        /* Individual Zoning Option */
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

        /* No Results Message */
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

        /* Land Cards Styles */
        .land-card {
            border: 2px solid #dee2e6;
            border-radius: 10px;
            margin-bottom: 15px;
            background: white;
            transition: all 0.3s;
        }

        .land-card.collapsed .land-card-body {
            display: none;
        }

        .land-card-header {
            padding: 12px 15px;
            background: linear-gradient(135deg, #ff7900 0%, #ff9940 100%);
            color: white;
            border-radius: 8px 8px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            user-select: none;
        }

        .land-card-header:hover {
            background: linear-gradient(135deg, #e66d00 0%, #ff8830 100%);
        }

        .land-card-title {
            font-weight: 600;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .land-card-actions {
            display: flex;
            gap: 8px;
        }

        .land-card-actions button {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 0.85rem;
            transition: all 0.2s;
        }

        .land-card-actions button:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .land-card-body {
            padding: 15px;
        }

        .btn-add-land {
            width: 100%;
            border: 2px dashed #ff7900;
            background: #fff9f5;
            color: #ff7900;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-add-land:hover {
            background: #ff7900;
            color: white;
            border-style: solid;
        }

        .lands-sidebar {
            position: sticky;
            top: 20px;
            max-height: calc(100vh - 100px);
            overflow-y: auto;
        }

        .lands-sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .lands-sidebar::-webkit-scrollbar-track {
            background: #f1f3f5;
            border-radius: 10px;
        }

        .lands-sidebar::-webkit-scrollbar-thumb {
            background: #ff7900;
            border-radius: 10px;
        }
    </style>
    <form action="{{ route('sites.update', $site) }}" method="POST" id="siteForm" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <!-- Left Side: Site Form -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h4 class="mb-0">
                            <i class="bi bi-pencil-square me-2 text-orange"></i>
                            Edit Site: {{ $site->name }}
                        </h4>
                    </div>
                    <div class="card-body p-4">

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="name" class="form-label fw-bold">
                                    Site Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $site->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Hidden field for cluster_no -->
                        <input type="hidden" name="cluster_no" id="cluster_no"
                            value="{{ old('cluster_no', $site->cluster_no) }}">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="governorate" class="form-label fw-bold">
                                    Governorate <span class="text-danger">*</span>
                                </label>
                                <select name="governorate" id="governorate"
                                    class="form-select @error('governorate') is-invalid @enderror" required>
                                    <option value="">-- Select Governorate --</option>

                                    <optgroup label="Region 1 - Capital">
                                        <option value="AM"
                                            {{ old('governorate', $site->governorate) == 'AM' ? 'selected' : '' }}>Amman
                                            (عمّان)</option>
                                    </optgroup>

                                    <optgroup label="Region 2 - North">
                                        <option value="IR"
                                            {{ old('governorate', $site->governorate) == 'IR' ? 'selected' : '' }}>Irbid
                                            (إربد)</option>
                                        <option value="MF"
                                            {{ old('governorate', $site->governorate) == 'MF' ? 'selected' : '' }}>Mafraq
                                            (المفرق)</option>
                                        <option value="AJ"
                                            {{ old('governorate', $site->governorate) == 'AJ' ? 'selected' : '' }}>Ajloun
                                            (عجلون)</option>
                                        <option value="JA"
                                            {{ old('governorate', $site->governorate) == 'JA' ? 'selected' : '' }}>Jerash
                                            (جرش)</option>
                                    </optgroup>

                                    <optgroup label="Region 3 - Middle">
                                        <option value="BA"
                                            {{ old('governorate', $site->governorate) == 'BA' ? 'selected' : '' }}>Balqa
                                            (البلقاء)</option>
                                        <option value="ZA"
                                            {{ old('governorate', $site->governorate) == 'ZA' ? 'selected' : '' }}>Zarqa
                                            (الزرقاء)</option>
                                        <option value="MA"
                                            {{ old('governorate', $site->governorate) == 'MA' ? 'selected' : '' }}>Madaba
                                            (مادبا)</option>
                                    </optgroup>

                                    <optgroup label="Region 4 - South">
                                        <option value="AQ"
                                            {{ old('governorate', $site->governorate) == 'AQ' ? 'selected' : '' }}>Aqaba
                                            (العقبة)</option>
                                        <option value="KA"
                                            {{ old('governorate', $site->governorate) == 'KA' ? 'selected' : '' }}>Karak
                                            (الكرك)</option>
                                        <option value="TF"
                                            {{ old('governorate', $site->governorate) == 'TF' ? 'selected' : '' }}>Tafileh
                                            (الطفيلة)</option>
                                        <option value="MN"
                                            {{ old('governorate', $site->governorate) == 'MN' ? 'selected' : '' }}>Ma'an
                                            (معان)</option>
                                    </optgroup>
                                </select>
                                @error('governorate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Changing governorate will update region and code</small>
                            </div>
                        </div>

                        <!-- Site Area Input -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="area_m2" class="form-label fw-bold">
                                    Site Area (m²) <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="area_m2" id="area_m2" step="0.01"
                                    class="form-control @error('area_m2') is-invalid @enderror"
                                    value="{{ old('area_m2', $site->area_m2) }}" required>
                                @error('area_m2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Zoning Status Section -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Zoning Status</label>

                            <div class="zoning-wrapper">
                                <!-- Search Bar -->
                                <div class="zoning-search-box">
                                    <i class="bi bi-search"></i>
                                    <input type="text" id="zoningSearch" placeholder="Search zoning status...">
                                </div>

                                <!-- Selected Items Display -->
                                <div class="zoning-selected-area">
                                    <div class="selected-header">
                                        <span><i class="bi bi-check-circle me-1"></i> Selected Items</span>
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
                                    @php
                                        $selectedZoningIds = old(
                                            'zoning_statuses',
                                            $site->zoningStatuses->pluck('id')->toArray(),
                                        );
                                        $noneOption = $zoningStatuses->firstWhere('name_ar', 'لا يوجد');
                                        $otherOptions = $zoningStatuses->where('name_ar', '!=', 'لا يوجد');
                                    @endphp

                                    {{-- Show "لا يوجد" first if exists --}}
                                    @if ($noneOption)
                                        <label class="zoning-option zoning-item"
                                            data-name="{{ strtolower($noneOption->name_ar) }}">
                                            <input type="checkbox" class="zoning-checkbox" name="zoning_statuses[]"
                                                value="{{ $noneOption->id }}" id="zoning_{{ $noneOption->id }}"
                                                {{ in_array($noneOption->id, $selectedZoningIds) ? 'checked' : '' }}>
                                            <span class="checkbox-custom"></span>
                                            <span class="option-text">{{ $noneOption->name_ar }}</span>
                                        </label>
                                    @endif

                                    {{-- Show all other options --}}
                                    @foreach ($otherOptions as $zoning)
                                        <label class="zoning-option zoning-item"
                                            data-name="{{ strtolower($zoning->name_ar) }}">
                                            <input type="checkbox" class="zoning-checkbox" name="zoning_statuses[]"
                                                value="{{ $zoning->id }}" id="zoning_{{ $zoning->id }}"
                                                {{ in_array($zoning->id, $selectedZoningIds) ? 'checked' : '' }}>
                                            <span class="checkbox-custom"></span>
                                            <span class="option-text">{{ $zoning->name_ar }}</span>
                                        </label>
                                    @endforeach

                                    <div id="noResults" class="no-results-msg">
                                        <i class="bi bi-search"></i>
                                        <p>No results found</p>
                                    </div>
                                </div>
                            </div>

                            @error('zoning_statuses')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label fw-bold">Notes</label>
                            <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $site->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Other Documents Upload -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                Other Documents (مستندات أخرى)
                            </label>

                            @if ($site->other_documents && count($site->other_documents) > 0)
                                <div class="mb-3">
                                    <label class="form-label small text-muted">Existing Documents:</label>
                                    <div class="list-group">
                                        @foreach ($site->other_documents as $index => $doc)
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div class="flex-grow-1">
                                                    <i class="bi bi-file-earmark-text me-2"></i>
                                                    <strong>{{ $doc['name'] ?? 'Document' }}</strong>
                                                    <br>
                                                    <small
                                                        class="text-muted ms-4">{{ $doc['original_name'] ?? basename($doc['path'] ?? $doc) }}</small>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <a href="{{ asset('storage/' . ($doc['path'] ?? $doc)) }}"
                                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <div class="form-check">
                                                        <input type="checkbox" name="remove_documents[]"
                                                            value="{{ $index }}" class="form-check-input"
                                                            id="remove_doc_{{ $index }}">
                                                        <label class="form-check-label small text-danger"
                                                            for="remove_doc_{{ $index }}">
                                                            Remove
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <label class="form-label small">Add New Documents:</label>
                            <div id="edit-documents-container">
                                <!-- Initial document field -->
                                <div class="document-item mb-3 p-3 border rounded">
                                    <div class="row align-items-end">
                                        <div class="col-md-5 mb-2 mb-md-0">
                                            <label class="form-label small">Document Name (اسم المستند)</label>
                                            <input type="text" name="document_names[]"
                                                class="form-control form-control-sm" placeholder="e.g., Contract, Permit">
                                        </div>
                                        <div class="col-md-6 mb-2 mb-md-0">
                                            <label class="form-label small">File (الملف)</label>
                                            <input type="file" name="other_documents[]"
                                                class="form-control form-control-sm document-file"
                                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-sm btn-danger w-100 remove-doc"
                                                disabled>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" id="add-edit-document-btn" class="btn btn-sm btn-outline-orange"
                                disabled>
                                <i class="bi bi-plus-circle me-1"></i> Add Another Document
                            </button>

                            <div class="form-text mt-2">
                                <i class="bi bi-info-circle me-1"></i>
                                Accepted formats: PDF, JPG, PNG, DOC, DOCX (Max 10MB each)
                            </div>
                            @error('other_documents.*')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <a href="{{ route('sites.show', $site) }}" class="btn btn-light">
                                <i class="bi bi-x-circle me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-orange">
                                <i class="bi bi-check-circle me-1"></i> Update Site
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Lands Section -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm lands-sidebar">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-map-fill me-2 text-orange"></i>
                            Lands
                        </h5>
                        <span class="badge bg-orange" id="landCount">{{ $site->lands->count() }} Lands</span>
                    </div>
                    <div class="card-body p-3">
                        <!-- Lands Container -->
                        <div id="landsContainer">
                            @if ($site->lands->isEmpty())
                                <div class="text-center text-muted py-5" id="emptyLandsMessage">
                                    <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="mt-3">No lands added yet</p>
                                    <small>Click "Add Land" to start</small>
                                </div>
                            @endif
                        </div>

                        <!-- Add Land Button -->
                        <button type="button" class="btn-add-land" id="addLandBtn">
                            <i class="bi bi-plus-circle me-2"></i>
                            Add Land
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

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
            // Handle zoning status checkboxes and badges
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

            // Search functionality
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

            // Handle new zoning status creation
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
                        const newOption = document.createElement('label');
                        newOption.className = 'zoning-option zoning-item';
                        newOption.setAttribute('data-name', data.name_ar.toLowerCase());
                        newOption.innerHTML = `
                            <input type="checkbox" class="zoning-checkbox"
                                name="zoning_statuses[]"
                                value="${data.id}"
                                id="zoning_${data.id}" checked>
                            <span class="checkbox-custom"></span>
                            <span class="option-text">${data.name_ar}</span>
                        `;
                        scrollArea.insertBefore(newOption, scrollArea.firstChild);

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

        // Other Documents Management for Edit
        const editDocumentsContainer = document.getElementById('edit-documents-container');
        const addEditDocumentBtn = document.getElementById('add-edit-document-btn');

        if (editDocumentsContainer && addEditDocumentBtn) {
            // Function to check if a file is selected
            function checkLastEditDocumentFile() {
                const allDocItems = editDocumentsContainer.querySelectorAll('.document-item');
                const lastDocItem = allDocItems[allDocItems.length - 1];
                const lastFileInput = lastDocItem.querySelector('.document-file');

                if (lastFileInput && lastFileInput.files.length > 0) {
                    addEditDocumentBtn.disabled = false;
                } else {
                    addEditDocumentBtn.disabled = true;
                }
            }

            // Function to update remove buttons
            function updateEditRemoveButtons() {
                const allDocItems = editDocumentsContainer.querySelectorAll('.document-item');
                allDocItems.forEach((item, index) => {
                    const removeBtn = item.querySelector('.remove-doc');
                    removeBtn.disabled = allDocItems.length === 1;
                });
            }

            // Add change event to file inputs
            editDocumentsContainer.addEventListener('change', function(e) {
                if (e.target.classList.contains('document-file')) {
                    checkLastEditDocumentFile();
                }
            });

            // Add document button click
            addEditDocumentBtn.addEventListener('click', function() {
                const newDocItem = document.createElement('div');
                newDocItem.className = 'document-item mb-3 p-3 border rounded';
                newDocItem.innerHTML = `
                    <div class="row align-items-end">
                        <div class="col-md-5 mb-2 mb-md-0">
                            <label class="form-label small">Document Name (اسم المستند)</label>
                            <input type="text" name="document_names[]" class="form-control form-control-sm"
                                   placeholder="e.g., Contract, Permit">
                        </div>
                        <div class="col-md-6 mb-2 mb-md-0">
                            <label class="form-label small">File (الملف)</label>
                            <input type="file" name="other_documents[]"
                                   class="form-control form-control-sm document-file"
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-sm btn-danger w-100 remove-doc">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `;

                editDocumentsContainer.appendChild(newDocItem);
                addEditDocumentBtn.disabled = true;
                updateEditRemoveButtons();

                // Scroll to new document
                setTimeout(() => {
                    newDocItem.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }, 100);
            });

            // Remove document
            editDocumentsContainer.addEventListener('click', function(e) {
                if (e.target.closest('.remove-doc')) {
                    const docItem = e.target.closest('.document-item');
                    docItem.remove();
                    checkLastEditDocumentFile();
                    updateEditRemoveButtons();
                }
            });

            // Initial check
            updateEditRemoveButtons();
        }

        // Lands Management
        document.addEventListener('DOMContentLoaded', function() {
            const landsContainer = document.getElementById('landsContainer');
            const addLandBtn = document.getElementById('addLandBtn');
            const emptyLandsMessage = document.getElementById('emptyLandsMessage');
            const siteForm = document.getElementById('siteForm');
            let landCounter = {{ $site->lands->count() }};
            let displayCounter = 0;

            function updateLandCount() {
                const landCards = landsContainer.querySelectorAll('.land-card');
                const count = landCards.length;
                document.getElementById('landCount').textContent = count + ' Land' + (count !== 1 ? 's' : '');

                // Update land numbers
                landCards.forEach((card, index) => {
                    const titleSpan = card.querySelector('.land-card-title span');
                    if (titleSpan) {
                        titleSpan.textContent = `Land #${index + 1}`;
                    }
                });

                if (count === 0 && emptyLandsMessage) {
                    emptyLandsMessage.style.display = 'block';
                } else if (emptyLandsMessage) {
                    emptyLandsMessage.style.display = 'none';
                }
            }

            function createLandCard(landData = null, isExisting = false) {
                landCounter++;
                displayCounter++;
                const landCard = document.createElement('div');
                landCard.className = 'land-card';
                landCard.setAttribute('data-land-id', landCounter);

                const landId = landData?.id || '';
                const directorate = landData?.directorate || '';
                const directorateNumber = landData?.directorate_number || '';
                const village = landData?.village || '';
                const villageNumber = landData?.village_number || '';
                const basin = landData?.basin || '';
                const basinNumber = landData?.basin_number || '';
                const neighborhood = landData?.neighborhood || '';
                const neighborhoodNumber = landData?.neighborhood_number || '';
                const plotNumber = landData?.plot_number || '';
                const plotKey = landData?.plot_key || '';
                const areaM2 = landData?.area_m2 || '';
                const mapLocation = landData?.map_location || '';
                const latitude = landData?.latitude || '';
                const longitude = landData?.longitude || '';

                landCard.innerHTML = `
                    <div class="land-card-header">
                        <div class="land-card-title">
                            <i class="bi bi-map"></i>
                            <span>Land #${displayCounter}</span>
                        </div>
                        <div class="land-card-actions">
                            <button type="button" class="btn-toggle-land" title="Minimize/Expand">
                                <i class="bi bi-chevron-up"></i>
                            </button>
                            <button type="button" class="btn-remove-land" title="Remove">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="land-card-body">
                        ${isExisting ? `<input type="hidden" name="lands[${landCounter}][id]" value="${landId}">` : ''}
                        ${isExisting ? `<input type="hidden" name="lands[${landCounter}][_action]" value="update">` : `<input type="hidden" name="lands[${landCounter}][_action]" value="create">`}

                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold" style="font-size: 0.85rem;">Directorate (المديرية) <span class="text-danger">*</span></label>
                                <input type="text" name="lands[${landCounter}][directorate]" class="form-control form-control-sm"
                                       placeholder="e.g., Amman" value="${directorate}" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold" style="font-size: 0.85rem;">Directorate Number (رقم المديرية) <span class="text-danger">*</span></label>
                                <input type="text" name="lands[${landCounter}][directorate_number]" class="form-control form-control-sm"
                                       placeholder="e.g., 123" value="${directorateNumber}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold" style="font-size: 0.85rem;">Village (القرية)</label>
                                <input type="text" name="lands[${landCounter}][village]" class="form-control form-control-sm"
                                       placeholder="Optional" value="${village}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold" style="font-size: 0.85rem;">Village Number (رقم القرية)</label>
                                <input type="text" name="lands[${landCounter}][village_number]" class="form-control form-control-sm"
                                       placeholder="Optional" value="${villageNumber}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold" style="font-size: 0.85rem;">Basin (الحوض) <span class="text-danger">*</span></label>
                                <input type="text" name="lands[${landCounter}][basin]" class="form-control form-control-sm"
                                       placeholder="e.g., Basin 5" value="${basin}" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold" style="font-size: 0.85rem;">Basin Number (رقم الحوض) <span class="text-danger">*</span></label>
                                <input type="text" name="lands[${landCounter}][basin_number]" class="form-control form-control-sm"
                                       placeholder="e.g., 10" value="${basinNumber}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold" style="font-size: 0.85rem;">Neighborhood (الحي)</label>
                                <input type="text" name="lands[${landCounter}][neighborhood]" class="form-control form-control-sm"
                                       placeholder="Optional" value="${neighborhood}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold" style="font-size: 0.85rem;">Neighborhood Number (رقم الحي)</label>
                                <input type="text" name="lands[${landCounter}][neighborhood_number]" class="form-control form-control-sm"
                                       placeholder="Optional" value="${neighborhoodNumber}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold" style="font-size: 0.85rem;">Plot Number (رقم القطعة) <span class="text-danger">*</span></label>
                                <input type="text" name="lands[${landCounter}][plot_number]" class="form-control form-control-sm"
                                       placeholder="e.g., 123" value="${plotNumber}" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold" style="font-size: 0.85rem;">Plot Key (مفتاح القطعة) <span class="text-danger">*</span></label>
                                <input type="text" name="lands[${landCounter}][plot_key]" class="form-control form-control-sm"
                                       placeholder="e.g., A-123" value="${plotKey}" required>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Area (المساحة) m² <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="lands[${landCounter}][area_m2]" class="form-control form-control-sm"
                                   placeholder="e.g., 500" value="${areaM2}" required min="0">
                            <small class="text-muted" style="font-size: 0.75rem;">Site area will be auto-updated</small>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Google Maps URL (رابط الخريطة)</label>
                            <input type="url" name="lands[${landCounter}][map_location]" class="form-control form-control-sm land-map-url"
                                   placeholder="Paste Google Maps URL" value="${mapLocation}">
                            <small class="text-muted" style="font-size: 0.75rem;">Coordinates will be auto-extracted</small>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-2">
                                <label class="form-label fw-bold" style="font-size: 0.85rem;">Latitude (خط العرض)</label>
                                <input type="number" step="0.0000001" name="lands[${landCounter}][latitude]"
                                       class="form-control form-control-sm land-latitude" placeholder="31.9539" value="${latitude}">
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label fw-bold" style="font-size: 0.85rem;">Longitude (خط الطول)</label>
                                <input type="number" step="0.0000001" name="lands[${landCounter}][longitude]"
                                       class="form-control form-control-sm land-longitude" placeholder="35.9106" value="${longitude}">
                            </div>
                        </div>

                        ${isExisting ?
                            `<div class="alert alert-success py-2 px-2 mb-0" style="font-size: 0.75rem;">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Existing land - modify as needed
                                </div>` :
                            `<div class="alert alert-info py-2 px-2 mb-0" style="font-size: 0.75rem;">
                                    <i class="bi bi-info-circle me-1"></i>
                                    New land - will be created on save
                                </div>`
                        }
                    </div>
                `;

                // Add event listeners
                const toggleBtn = landCard.querySelector('.btn-toggle-land');
                const removeBtn = landCard.querySelector('.btn-remove-land');
                const header = landCard.querySelector('.land-card-header');
                const mapUrlInput = landCard.querySelector('.land-map-url');
                const latInput = landCard.querySelector('.land-latitude');
                const lngInput = landCard.querySelector('.land-longitude');

                // Google Maps coordinate extraction
                mapUrlInput.addEventListener('blur', function() {
                    const url = this.value.trim();
                    if (url) {
                        extractCoordinatesForLand(url, latInput, lngInput);
                    }
                });

                // Toggle collapse
                const toggleCollapse = () => {
                    landCard.classList.toggle('collapsed');
                    const icon = toggleBtn.querySelector('i');
                    if (landCard.classList.contains('collapsed')) {
                        icon.className = 'bi bi-chevron-down';
                    } else {
                        icon.className = 'bi bi-chevron-up';
                    }
                };

                toggleBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    toggleCollapse();
                });

                header.addEventListener('click', (e) => {
                    if (e.target.closest('.btn-remove-land') || e.target.closest('.btn-toggle-land'))
                        return;
                    toggleCollapse();
                });

                // Remove land card
                removeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (confirm('Are you sure you want to remove this land?')) {
                        // If it's an existing land, mark it for deletion instead of removing the card
                        if (isExisting && landId) {
                            // Add a hidden input to mark for deletion
                            const deleteInput = document.createElement('input');
                            deleteInput.type = 'hidden';
                            deleteInput.name = 'delete_lands[]';
                            deleteInput.value = landId;
                            siteForm.appendChild(deleteInput);
                        }
                        landCard.remove();
                        updateLandCount();
                    }
                });

                return landCard;
            }

            // Function to extract coordinates for individual land cards
            function extractCoordinatesForLand(url, latInput, lngInput) {
                try {
                    let match = url.match(/place\/([-+]?\d+\.?\d*),([-+]?\d+\.?\d*)/);
                    if (!match) match = url.match(/@([-+]?\d+\.?\d*),([-+]?\d+\.?\d*)/);
                    if (!match) match = url.match(/[?&]q=([-+]?\d+\.?\d*),([-+]?\d+\.?\d*)/);
                    if (!match) match = url.match(/maps\?q=([-+]?\d+\.?\d*),([-+]?\d+\.?\d*)/);
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
                        latInput.value = lat;
                        lngInput.value = lng;
                    }
                } catch (error) {
                    console.error('Error extracting coordinates:', error);
                }
            }

            // Load existing lands
            @foreach ($site->lands as $land)
                const existingLand{{ $land->id }} = createLandCard({
                    id: {{ $land->id }},
                    directorate: '{{ addslashes($land->directorate) }}',
                    directorate_number: '{{ addslashes($land->directorate_number) }}',
                    village: '{{ addslashes($land->village ?? '') }}',
                    village_number: '{{ addslashes($land->village_number ?? '') }}',
                    basin: '{{ addslashes($land->basin) }}',
                    basin_number: '{{ addslashes($land->basin_number) }}',
                    neighborhood: '{{ addslashes($land->neighborhood ?? '') }}',
                    neighborhood_number: '{{ addslashes($land->neighborhood_number ?? '') }}',
                    plot_number: '{{ addslashes($land->plot_number) }}',
                    plot_key: '{{ addslashes($land->plot_key) }}',
                    area_m2: {{ $land->area_m2 }},
                    map_location: '{{ addslashes($land->map_location ?? '') }}',
                    latitude: {{ $land->latitude ?? 'null' }},
                    longitude: {{ $land->longitude ?? 'null' }}
                }, true);
                landsContainer.appendChild(existingLand{{ $land->id }});
            @endforeach

            // Add new land button
            addLandBtn.addEventListener('click', function() {
                const newLandCard = createLandCard();
                landsContainer.appendChild(newLandCard);
                updateLandCount();

                // Scroll to the new card
                setTimeout(() => {
                    newLandCard.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }, 100);
            });

            // Initialize count
            updateLandCount();
        });
    </script>
@endpush
