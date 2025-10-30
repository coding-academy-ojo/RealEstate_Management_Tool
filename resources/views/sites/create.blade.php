@extends('layouts.app')

@section('title', 'Create Site')

@push('styles')
    <style>
        /* Zoning Wrapper - Main Container */
        .zoning-wrapper {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            background: #ffffff;
            overflow: hidden;
        }

        /* Search Box */
        .zoning-search-box {
            padding: 12px 15px;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .zoning-search-box i {
            color: #6c757d;
            font-size: 1rem;
        }

        .zoning-search-box input {
            flex: 1;
            border: none;
            background: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.9rem;
            outline: none;
            border: 1px solid #dee2e6;
            transition: all 0.2s;
        }

        .zoning-search-box input:focus {
            border-color: #ff7900;
            box-shadow: 0 0 0 2px rgba(255, 121, 0, 0.1);
        }

        /* Selected Items Area */
        .zoning-selected-area {
            padding: 12px 15px;
            background: #fff9f5;
            border-bottom: 1px solid #dee2e6;
        }

        .selected-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .selected-header span {
            font-size: 0.85rem;
            font-weight: 600;
            color: #495057;
        }

        .btn-add-zoning {
            background: #ff7900;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s;
        }

        .btn-add-zoning:hover {
            background: #e66d00;
            transform: translateY(-1px);
        }

        .btn-add-zoning i {
            font-size: 0.9rem;
        }

        .selected-badges {
            min-height: 32px;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .selected-badges .badge-item {
            background: #ff7900;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            animation: fadeIn 0.2s;
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
            cursor: pointer;
            font-size: 1rem;
            opacity: 0.9;
            transition: opacity 0.2s;
        }

        .selected-badges .badge-item i:hover {
            opacity: 1;
        }

        .no-selection {
            color: #adb5bd;
            font-size: 0.85rem;
            font-style: italic;
        }

        /* Scrollable Area */
        .zoning-scroll-area {
            height: 200px;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 8px;
            background: #fafbfc;
        }

        .zoning-scroll-area::-webkit-scrollbar {
            width: 8px;
        }

        .zoning-scroll-area::-webkit-scrollbar-track {
            background: #f1f3f5;
        }

        .zoning-scroll-area::-webkit-scrollbar-thumb {
            background: #ff7900;
            border-radius: 10px;
        }

        .zoning-scroll-area::-webkit-scrollbar-thumb:hover {
            background: #e66d00;
        }

        /* Individual Zoning Option */
        .zoning-option {
            display: flex;
            align-items: center;
            padding: 10px 12px;
            margin-bottom: 6px;
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            width: 100%;
        }

        .zoning-option:hover {
            border-color: #ff7900;
            background: #fff9f5;
            transform: translateX(3px);
        }

        .zoning-option input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            width: 0;
            height: 0;
        }

        .checkbox-custom {
            width: 20px;
            height: 20px;
            border: 2px solid #dee2e6;
            border-radius: 5px;
            margin-right: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .zoning-option input:checked~.checkbox-custom {
            background: #ff7900;
            border-color: #ff7900;
        }

        .zoning-option input:checked~.checkbox-custom::after {
            content: "✓";
            color: white;
            font-size: 14px;
            font-weight: bold;
        }

        .option-text {
            flex: 1;
            font-size: 0.9rem;
            color: #495057;
            transition: all 0.2s;
        }

        .zoning-option:hover .option-text {
            color: #ff7900;
            font-weight: 500;
        }

        .zoning-option input:checked~.option-text {
            color: #ff7900;
            font-weight: 600;
        }

        /* No Results Message */
        .no-results-msg {
            display: none;
            text-align: center;
            padding: 40px 20px;
            color: #adb5bd;
            width: 100%;
        }

        .no-results-msg i {
            font-size: 2rem;
            margin-bottom: 10px;
            display: block;
        }

        .no-results-msg p {
            margin: 0;
            font-size: 0.9rem;
        }
    </style>
@endpush

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('sites.index') }}">Sites</a></li>
    <li class="breadcrumb-item active">Create</li>
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

        /* Image Upload Styles */
        .target-btn {
            transition: all 0.2s;
        }

        .target-btn.active {
            background: #ff7900 !important;
            color: white !important;
            border-color: #ff7900 !important;
        }

        .image-preview-section {
            display: none;
            margin-bottom: 20px;
        }

        .image-preview-section.active {
            display: block;
        }

        .image-preview-item {
            position: relative;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            background: #f8f9fa;
            transition: all 0.2s;
        }

        .image-preview-item:hover {
            border-color: #ff7900;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 121, 0, 0.2);
        }

        .image-preview-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .image-preview-remove {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            z-index: 10;
        }

        .image-preview-remove:hover {
            background: #dc3545;
            transform: scale(1.1);
        }

        .image-preview-name {
            padding: 8px;
            background: white;
            font-size: 0.85rem;
            color: #495057;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .image-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            background: rgba(255, 121, 0, 0.95);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .image-badge.land-badge {
            background: rgba(13, 110, 253, 0.95);
        }
    </style>
    <form action="{{ route('sites.store') }}" method="POST" id="siteForm" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <!-- Left Side: Site Form (Full Width) -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h4 class="mb-0">
                            <i class="bi bi-geo-alt-fill me-2 text-orange"></i>
                            Create New Site
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <!-- Global Error Display -->
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h6 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-2"></i>Please fix the following errors:</h6>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="name" class="form-label fw-bold">
                                    Site Name (اسم الموقع) <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Hidden field for cluster_no (auto-generated) -->
                        <input type="hidden" name="cluster_no" id="cluster_no" value="{{ old('cluster_no', '1') }}">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="governorate" class="form-label fw-bold">
                                    Governorate (المحافظة) <span class="text-danger">*</span>
                                </label>
                                <select name="governorate" id="governorate"
                                    class="form-select @error('governorate') is-invalid @enderror" required>
                                    <option value="">-- Select Governorate --</option>

                                    <optgroup label="Region 1 - Capital">
                                        <option value="AM" {{ old('governorate') == 'AM' ? 'selected' : '' }}>Amman
                                            (عمّان)</option>
                                    </optgroup>

                                    <optgroup label="Region 2 - North">
                                        <option value="IR" {{ old('governorate') == 'IR' ? 'selected' : '' }}>Irbid
                                            (إربد)</option>
                                        <option value="MF" {{ old('governorate') == 'MF' ? 'selected' : '' }}>Mafraq
                                            (المفرق)</option>
                                        <option value="AJ" {{ old('governorate') == 'AJ' ? 'selected' : '' }}>Ajloun
                                            (عجلون)</option>
                                        <option value="JA" {{ old('governorate') == 'JA' ? 'selected' : '' }}>Jerash
                                            (جرش)</option>
                                    </optgroup>

                                    <optgroup label="Region 3 - Middle">
                                        <option value="BA" {{ old('governorate') == 'BA' ? 'selected' : '' }}>Balqa
                                            (البلقاء)</option>
                                        <option value="ZA" {{ old('governorate') == 'ZA' ? 'selected' : '' }}>Zarqa
                                            (الزرقاء)</option>
                                        <option value="MA" {{ old('governorate') == 'MA' ? 'selected' : '' }}>Madaba
                                            (مادبا)</option>
                                    </optgroup>

                                    <optgroup label="Region 4 - South">
                                        <option value="AQ" {{ old('governorate') == 'AQ' ? 'selected' : '' }}>Aqaba
                                            (العقبة)</option>
                                        <option value="KA" {{ old('governorate') == 'KA' ? 'selected' : '' }}>Karak
                                            (الكرك)</option>
                                        <option value="TF" {{ old('governorate') == 'TF' ? 'selected' : '' }}>Tafileh
                                            (الطفيلة)</option>
                                        <option value="MN" {{ old('governorate') == 'MN' ? 'selected' : '' }}>Ma'an
                                            (معان)</option>
                                    </optgroup>
                                </select>
                                @error('governorate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Region and cluster will be auto-set based on governorate</small>
                            </div>
                        </div>

                        <!-- Site Area Input -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="area_m2" class="form-label fw-bold">
                                    Site Area (مساحة الموقع) m² <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="area_m2" id="area_m2" step="0.01"
                                    class="form-control @error('area_m2') is-invalid @enderror"
                                    value="{{ old('area_m2') }}" required>
                                @error('area_m2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label fw-bold">Notes (ملاحظات)</label>
                            <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Other Documents Upload -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                Other Documents (مستندات أخرى)
                            </label>

                            <div id="documents-container">
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

                            <button type="button" id="add-document-btn" class="btn btn-sm btn-outline-orange" disabled>
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
                            <a href="{{ route('sites.index') }}" class="btn btn-light">
                                <i class="bi bi-x-circle me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-orange">
                                <i class="bi bi-check-circle me-1"></i> Create Site
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Lands Section -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm lands-sidebar">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-map-fill me-2 text-orange"></i>
                            Lands
                        </h5>
                        <span class="badge bg-orange" id="landCount">0 Lands</span>
                    </div>
                    <div class="card-body p-3">
                        <!-- Lands Container -->
                        <div id="landsContainer">
                            <div class="text-center text-muted py-5" id="emptyLandsMessage">
                                <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="mt-3">No lands added yet</p>
                                <small>Click "Add Land" to start</small>
                            </div>
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

        <!-- Images Upload Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-images me-2 text-orange"></i>
                            Images Gallery
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <!-- Target Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Upload images for:</label>
                            <div class="d-flex gap-2 flex-wrap" id="imageTargetSelector">
                                <button type="button" class="btn btn-outline-orange target-btn active" data-target="site">
                                    <i class="bi bi-building me-1"></i> Site
                                </button>
                                <!-- Land buttons will be added dynamically -->
                            </div>
                        </div>

                        <!-- Image Upload Area -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Choose Images</label>
                            <input type="file" class="form-control" id="imageUploadInput"
                                   accept="image/jpeg,image/png,image/jpg" multiple>
                            <small class="text-muted">You can select multiple images (JPG, PNG). Max 5MB each.</small>
                        </div>

                        <!-- All Image Previews (No Sections) -->
                        <div class="mb-3">
                            <h6 class="text-muted mb-3">
                                <i class="bi bi-images me-1"></i> All Images
                            </h6>
                            <div class="row g-3" id="allImagePreviews">
                                <div class="col-12 text-center text-muted py-4" id="emptyAllMessage">
                                    <i class="bi bi-image" style="font-size: 2rem; opacity: 0.3;"></i>
                                    <p class="mt-2 mb-0">No images uploaded yet</p>
                                </div>
                            </div>
                        </div>
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
            const governorateSelect = document.getElementById('governorate');
            const clusterNoInput = document.getElementById('cluster_no');

            // Governorate to Region mapping
            const governorateToRegion = {
                'AM': 1, // Capital
                'IR': 2,
                'MF': 2,
                'AJ': 2,
                'JA': 2, // North
                'BA': 3,
                'ZA': 3,
                'MA': 3, // Middle
                'AQ': 4,
                'KA': 4,
                'TF': 4,
                'MN': 4 // South
            };

            // Function to get next cluster number for region
            async function getNextClusterNumber(governorate) {
                if (!governorate) {
                    clusterNoInput.value = '';
                    return;
                }

                const region = governorateToRegion[governorate];

                try {
                    // Fetch sites for this governorate to determine next cluster
                    const response = await fetch(`/api/sites/next-cluster/${governorate}`);
                    if (response.ok) {
                        const data = await response.json();
                        clusterNoInput.value = data.next_cluster;
                    } else {
                        // Fallback: just set to 1
                        clusterNoInput.value = '1';
                    }
                } catch (error) {
                    // Fallback: just set to 1
                    clusterNoInput.value = '1';
                }
            }

            // Update cluster number when governorate changes
            governorateSelect.addEventListener('change', function() {
                getNextClusterNumber(this.value);
            });

            // Initialize on page load if governorate is already selected
            if (governorateSelect.value) {
                getNextClusterNumber(governorateSelect.value);
            }

            // Handle new zoning status creation (for land cards)
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
                        // Add new checkbox to ALL land card zoning lists
                        const allLandScrollAreas = document.querySelectorAll('.land-zoning-scroll');
                        allLandScrollAreas.forEach((scrollArea) => {
                            const landCard = scrollArea.closest('.land-card');
                            const landId = landCard.getAttribute('data-land-id');

                            const newOption = document.createElement('label');
                            newOption.className = 'zoning-option zoning-item';
                            newOption.setAttribute('data-name', data.name_ar.toLowerCase());
                            newOption.innerHTML = `
                                <input type="checkbox" class="land-zoning-checkbox"
                                    name="lands[${landId}][zoning_statuses][]"
                                    value="${data.id}"
                                    id="land_${landId}_zoning_${data.id}">
                                <span class="checkbox-custom"></span>
                                <span class="option-text">${data.name_ar}</span>
                            `;

                            const noResultsNode = scrollArea.querySelector('.land-no-results');
                            if (noResultsNode) {
                                scrollArea.insertBefore(newOption, noResultsNode);
                            } else {
                                scrollArea.appendChild(newOption);
                            }

                            const newCheckbox = newOption.querySelector('.land-zoning-checkbox');
                            newCheckbox.addEventListener('change', function() {
                                updateLandBadges(landCard);
                            });
                        });

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

            // ============ Dynamic Land Cards ============
            let landCounter = 0;
            const landsContainer = document.getElementById('landsContainer');
            const addLandBtn = document.getElementById('addLandBtn');
            const emptyLandsMessage = document.getElementById('emptyLandsMessage');
            const landCountBadge = document.getElementById('landCount');

            // Helper function to update badges for a specific land card
            function updateLandBadges(landCard) {
                const badgeContainer = landCard.querySelector('.land-badge-container');
                const checkedBoxes = landCard.querySelectorAll('.land-zoning-checkbox:checked');

                badgeContainer.innerHTML = '';

                if (checkedBoxes.length === 0) {
                    badgeContainer.innerHTML = '<em class="no-selection">No items selected</em>';
                    return;
                }

                checkedBoxes.forEach(checkbox => {
                    const label = checkbox.closest('.zoning-option').querySelector('.option-text').textContent.trim();
                    const badge = document.createElement('span');
                    badge.className = 'badge-item';
                    badge.innerHTML = `
                        ${label}
                        <i class="bi bi-x-circle" data-checkbox-id="${checkbox.id}"></i>
                    `;
                    badgeContainer.appendChild(badge);

                    badge.querySelector('i').addEventListener('click', function(e) {
                        e.stopPropagation();
                        checkbox.checked = false;
                        updateLandBadges(landCard);
                    });
                });
            }

            // Function to setup zoning for individual land card
            function setupLandZoning(landCard, landId) {
                const checkboxes = landCard.querySelectorAll('.land-zoning-checkbox');
                const searchInput = landCard.querySelector('.land-zoning-search');
                const noResults = landCard.querySelector('.land-no-results');
                const zoningItems = landCard.querySelectorAll('.zoning-item');

                // Checkbox change event
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        updateLandBadges(landCard);
                    });
                });

                // Search functionality
                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        const searchTerm = this.value.toLowerCase().trim();
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
                }

                // Initialize badges
                updateLandBadges(landCard);
            }

            function updateLandCount() {
                const activeLands = document.querySelectorAll('.land-card').length;
                landCountBadge.textContent = `${activeLands} Land${activeLands !== 1 ? 's' : ''}`;

                if (activeLands === 0) {
                    emptyLandsMessage.style.display = 'block';
                } else {
                    emptyLandsMessage.style.display = 'none';
                }
            }

            function createLandCard() {
                landCounter++;
                const landCard = document.createElement('div');
                landCard.className = 'land-card';
                landCard.setAttribute('data-land-id', landCounter);
                landCard.setAttribute('data-form-key', landCounter);

                landCard.innerHTML = `
                    <div class="land-card-header">
                        <div class="land-card-title">
                            <i class="bi bi-map"></i>
                            <span>Land #${landCounter}</span>
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
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold" style="font-size: 0.85rem;">Directorate (المديرية) <span class="text-danger">*</span></label>
                                <input type="text" name="lands[${landCounter}][directorate]" class="form-control form-control-sm" placeholder="e.g., Amman" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold" style="font-size: 0.85rem;">Directorate Number (رقم المديرية) <span class="text-danger">*</span></label>
                                <input type="text" name="lands[${landCounter}][directorate_number]" class="form-control form-control-sm" placeholder="e.g., 123" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold" style="font-size: 0.85rem;">Village (القرية)</label>
                                <input type="text" name="lands[${landCounter}][village]" class="form-control form-control-sm" placeholder="Optional">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold" style="font-size: 0.85rem;">Village Number (رقم القرية)</label>
                                <input type="text" name="lands[${landCounter}][village_number]" class="form-control form-control-sm" placeholder="Optional">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold" style="font-size: 0.85rem;">Basin (الحوض) <span class="text-danger">*</span></label>
                                <input type="text" name="lands[${landCounter}][basin]" class="form-control form-control-sm" placeholder="e.g., Basin 5" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold" style="font-size: 0.85rem;">Basin Number (رقم الحوض) <span class="text-danger">*</span></label>
                                <input type="text" name="lands[${landCounter}][basin_number]" class="form-control form-control-sm" placeholder="e.g., 10" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold" style="font-size: 0.85rem;">Neighborhood (الحي)</label>
                                <input type="text" name="lands[${landCounter}][neighborhood]" class="form-control form-control-sm" placeholder="Optional">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold" style="font-size: 0.85rem;">Neighborhood Number (رقم الحي)</label>
                                <input type="text" name="lands[${landCounter}][neighborhood_number]" class="form-control form-control-sm" placeholder="Optional">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold" style="font-size: 0.85rem;">Plot Number (رقم القطعة) <span class="text-danger">*</span></label>
                                <input type="text" name="lands[${landCounter}][plot_number]" class="form-control form-control-sm" placeholder="e.g., 123" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold" style="font-size: 0.85rem;">Plot Key (مفتاح القطعة) <span class="text-danger">*</span></label>
                                <input type="text" name="lands[${landCounter}][plot_key]" class="form-control form-control-sm" placeholder="e.g., A-123" required>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Area (المساحة) m² <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="lands[${landCounter}][area_m2]" class="form-control form-control-sm" placeholder="e.g., 500" required min="0">
                            <small class="text-muted" style="font-size: 0.75rem;">Site area will be auto-updated</small>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Google Maps URL (رابط الخريطة)</label>
                            <input type="url" name="lands[${landCounter}][map_location]" class="form-control form-control-sm land-map-url" placeholder="Paste Google Maps URL">
                            <small class="text-muted" style="font-size: 0.75rem;">Coordinates will be auto-extracted</small>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-2">
                                <label class="form-label fw-bold" style="font-size: 0.85rem;">Latitude (خط العرض)</label>
                                <input type="number" step="0.0000001" name="lands[${landCounter}][latitude]" class="form-control form-control-sm land-latitude" placeholder="31.9539">
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label fw-bold" style="font-size: 0.85rem;">Longitude (خط الطول)</label>
                                <input type="number" step="0.0000001" name="lands[${landCounter}][longitude]" class="form-control form-control-sm land-longitude" placeholder="35.9106">
                            </div>
                        </div>

                        <!-- Zoning Status Section for Land -->
                        <div class="mb-2">
                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Zoning Status (التنظيم)</label>
                            <div class="zoning-wrapper">
                                <div class="zoning-search-box">
                                    <i class="bi bi-search"></i>
                                    <input type="text" class="land-zoning-search" placeholder="Search zoning...">
                                </div>
                                <div class="zoning-selected-area">
                                    <div class="selected-header">
                                        <span><i class="bi bi-check-circle me-1"></i> Selected</span>
                                        <button type="button" class="btn-add-zoning" data-bs-toggle="modal" data-bs-target="#addZoningModal">
                                            <i class="bi bi-plus-circle"></i> Add
                                        </button>
                                    </div>
                                    <div class="land-badge-container selected-badges">
                                        <em class="no-selection">No items selected</em>
                                    </div>
                                </div>
                                <div class="zoning-scroll-area land-zoning-scroll" style="height: 150px !important;">
                                    @php
                                        $noneOption = $zoningStatuses->firstWhere('name_ar', 'لا يوجد');
                                        $otherOptions = $zoningStatuses->where('name_ar', '!=', 'لا يوجد');
                                    @endphp
                                    @if ($noneOption)
                                        <label class="zoning-option zoning-item" data-name="{{ strtolower($noneOption->name_ar) }}">
                                            <input type="checkbox" class="land-zoning-checkbox" name="lands[${landCounter}][zoning_statuses][]"
                                                value="{{ $noneOption->id }}" id="land_${landCounter}_zoning_{{ $noneOption->id }}">
                                            <span class="checkbox-custom"></span>
                                            <span class="option-text">{{ $noneOption->name_ar }}</span>
                                        </label>
                                    @endif
                                    @foreach ($otherOptions as $zoning)
                                        <label class="zoning-option zoning-item" data-name="{{ strtolower($zoning->name_ar) }}">
                                            <input type="checkbox" class="land-zoning-checkbox" name="lands[${landCounter}][zoning_statuses][]"
                                                value="{{ $zoning->id }}" id="land_${landCounter}_zoning_{{ $zoning->id }}">
                                            <span class="checkbox-custom"></span>
                                            <span class="option-text">{{ $zoning->name_ar }}</span>
                                        </label>
                                    @endforeach
                                    <div class="no-results-msg land-no-results">
                                        <i class="bi bi-search"></i>
                                        <p>No results found</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Documents Section for Land -->
                        <div class="mt-3">
                            <label class="form-label fw-bold mb-2" style="font-size: 0.85rem;">
                                <i class="bi bi-file-earmark-text text-orange me-1"></i>Documents (Optional)
                            </label>

                            <div class="mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <label class="mb-0" style="font-size: 0.75rem; min-width: 100px;">
                                        <i class="bi bi-file-pdf text-danger me-1"></i>سند الملكية
                                    </label>
                                    <input type="file" name="lands[${landCounter}][ownership_doc]"
                                        class="form-control form-control-sm" accept=".jpg,.jpeg,.pdf" style="max-width: 250px;">
                                    <small class="text-muted" style="font-size: 0.7rem;">JPG/PDF (Max: 10MB)</small>
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <label class="mb-0" style="font-size: 0.75rem; min-width: 100px;">
                                        <i class="bi bi-map text-info me-1"></i>مخطط الموقع
                                    </label>
                                    <input type="file" name="lands[${landCounter}][site_plan]"
                                        class="form-control form-control-sm" accept=".jpg,.jpeg,.pdf" style="max-width: 250px;">
                                    <small class="text-muted" style="font-size: 0.7rem;">JPG/PDF (Max: 10MB)</small>
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <label class="mb-0" style="font-size: 0.75rem; min-width: 100px;">
                                        <i class="bi bi-diagram-3 text-success me-1"></i>مخطط تنظيمي
                                    </label>
                                    <input type="file" name="lands[${landCounter}][zoning_plan]"
                                        class="form-control form-control-sm" accept=".jpg,.jpeg,.pdf" style="max-width: 250px;">
                                    <small class="text-muted" style="font-size: 0.7rem;">JPG/PDF (Max: 10MB)</small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info py-2 px-2 mb-0" style="font-size: 0.75rem;">
                            <i class="bi bi-info-circle me-1"></i>
                            New land - will be created on save
                        </div>
                    </div>
                `;

                // Setup land-specific zoning functionality
                setupLandZoning(landCard, landCounter);

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

                        if (lat >= 29 && lat <= 34 && lng >= 34 && lng <= 40) {
                            latInput.value = lat;
                            lngInput.value = lng;
                        } else {
                            latInput.value = lat;
                            lngInput.value = lng;
                        }
                    }
                } catch (error) {
                    console.error('Error extracting coordinates:', error);
                }
            }

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

            // ============ Image Upload Management ============
            setupImageUpload();
        });

        function setupImageUpload() {
            const imageTargetSelector = document.getElementById('imageTargetSelector');
            const imageUploadInput = document.getElementById('imageUploadInput');
            const allImagePreviews = document.getElementById('allImagePreviews');
            const emptyAllMessage = document.getElementById('emptyAllMessage');

            let currentTarget = 'site';
            let allImages = [];

            // Update target buttons when lands are added/removed
            function updateImageTargetButtons() {
                const landCards = document.querySelectorAll('.land-card');
                const existingButtons = imageTargetSelector.querySelectorAll('[data-target^="land-"]');

                // Remove buttons for deleted lands
                existingButtons.forEach(btn => {
                    const landId = btn.getAttribute('data-target').replace('land-', '');
                    const landExists = Array.from(landCards).some(card =>
                        card.getAttribute('data-land-id') === landId
                    );
                    if (!landExists) {
                        btn.remove();
                        // Remove images for deleted land
                        allImages = allImages.filter(img => img.target !== `land-${landId}`);
                        renderAllImages();
                    }
                });

                // Add buttons for new lands
                landCards.forEach((card, index) => {
                    const landId = card.getAttribute('data-land-id');
                    const targetKey = `land-${landId}`;

                    const existingBtn = imageTargetSelector.querySelector(`[data-target="${targetKey}"]`);
                    if (!existingBtn) {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'btn btn-outline-orange target-btn';
                        btn.setAttribute('data-target', targetKey);
                        btn.innerHTML = `<i class="bi bi-map me-1"></i> Land ${index + 1}`;
                        imageTargetSelector.appendChild(btn);
                    }
                });

                // Update land numbers in existing buttons
                landCards.forEach((card, index) => {
                    const landId = card.getAttribute('data-land-id');
                    const btn = imageTargetSelector.querySelector(`[data-target="land-${landId}"]`);
                    if (btn) {
                        btn.innerHTML = `<i class="bi bi-map me-1"></i> Land ${index + 1}`;
                    }

                    // Update badges in previews
                    allImages.forEach(img => {
                        if (img.target === `land-${landId}`) {
                            img.displayName = `Land ${index + 1}`;
                        }
                    });
                });

                renderAllImages();
            }

            // Target button click handler
            imageTargetSelector.addEventListener('click', function(e) {
                const btn = e.target.closest('.target-btn');
                if (!btn) return;

                const target = btn.getAttribute('data-target');
                if (!target) return;

                currentTarget = target;

                // Update active button
                imageTargetSelector.querySelectorAll('.target-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            });

            // Handle file selection
            imageUploadInput.addEventListener('change', function(e) {
                const files = Array.from(e.target.files);
                if (files.length === 0) return;

                files.forEach(file => {
                    if (!file.type.match('image/(jpeg|jpg|png)')) {
                        alert('Only JPG and PNG images are allowed');
                        return;
                    }

                    if (file.size > 5 * 1024 * 1024) {
                        alert(`File ${file.name} is too large. Max 5MB`);
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const landCards = document.querySelectorAll('.land-card');
                        let displayName = 'Site';

                        if (currentTarget.startsWith('land-')) {
                            const landId = currentTarget.replace('land-', '');
                            const landIndex = Array.from(landCards).findIndex(card =>
                                card.getAttribute('data-land-id') === landId
                            );
                            displayName = `Land ${landIndex + 1}`;
                        }

                        allImages.push({
                            file: file,
                            dataUrl: event.target.result,
                            name: file.name,
                            target: currentTarget,
                            displayName: displayName
                        });
                        renderAllImages();
                    };
                    reader.readAsDataURL(file);
                });

                // Clear input
                e.target.value = '';
            });

            // Render all images
            function renderAllImages() {
                if (allImages.length === 0) {
                    emptyAllMessage.style.display = 'block';
                    return;
                }

                emptyAllMessage.style.display = 'none';

                // Clear existing previews
                const existingPreviews = allImagePreviews.querySelectorAll('.image-preview-col');
                existingPreviews.forEach(item => item.remove());

                // Render all images
                allImages.forEach((img, index) => {
                    const col = document.createElement('div');
                    col.className = 'col-md-3 col-sm-4 col-6 image-preview-col';
                    const badgeClass = img.target === 'site' ? '' : 'land-badge';
                    const badgeIcon = img.target === 'site' ? 'building' : 'map';

                    col.innerHTML = `
                        <div class="image-preview-item">
                            <div class="image-badge ${badgeClass}">
                                <i class="bi bi-${badgeIcon}"></i>
                                ${img.displayName}
                            </div>
                            <img src="${img.dataUrl}" alt="${img.name}">
                            <button type="button" class="image-preview-remove" data-index="${index}">
                                <i class="bi bi-x"></i>
                            </button>
                            <div class="image-preview-name" title="${img.name}">
                                ${img.name}
                            </div>
                        </div>
                    `;
                    allImagePreviews.appendChild(col);
                });

                // Add remove handlers
                allImagePreviews.querySelectorAll('.image-preview-remove').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const index = parseInt(this.getAttribute('data-index'));
                        allImages.splice(index, 1);
                        renderAllImages();
                    });
                });
            }

            // Form submission - append images as FormData
            const siteForm = document.getElementById('siteForm');
            const originalAction = siteForm.action;

            siteForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(siteForm);

                // Group images by target
                const siteImages = allImages.filter(img => img.target === 'site');
                const landImagesMap = {};

                // Map each land card to its form key (matches lands[...] index)
                const landCards = document.querySelectorAll('.land-card');
                const landIdToFormKey = {};
                landCards.forEach(card => {
                    const targetKey = card.getAttribute('data-land-id');
                    const formKey = card.getAttribute('data-form-key') ?? targetKey;
                    if (targetKey && formKey) {
                        landIdToFormKey[targetKey] = formKey;
                    }
                });

                allImages.forEach(img => {
                    if (img.target.startsWith('land-')) {
                        const tempId = img.target.replace('land-', '');
                        const formKey = landIdToFormKey[tempId];
                        if (formKey !== undefined) {
                            if (!landImagesMap[formKey]) {
                                landImagesMap[formKey] = [];
                            }
                            landImagesMap[formKey].push(img);
                        }
                    }
                });

                // Add site images
                siteImages.forEach((img) => {
                    formData.append('site_images[]', img.file);
                });

                // Add land images by index
                Object.keys(landImagesMap).forEach(landKey => {
                    landImagesMap[landKey].forEach((img) => {
                        formData.append(`land_images[${landKey}][]`, img.file);
                    });
                });

                // Debug logging
                console.log('Site images:', siteImages.length);
                console.log('Land images map:', landImagesMap);
                console.log('Land ID to Form key mapping:', landIdToFormKey);

                // Debug FormData contents
                console.log('=== FormData Contents ===');
                for (let pair of formData.entries()) {
                    if (pair[0].includes('image')) {
                        console.log(pair[0] + ':', pair[1] instanceof File ? pair[1].name : pair[1]);
                    }
                }

                // Submit form normally (let Laravel handle redirect)
                fetch(originalAction, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                        return;
                    }
                    return response.json().then(data => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else if (response.ok) {
                            window.location.href = '{{ route("sites.index") }}';
                        } else {
                            throw new Error(data.message || 'Error submitting form');
                        }
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Try regular form submission as fallback
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = originalAction;
                    form.style.display = 'none';

                    // Add CSRF token
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = document.querySelector('input[name="_token"]').value;
                    form.appendChild(csrfInput);

                    // Add all form data
                    for (let pair of formData.entries()) {
                        if (pair[1] instanceof File) {
                            // Handle files
                            const fileInput = document.createElement('input');
                            fileInput.type = 'file';
                            fileInput.name = pair[0];
                            fileInput.files = new DataTransfer().files;
                        } else {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = pair[0];
                            input.value = pair[1];
                            form.appendChild(input);
                        }
                    }

                    document.body.appendChild(form);
                    form.submit();
                });

                return false;
            });

            // Watch for land additions/removals
            const observer = new MutationObserver(function(mutations) {
                updateImageTargetButtons();
            });

            const landsContainer = document.getElementById('landsContainer');
            if (landsContainer) {
                observer.observe(landsContainer, {
                    childList: true,
                    subtree: false
                });
            }

            // Initial update
            updateImageTargetButtons();
        }

        // Other Documents Management
        document.addEventListener('DOMContentLoaded', function() {
            const documentsContainer = document.getElementById('documents-container');
            const addDocumentBtn = document.getElementById('add-document-btn');
            let documentCount = 0;

            // Function to check if a file is selected
            function checkLastDocumentFile() {
                const allDocItems = documentsContainer.querySelectorAll('.document-item');
                const lastDocItem = allDocItems[allDocItems.length - 1];
                const lastFileInput = lastDocItem.querySelector('.document-file');

                if (lastFileInput && lastFileInput.files.length > 0) {
                    addDocumentBtn.disabled = false;
                } else {
                    addDocumentBtn.disabled = true;
                }
            }

            // Function to update remove buttons
            function updateRemoveButtons() {
                const allDocItems = documentsContainer.querySelectorAll('.document-item');
                allDocItems.forEach((item, index) => {
                    const removeBtn = item.querySelector('.remove-doc');
                    removeBtn.disabled = allDocItems.length === 1;
                });
            }

            // Add change event to initial file input
            documentsContainer.addEventListener('change', function(e) {
                if (e.target.classList.contains('document-file')) {
                    checkLastDocumentFile();
                }
            });

            // Add document button click
            addDocumentBtn.addEventListener('click', function() {
                documentCount++;
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

                documentsContainer.appendChild(newDocItem);
                addDocumentBtn.disabled = true;
                updateRemoveButtons();

                // Scroll to new document
                setTimeout(() => {
                    newDocItem.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }, 100);
            });

            // Remove document
            documentsContainer.addEventListener('click', function(e) {
                if (e.target.closest('.remove-doc')) {
                    const docItem = e.target.closest('.document-item');
                    docItem.remove();
                    checkLastDocumentFile();
                    updateRemoveButtons();
                }
            });

            // Initial check
            updateRemoveButtons();
        });
    </script>
@endpush
