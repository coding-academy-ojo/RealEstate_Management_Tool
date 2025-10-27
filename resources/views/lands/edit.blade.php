@extends('layouts.app')@extends('layouts.app')



@section('title', 'Edit Land')@section('title', 'Edit Land')



@section('breadcrumbs')@section('breadcrumbs')

    <li class="breadcrumb-item"><a href="{{ route('lands.index') }}">Lands</a></li>    <li class="breadcrumb-item"><a href="{{ route('lands.index') }}">Lands</a></li>

    <li class="breadcrumb-item"><a href="{{ route('lands.show', $land) }}">{{ $land->plot_key }}</a></li>    <li class="breadcrumb-item"><a href="{{ route('lands.show', $land) }}">{{ $land->plot_key }}</a></li>

    <li class="breadcrumb-item active">Edit</li>    <li class="breadcrumb-item active">Edit</li>

@endsection@endsection



@section('content')@section('content')

    <style>    <style>

        /* Zoning Wrapper - Main Container */        /* Zoning Wrapper - Main Container */

        .zoning-wrapper {        .zoning-wrapper {

            border: 1px solid #dee2e6 !important;            border: 1px solid #dee2e6 !important;

            border-radius: 10px !important;            border-radius: 10px !important;

            background: #ffffff !important;            background: #ffffff !important;

            overflow: hidden !important;            overflow: hidden !important;

        }        }



        .zoning-search-box {        .zoning-search-box {

            padding: 12px 15px !important;            padding: 12px 15px !important;

            background: #f8f9fa !important;            background: #f8f9fa !important;

            border-bottom: 1px solid #dee2e6 !important;            border-bottom: 1px solid #dee2e6 !important;

            display: flex !important;            display: flex !important;

            align-items: center !important;            align-items: center !important;

            gap: 10px !important;            gap: 10px !important;

        }        }



        .zoning-search-box i {        .zoning-search-box i {

            color: #6c757d !important;            color: #6c757d !important;

            font-size: 1rem !important;            font-size: 1rem !important;

        }        }



        .zoning-search-box input {        .zoning-search-box input {

            flex: 1 !important;            flex: 1 !important;

            border: none !important;            border: none !important;

            background: white !important;            background: white !important;

            padding: 6px 12px !important;            padding: 6px 12px !important;

            border-radius: 6px !important;            border-radius: 6px !important;

            font-size: 0.9rem !important;            font-size: 0.9rem !important;

            outline: none !important;            outline: none !important;

            border: 1px solid #dee2e6 !important;            border: 1px solid #dee2e6 !important;

            transition: all 0.2s !important;            transition: all 0.2s !important;

        }        }



        .zoning-search-box input:focus {        .zoning-search-box input:focus {

            border-color: #ff7900 !important;            border-color: #ff7900 !important;

            box-shadow: 0 0 0 2px rgba(255, 121, 0, 0.1) !important;            box-shadow: 0 0 0 2px rgba(255, 121, 0, 0.1) !important;

        }        }



        .zoning-selected-area {        .zoning-selected-area {

            padding: 12px 15px !important;            padding: 12px 15px !important;

            background: #fff9f5 !important;            background: #fff9f5 !important;

            border-bottom: 1px solid #dee2e6 !important;            border-bottom: 1px solid #dee2e6 !important;

        }        }



        .selected-header {        .selected-header {

            display: flex !important;            display: flex !important;

            justify-content: space-between !important;            justify-content: space-between !important;

            align-items: center !important;            align-items: center !important;

            margin-bottom: 10px !important;            margin-bottom: 10px !important;

        }        }



        .selected-header span {        .selected-header span {

            font-size: 0.85rem !important;            font-size: 0.85rem !important;

            font-weight: 600 !important;            font-weight: 600 !important;

            color: #495057 !important;            color: #495057 !important;

        }        }



        .btn-add-zoning {        .btn-add-zoning {

            background: #ff7900 !important;            background: #ff7900 !important;

            color: white !important;            color: white !important;

            border: none !important;            border: none !important;

            padding: 5px 12px !important;            padding: 5px 12px !important;

            border-radius: 6px !important;            border-radius: 6px !important;

            font-size: 0.85rem !important;            font-size: 0.85rem !important;

            cursor: pointer !important;            cursor: pointer !important;

            display: flex !important;            display: flex !important;

            align-items: center !important;            align-items: center !important;

            gap: 5px !important;            gap: 5px !important;

            transition: all 0.2s !important;            transition: all 0.2s !important;

        }        }



        .btn-add-zoning:hover {        .btn-add-zoning:hover {

            background: #e66d00 !important;            background: #e66d00 !important;

            transform: translateY(-1px) !important;            transform: translateY(-1px) !important;

            color: white !important;            color: white !important;

        }        }



        .btn-add-zoning i {        .btn-add-zoning i {

            font-size: 0.9rem !important;            font-size: 0.9rem !important;

        }        }



        .selected-badges {        .selected-badges {

            min-height: 32px !important;            min-height: 32px !important;

            display: flex !important;            display: flex !important;

            flex-wrap: wrap !important;            flex-wrap: wrap !important;

            gap: 6px !important;            gap: 6px !important;

        }        }



        .selected-badges .badge-item {        .selected-badges .badge-item {

            background: #ff7900 !important;            background: #ff7900 !important;

            color: white !important;            color: white !important;

            padding: 5px 10px !important;            padding: 5px 10px !important;

            border-radius: 5px !important;            border-radius: 5px !important;

            font-size: 0.85rem !important;            font-size: 0.85rem !important;

            display: inline-flex !important;            display: inline-flex !important;

            align-items: center !important;            align-items: center !important;

            gap: 8px !important;            gap: 8px !important;

            animation: fadeIn 0.2s !important;            animation: fadeIn 0.2s !important;

        }        }



        @keyframes fadeIn {        @keyframes fadeIn {

            from {            from {

                opacity: 0;                opacity: 0;

                transform: scale(0.9);                transform: scale(0.9);

            }            }



            to {            to {

                opacity: 1;                opacity: 1;

                transform: scale(1);                transform: scale(1);

            }            }

        }        }



        .selected-badges .badge-item i {        .selected-badges .badge-item i {

            cursor: pointer !important;            cursor: pointer !important;

            font-size: 1rem !important;            font-size: 1rem !important;

            opacity: 0.9 !important;            opacity: 0.9 !important;

            transition: opacity 0.2s !important;            transition: opacity 0.2s !important;

        }        }



        .selected-badges .badge-item i:hover {        .selected-badges .badge-item i:hover {

            opacity: 1 !important;            opacity: 1 !important;

        }        }



        .no-selection {        .no-selection {

            color: #adb5bd !important;            color: #adb5bd !important;

            font-size: 0.85rem !important;            font-size: 0.85rem !important;

            font-style: italic !important;            font-style: italic !important;

        }        }



        .zoning-scroll-area {        .zoning-scroll-area {

            height: 200px !important;            height: 200px !important;

            overflow-y: auto !important;            overflow-y: auto !important;

            overflow-x: hidden !important;            overflow-x: hidden !important;

            padding: 8px !important;            padding: 8px !important;

            background: #fafbfc !important;            background: #fafbfc !important;

        }        }



        .zoning-scroll-area::-webkit-scrollbar {        .zoning-scroll-area::-webkit-scrollbar {

            width: 8px !important;            width: 8px !important;

        }        }



        .zoning-scroll-area::-webkit-scrollbar-track {        .zoning-scroll-area::-webkit-scrollbar-track {

            background: #f1f3f5 !important;            background: #f1f3f5 !important;

        }        }



        .zoning-scroll-area::-webkit-scrollbar-thumb {        .zoning-scroll-area::-webkit-scrollbar-thumb {

            background: #ff7900 !important;            background: #ff7900 !important;

            border-radius: 10px !important;            border-radius: 10px !important;

        }        }



        .zoning-scroll-area::-webkit-scrollbar-thumb:hover {        .zoning-scroll-area::-webkit-scrollbar-thumb:hover {

            background: #e66d00 !important;            background: #e66d00 !important;

        }        }



        .zoning-option {        .zoning-option {

            display: flex !important;            display: flex !important;

            align-items: center !important;            align-items: center !important;

            padding: 10px 12px !important;            padding: 10px 12px !important;

            margin-bottom: 6px !important;            margin-bottom: 6px !important;

            background: white !important;            background: white !important;

            border: 2px solid #e9ecef !important;            border: 2px solid #e9ecef !important;

            border-radius: 8px !important;            border-radius: 8px !important;

            cursor: pointer !important;            cursor: pointer !important;

            transition: all 0.2s !important;            transition: all 0.2s !important;

            position: relative !important;            position: relative !important;

            width: 100% !important;            width: 100% !important;

        }        }



        .zoning-option:hover {        .zoning-option:hover {

            border-color: #ff7900 !important;            border-color: #ff7900 !important;

            background: #fff9f5 !important;            background: #fff9f5 !important;

            transform: translateX(3px) !important;            transform: translateX(3px) !important;

        }        }



        .zoning-option input[type="checkbox"] {        .zoning-option input[type="checkbox"] {

            position: absolute !important;            position: absolute !important;

            opacity: 0 !important;            opacity: 0 !important;

            cursor: pointer !important;            cursor: pointer !important;

            width: 0 !important;            width: 0 !important;

            height: 0 !important;            height: 0 !important;

        }        }



        .checkbox-custom {        .checkbox-custom {

            width: 20px !important;            width: 20px !important;

            height: 20px !important;            height: 20px !important;

            border: 2px solid #dee2e6 !important;            border: 2px solid #dee2e6 !important;

            border-radius: 5px !important;            border-radius: 5px !important;

            margin-right: 12px !important;            margin-right: 12px !important;

            display: flex !important;            display: flex !important;

            align-items: center !important;            align-items: center !important;

            justify-content: center !important;            justify-content: center !important;

            transition: all 0.2s !important;            transition: all 0.2s !important;

            flex-shrink: 0 !important;            flex-shrink: 0 !important;

        }        }



        .zoning-option input:checked~.checkbox-custom {        .zoning-option input:checked~.checkbox-custom {

            background: #ff7900 !important;            background: #ff7900 !important;

            border-color: #ff7900 !important;            border-color: #ff7900 !important;

        }        }



        .zoning-option input:checked~.checkbox-custom::after {        .zoning-option input:checked~.checkbox-custom::after {

            content: "✓" !important;            content: "✓" !important;

            color: white !important;            color: white !important;

            font-size: 14px !important;            font-size: 14px !important;

            font-weight: bold !important;            font-weight: bold !important;

        }        }



        .option-text {        .option-text {

            flex: 1 !important;            flex: 1 !important;

            font-size: 0.9rem !important;            font-size: 0.9rem !important;

            color: #495057 !important;            color: #495057 !important;

            transition: all 0.2s !important;            transition: all 0.2s !important;

        }        }



        .zoning-option:hover .option-text {        .zoning-option:hover .option-text {

            color: #ff7900 !important;            color: #ff7900 !important;

            font-weight: 500 !important;            font-weight: 500 !important;

        }        }



        .zoning-option input:checked~.option-text {        .zoning-option input:checked~.option-text {

            color: #ff7900 !important;            color: #ff7900 !important;

            font-weight: 600 !important;            font-weight: 600 !important;

        }        }



        .no-results-msg {        .no-results-msg {

            display: none;            display: none;

            text-align: center !important;            text-align: center !important;

            padding: 40px 20px !important;            padding: 40px 20px !important;

            color: #6c757d !important;            color: #6c757d !important;

            width: 100% !important;            width: 100% !important;

            background: white !important;            background: white !important;

            border: 2px dashed #dee2e6 !important;            border: 2px dashed #dee2e6 !important;

            border-radius: 8px !important;            border-radius: 8px !important;

            margin-top: 10px !important;            margin-top: 10px !important;

        }        }



        .no-results-msg.show {        .no-results-msg.show {

            display: block !important;            display: block !important;

        }        }



        .no-results-msg i {        .no-results-msg i {

            font-size: 2.5rem !important;            font-size: 2.5rem !important;

            margin-bottom: 15px !important;            margin-bottom: 15px !important;

            display: block !important;            display: block !important;

            color: #ff7900 !important;            color: #ff7900 !important;

        }        }



        .no-results-msg p {        .no-results-msg p {

            margin: 0 !important;            margin: 0 !important;

            font-size: 1rem !important;            font-size: 1rem !important;

            font-weight: 500 !important;            font-weight: 500 !important;

            color: #495057 !important;            color: #495057 !important;

        }        }

    </style>    </style>

    <div class="row justify-content-center">    <div class="row justify-content-center">

        <div class="col-lg-8">        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white py-3">                <div class="card-header bg-white py-3">

                    <h4 class="mb-0">                    <h4 class="mb-0">

                        <i class="bi bi-pencil-square me-2 text-orange"></i>                        <i class="bi bi-pencil-fill me-2 text-orange"></i>

                        Edit Land Parcel: {{ $land->plot_key }}                        Edit Land Parcel

                    </h4>                    </h4>

                </div>                </div>

                <div class="card-body p-4">                <div class="card-body p-4">

                    <form action="{{ route('lands.update', $land) }}" method="POST" enctype="multipart/form-data">                    <form action="{{ route('lands.update', $land) }}" method="POST" enctype="multipart/form-data">

                        @csrf                        @csrf

                        @method('PUT')                        @method('PUT')



                        <!-- Hidden inputs for governorate and region -->                        <!-- Hidden inputs for governorate and region -->

                        <input type="hidden" name="governorate" id="governorate" value="{{ old('governorate', $land->governorate) }}">                        <input type="hidden" name="governorate" id="governorate"

                        <input type="hidden" name="region" id="region" value="{{ old('region', $land->region) }}">                            value="{{ old('governorate', $land->governorate) }}">

                        <input type="hidden" name="region" id="region" value="{{ old('region', $land->region) }}">

                        <!-- Governorate Selection -->

                        <div class="mb-4">                        <!-- Site Selection -->

                            <label for="governorate_filter" class="form-label fw-bold">                        <div class="mb-4">

                                Governorate <span class="text-danger">*</span>                            <label for="site_id" class="form-label fw-bold">

                            </label>                                Site <span class="text-danger">*</span>

                            <select id="governorate_filter" class="form-select" required>                            </label>

                                <option value="">-- Select Governorate --</option>                            <select name="site_id" id="site_id" class="form-select @error('site_id') is-invalid @enderror"

                                <optgroup label="Region 1 - Capital">                                required>

                                    <option value="AM" {{ old('governorate', $land->site->governorate ?? '') == 'AM' ? 'selected' : '' }}>Amman                                <option value="">-- Select Site --</option>

                                        (عمّان)</option>                                @foreach ($sites as $site)

                                </optgroup>                                    <option value="{{ $site->id }}"

                                <optgroup label="Region 2 - North">                                        {{ old('site_id', $land->site_id) == $site->id ? 'selected' : '' }}>

                                    <option value="IR" {{ old('governorate', $land->site->governorate ?? '') == 'IR' ? 'selected' : '' }}>Irbid                                        {{ $site->code }} - {{ $site->name }}

                                        (إربد)</option>                                    </option>

                                    <option value="MF" {{ old('governorate', $land->site->governorate ?? '') == 'MF' ? 'selected' : '' }}>Mafraq                                @endforeach

                                        (المفرق)</option>                            </select>

                                    <option value="AJ" {{ old('governorate', $land->site->governorate ?? '') == 'AJ' ? 'selected' : '' }}>Ajloun                            @error('site_id')

                                        (عجلون)</option>                                <div class="invalid-feedback">{{ $message }}</div>

                                    <option value="JA" {{ old('governorate', $land->site->governorate ?? '') == 'JA' ? 'selected' : '' }}>Jerash                            @enderror

                                        (جرش)</option>                        </div>

                                </optgroup>

                                <optgroup label="Region 3 - Middle">                        <hr class="my-4">

                                    <option value="BA" {{ old('governorate', $land->site->governorate ?? '') == 'BA' ? 'selected' : '' }}>Balqa

                                        (البلقاء)</option>                        <h5 class="mb-3 text-orange">

                                    <option value="ZA" {{ old('governorate', $land->site->governorate ?? '') == 'ZA' ? 'selected' : '' }}>Zarqa                            <i class="bi bi-info-circle me-2"></i>Complete Land Information

                                        (الزرقاء)</option>                        </h5>

                                    <option value="MA" {{ old('governorate', $land->site->governorate ?? '') == 'MA' ? 'selected' : '' }}>Madaba

                                        (مادبا)</option>                        <div class="row">

                                </optgroup>                            <div class="col-md-6 mb-3">

                                <optgroup label="Region 4 - South">                                <label for="directorate" class="form-label fw-bold">

                                    <option value="AQ" {{ old('governorate', $land->site->governorate ?? '') == 'AQ' ? 'selected' : '' }}>Aqaba                                    Directorate (المديرية) <span class="text-danger">*</span>

                                        (العقبة)</option>                                </label>

                                    <option value="KA" {{ old('governorate', $land->site->governorate ?? '') == 'KA' ? 'selected' : '' }}>Karak                                <input type="text" name="directorate" id="directorate"

                                        (الكرك)</option>                                    class="form-control @error('directorate') is-invalid @enderror"

                                    <option value="TF" {{ old('governorate', $land->site->governorate ?? '') == 'TF' ? 'selected' : '' }}>                                    value="{{ old('directorate', $land->directorate) }}" required>

                                        Tafileh (الطفيلة)</option>                                @error('directorate')

                                    <option value="MN" {{ old('governorate', $land->site->governorate ?? '') == 'MN' ? 'selected' : '' }}>Ma'an                                    <div class="invalid-feedback">{{ $message }}</div>

                                        (معان)</option>                                @enderror

                                </optgroup>                            </div>

                            </select>

                            <small class="text-muted">Select governorate to filter sites</small>                            <div class="col-md-6 mb-3">

                        </div>                                <label for="directorate_number" class="form-label fw-bold">

                                    Directorate Number (رقم المديرية) <span class="text-danger">*</span>

                        <!-- Site Selection -->                                </label>

                        <div class="mb-4">                                <input type="text" name="directorate_number" id="directorate_number"

                            <label for="site_id" class="form-label fw-bold">                                    class="form-control @error('directorate_number') is-invalid @enderror"

                                Site <span class="text-danger">*</span>                                    value="{{ old('directorate_number', $land->directorate_number) }}" required>

                            </label>                                @error('directorate_number')

                            <select name="site_id" id="site_id" class="form-select @error('site_id') is-invalid @enderror" required>                                    <div class="invalid-feedback">{{ $message }}</div>

                                <option value="">-- Select Site --</option>                                @enderror

                                <!-- Will be populated by JavaScript -->                            </div>

                            </select>                        </div>

                            <small class="text-muted">Select governorate first to filter sites</small>

                            @error('site_id')                        <div class="row">

                                <div class="invalid-feedback">{{ $message }}</div>                            <div class="col-md-6 mb-3">

                            @enderror                                <label for="village" class="form-label fw-bold">

                        </div>                                    Village (القرية)

                                </label>

                        <hr class="my-4">                                <input type="text" name="village" id="village"

                                    class="form-control @error('village') is-invalid @enderror"

                        <h5 class="mb-3 text-orange">                                    value="{{ old('village', $land->village) }}">

                            <i class="bi bi-info-circle me-2"></i>Complete Land Information                                @error('village')

                        </h5>                                    <div class="invalid-feedback">{{ $message }}</div>

                                @enderror

                        <div class="row">                            </div>

                            <div class="col-md-6 mb-3">

                                <label for="directorate" class="form-label fw-bold">                            <div class="col-md-6 mb-3">

                                    Directorate (المديرية) <span class="text-danger">*</span>                                <label for="village_number" class="form-label fw-bold">

                                </label>                                    Village Number (رقم القرية)

                                <input type="text" name="directorate" id="directorate"                                </label>

                                    class="form-control @error('directorate') is-invalid @enderror"                                <input type="text" name="village_number" id="village_number"

                                    value="{{ old('directorate', $land->directorate) }}" required>                                    class="form-control @error('village_number') is-invalid @enderror"

                                @error('directorate')                                    value="{{ old('village_number', $land->village_number) }}">

                                    <div class="invalid-feedback">{{ $message }}</div>                                @error('village_number')

                                @enderror                                    <div class="invalid-feedback">{{ $message }}</div>

                            </div>                                @enderror

                            </div>

                            <div class="col-md-6 mb-3">                        </div>

                                <label for="directorate_number" class="form-label fw-bold">

                                    Directorate Number (رقم المديرية) <span class="text-danger">*</span>                        <div class="row">

                                </label>                            <div class="col-md-6 mb-3">

                                <input type="text" name="directorate_number" id="directorate_number"                                <label for="basin" class="form-label fw-bold">

                                    class="form-control @error('directorate_number') is-invalid @enderror"                                    Basin (الحوض) <span class="text-danger">*</span>

                                    value="{{ old('directorate_number', $land->directorate_number) }}" required>                                </label>

                                @error('directorate_number')                                <input type="text" name="basin" id="basin"

                                    <div class="invalid-feedback">{{ $message }}</div>                                    class="form-control @error('basin') is-invalid @enderror"

                                @enderror                                    value="{{ old('basin', $land->basin) }}" required>

                            </div>                                @error('basin')

                        </div>                                    <div class="invalid-feedback">{{ $message }}</div>

                                @enderror

                        <div class="row">                            </div>

                            <div class="col-md-6 mb-3">

                                <label for="village" class="form-label fw-bold">                            <div class="col-md-6 mb-3">

                                    Village (القرية)                                <label for="basin_number" class="form-label fw-bold">

                                </label>                                    Basin Number (رقم الحوض) <span class="text-danger">*</span>

                                <input type="text" name="village" id="village"                                </label>

                                    class="form-control @error('village') is-invalid @enderror"                                <input type="text" name="basin_number" id="basin_number"

                                    value="{{ old('village', $land->village) }}">                                    class="form-control @error('basin_number') is-invalid @enderror"

                                @error('village')                                    value="{{ old('basin_number', $land->basin_number) }}" required>

                                    <div class="invalid-feedback">{{ $message }}</div>                                @error('basin_number')

                                @enderror                                    <div class="invalid-feedback">{{ $message }}</div>

                            </div>                                @enderror

                            </div>

                            <div class="col-md-6 mb-3">                        </div>

                                <label for="village_number" class="form-label fw-bold">

                                    Village Number (رقم القرية)                        <div class="row">

                                </label>                            <div class="col-md-6 mb-3">

                                <input type="text" name="village_number" id="village_number"                                <label for="neighborhood" class="form-label fw-bold">

                                    class="form-control @error('village_number') is-invalid @enderror"                                    Neighborhood (الحي)

                                    value="{{ old('village_number', $land->village_number) }}">                                </label>

                                @error('village_number')                                <input type="text" name="neighborhood" id="neighborhood"

                                    <div class="invalid-feedback">{{ $message }}</div>                                    class="form-control @error('neighborhood') is-invalid @enderror"

                                @enderror                                    value="{{ old('neighborhood', $land->neighborhood) }}">

                            </div>                                @error('neighborhood')

                        </div>                                    <div class="invalid-feedback">{{ $message }}</div>

                                @enderror

                        <div class="row">                            </div>

                            <div class="col-md-6 mb-3">

                                <label for="basin" class="form-label fw-bold">                            <div class="col-md-6 mb-3">

                                    Basin (الحوض) <span class="text-danger">*</span>                                <label for="neighborhood_number" class="form-label fw-bold">

                                </label>                                    Neighborhood Number (رقم الحي)

                                <input type="text" name="basin" id="basin"                                </label>

                                    class="form-control @error('basin') is-invalid @enderror" value="{{ old('basin', $land->basin) }}"                                <input type="text" name="neighborhood_number" id="neighborhood_number"

                                    required>                                    class="form-control @error('neighborhood_number') is-invalid @enderror"

                                @error('basin')                                    value="{{ old('neighborhood_number', $land->neighborhood_number) }}">

                                    <div class="invalid-feedback">{{ $message }}</div>                                @error('neighborhood_number')

                                @enderror                                    <div class="invalid-feedback">{{ $message }}</div>

                            </div>                                @enderror

                            </div>

                            <div class="col-md-6 mb-3">                        </div>

                                <label for="basin_number" class="form-label fw-bold">

                                    Basin Number (رقم الحوض) <span class="text-danger">*</span>                        <div class="row">

                                </label>                            <div class="col-md-6 mb-3">

                                <input type="text" name="basin_number" id="basin_number"                                <label for="plot_number" class="form-label fw-bold">

                                    class="form-control @error('basin_number') is-invalid @enderror"                                    Plot Number (رقم القطعة) <span class="text-danger">*</span>

                                    value="{{ old('basin_number', $land->basin_number) }}" required>                                </label>

                                @error('basin_number')                                <input type="text" name="plot_number" id="plot_number"

                                    <div class="invalid-feedback">{{ $message }}</div>                                    class="form-control @error('plot_number') is-invalid @enderror"

                                @enderror                                    value="{{ old('plot_number', $land->plot_number) }}" required>

                            </div>                                @error('plot_number')

                        </div>                                    <div class="invalid-feedback">{{ $message }}</div>

                                @enderror

                        <div class="row">                            </div>

                            <div class="col-md-6 mb-3">

                                <label for="neighborhood" class="form-label fw-bold">                            <div class="col-md-6 mb-3">

                                    Neighborhood (الحي)                                <label for="plot_key" class="form-label fw-bold">

                                </label>                                    Plot Key (مفتاح القطعة) <span class="text-danger">*</span>

                                <input type="text" name="neighborhood" id="neighborhood"                                </label>

                                    class="form-control @error('neighborhood') is-invalid @enderror"                                <input type="text" name="plot_key" id="plot_key"

                                    value="{{ old('neighborhood', $land->neighborhood) }}">                                    class="form-control @error('plot_key') is-invalid @enderror"

                                @error('neighborhood')                                    value="{{ old('plot_key', $land->plot_key) }}" required>

                                    <div class="invalid-feedback">{{ $message }}</div>                                @error('plot_key')

                                @enderror                                    <div class="invalid-feedback">{{ $message }}</div>

                            </div>                                @enderror

                            </div>

                            <div class="col-md-6 mb-3">                        </div>

                                <label for="neighborhood_number" class="form-label fw-bold">

                                    Neighborhood Number (رقم الحي)                        <div class="row">

                                </label>                            <div class="col-md-12 mb-3">

                                <input type="text" name="neighborhood_number" id="neighborhood_number"                                <label for="area_m2" class="form-label fw-bold">

                                    class="form-control @error('neighborhood_number') is-invalid @enderror"                                    Area (m²) <span class="text-danger">*</span>

                                    value="{{ old('neighborhood_number', $land->neighborhood_number) }}">                                </label>

                                @error('neighborhood_number')                                <input type="number" step="0.01" name="area_m2" id="area_m2"

                                    <div class="invalid-feedback">{{ $message }}</div>                                    class="form-control @error('area_m2') is-invalid @enderror"

                                @enderror                                    value="{{ old('area_m2', $land->area_m2) }}" required>

                            </div>                                @error('area_m2')

                        </div>                                    <div class="invalid-feedback">{{ $message }}</div>

                                @enderror

                        <div class="row">                            </div>

                            <div class="col-md-6 mb-3">                        </div>

                                <label for="plot_number" class="form-label fw-bold">

                                    Plot Number (رقم القطعة) <span class="text-danger">*</span>                        <!-- Zoning Status Section -->

                                </label>                        <div class="mb-3">

                                <input type="text" name="plot_number" id="plot_number"                            <label class="form-label fw-bold">Zoning Status (التنظيم)</label>

                                    class="form-control @error('plot_number') is-invalid @enderror"                            <div class="zoning-wrapper">

                                    value="{{ old('plot_number', $land->plot_number) }}" required>                                <!-- Search Bar -->

                                @error('plot_number')                                <div class="zoning-search-box">

                                    <div class="invalid-feedback">{{ $message }}</div>                                    <i class="bi bi-search"></i>

                                @enderror                                    <input type="text" id="zoningSearch" placeholder="Search zoning status...">

                            </div>                                </div>



                            <div class="col-md-6 mb-3">                                <!-- Selected Items Display -->

                                <label for="plot_key" class="form-label fw-bold">                                <div class="zoning-selected-area">

                                    Plot Key (مفتاح القطعة) <span class="text-danger">*</span>                                    <div class="selected-header">

                                </label>                                        <span>Selected Items</span>

                                <input type="text" name="plot_key" id="plot_key"                                        <button type="button" class="btn-add-zoning" data-bs-toggle="modal"

                                    class="form-control @error('plot_key') is-invalid @enderror"                                            data-bs-target="#addZoningModal">

                                    value="{{ old('plot_key', $land->plot_key) }}" required>                                            <i class="bi bi-plus-circle"></i> Add New

                                @error('plot_key')                                        </button>

                                    <div class="invalid-feedback">{{ $message }}</div>                                    </div>

                                @enderror                                    <div id="badgeContainer" class="selected-badges">

                            </div>                                        <em class="no-selection">No items selected</em>

                        </div>                                    </div>

                                </div>

                        <div class="row">

                            <div class="col-md-12 mb-3">                                <!-- Scrollable Checkbox List -->

                                <label for="area_m2" class="form-label fw-bold">                                <div class="zoning-scroll-area">

                                    Area (m²) <span class="text-danger">*</span>                                    <div id="noResults" class="no-results-msg">

                                </label>                                        <i class="bi bi-search"></i>

                                <input type="number" step="0.01" name="area_m2" id="area_m2"                                        <p>No zoning status found</p>

                                    class="form-control @error('area_m2') is-invalid @enderror"                                    </div>

                                    value="{{ old('area_m2', $land->area_m2) }}" required min="0">                                    @php

                                @error('area_m2')                                        // Get currently selected zoning statuses from the land's zoning field

                                    <div class="invalid-feedback">{{ $message }}</div>$currentZoningNames = $land->zoning ? explode(', ', $land->zoning) : [];

                                @enderror$selectedZoningIds = $zoningStatuses

                                <small class="text-muted">Site area will be auto-updated</small>    ->whereIn('name_ar', $currentZoningNames)

                            </div>    ->pluck('id')

                        </div>                                            ->toArray();

                                    @endphp

                        <!-- Zoning Status Section -->                                    @foreach ($zoningStatuses as $status)

                        <div class="mb-3">                                        <label class="zoning-option" data-name="{{ strtolower($status->name_ar) }}">

                            <label class="form-label fw-bold">Zoning Status (التنظيم)</label>                                            <input type="checkbox" class="zoning-checkbox" name="zoning_statuses[]"

                            <div class="zoning-wrapper">                                                value="{{ $status->id }}" id="zoning_{{ $status->id }}"

                                <!-- Search Bar -->                                                {{ in_array($status->id, old('zoning_statuses', $selectedZoningIds)) ? 'checked' : '' }}>

                                <div class="zoning-search-box">                                            <span class="checkbox-custom"></span>

                                    <i class="bi bi-search"></i>                                            <span class="option-text">{{ $status->name_ar }}</span>

                                    <input type="text" id="zoningSearch" placeholder="Search zoning status...">                                        </label>

                                </div>                                    @endforeach

                                </div>

                                <!-- Selected Items Display -->                            </div>

                                <div class="zoning-selected-area">                            @error('zoning_statuses')

                                    <div class="selected-header">                                <div class="text-danger small mt-2">{{ $message }}</div>

                                        <span>Selected Items</span>                            @enderror

                                        <button type="button" class="btn-add-zoning" data-bs-toggle="modal"                        </div>

                                            data-bs-target="#addZoningModal">

                                            <i class="bi bi-plus-circle"></i> Add New                        <div class="row">

                                        </button>                            <div class="col-md-12 mb-3">

                                    </div>                                <label for="map_location" class="form-label fw-bold">

                                    <div id="badgeContainer" class="selected-badges">                                    Google Maps URL

                                        <em class="no-selection">No items selected</em>                                </label>

                                    </div>                                <input type="url" name="map_location" id="map_location"

                                </div>                                    class="form-control @error('map_location') is-invalid @enderror"

                                    value="{{ old('map_location', $land->map_location) }}"

                                <!-- Scrollable Checkbox List -->                                    placeholder="Paste Google Maps URL here (e.g., https://maps.google.com/?q=31.9539,35.9106)">

                                <div class="zoning-scroll-area">                                @error('map_location')

                                    <div id="noResults" class="no-results-msg">                                    <div class="invalid-feedback">{{ $message }}</div>

                                        <i class="bi bi-search"></i>                                @enderror

                                        <p>No zoning status found</p>                                <small class="text-muted">Coordinates will be automatically extracted</small>

                                    </div>                            </div>

                                    @php                        </div>

                                        // Get currently selected zoning status IDs from land's zoning field

                                        $currentZoningNames = $land->zoning ? explode(', ', $land->zoning) : [];                        <div class="row">

                                        $selectedZoningIds = \App\Models\ZoningStatus::whereIn('name_ar', $currentZoningNames)->pluck('id')->toArray();                            <div class="col-md-6 mb-3">

                                    @endphp                                <label for="latitude" class="form-label fw-bold">

                                    @foreach ($zoningStatuses as $status)                                    Latitude (خط العرض)

                                        <label class="zoning-option" data-name="{{ strtolower($status->name_ar) }}">                                </label>

                                            <input type="checkbox" class="zoning-checkbox" name="zoning_statuses[]"                                <input type="number" step="0.0000001" name="latitude" id="latitude"

                                                value="{{ $status->id }}" id="zoning_{{ $status->id }}"                                    class="form-control @error('latitude') is-invalid @enderror"

                                                {{ in_array($status->id, old('zoning_statuses', $selectedZoningIds)) ? 'checked' : '' }}>                                    value="{{ old('latitude', $land->latitude) }}" placeholder="e.g., 31.9539">

                                            <span class="checkbox-custom"></span>                                @error('latitude')

                                            <span class="option-text">{{ $status->name_ar }}</span>                                    <div class="invalid-feedback">{{ $message }}</div>

                                        </label>                                @enderror

                                    @endforeach                            </div>

                                </div>

                            </div>                            <div class="col-md-6 mb-3">

                            @error('zoning_statuses')                                <label for="longitude" class="form-label fw-bold">

                                <div class="text-danger small mt-2">{{ $message }}</div>                                    Longitude (خط الطول)

                            @enderror                                </label>

                        </div>                                <input type="number" step="0.0000001" name="longitude" id="longitude"

                                    class="form-control @error('longitude') is-invalid @enderror"

                        <div class="row">                                    value="{{ old('longitude', $land->longitude) }}" placeholder="e.g., 35.9106">

                            <div class="col-md-12 mb-3">                                @error('longitude')

                                <label for="map_location" class="form-label fw-bold">                                    <div class="invalid-feedback">{{ $message }}</div>

                                    Google Maps URL                                @enderror

                                </label>                            </div>

                                <input type="url" name="map_location" id="map_location"                        </div>

                                    class="form-control @error('map_location') is-invalid @enderror"

                                    value="{{ old('map_location', $land->map_location) }}"                        <hr class="my-4">

                                    placeholder="Paste Google Maps URL here (e.g., https://maps.google.com/?q=31.9539,35.9106)">

                                @error('map_location')                        <h5 class="mb-3 text-orange">

                                    <div class="invalid-feedback">{{ $message }}</div>                            <i class="bi bi-file-earmark-text me-2"></i>Documents

                                @enderror                        </h5>

                                <small class="text-muted">Coordinates will be automatically extracted</small>

                            </div>                        <div class="row">

                        </div>                            <div class="col-md-4 mb-3">

                                <label for="ownership_doc" class="form-label fw-bold">

                        <div class="row">                                    سند الملكية

                            <div class="col-md-6 mb-3">                                </label>

                                <label for="latitude" class="form-label fw-bold">                                @if ($land->ownership_doc)

                                    Latitude (خط العرض)                                    <div class="mb-2">

                                </label>                                        <a href="{{ asset('storage/' . $land->ownership_doc) }}" target="_blank"

                                <input type="number" step="0.0000001" name="latitude" id="latitude"                                            class="btn btn-sm btn-outline-primary">

                                    class="form-control @error('latitude') is-invalid @enderror"                                            <i class="bi bi-eye me-1"></i> View Current

                                    value="{{ old('latitude', $land->latitude) }}" placeholder="e.g., 31.9539">                                        </a>

                                @error('latitude')                                    </div>

                                    <div class="invalid-feedback">{{ $message }}</div>                                @endif

                                @enderror                                <input type="file" name="ownership_doc" id="ownership_doc"

                            </div>                                    class="form-control @error('ownership_doc') is-invalid @enderror"

                                    accept=".jpg,.jpeg,.pdf">

                            <div class="col-md-6 mb-3">                                @error('ownership_doc')

                                <label for="longitude" class="form-label fw-bold">                                    <div class="invalid-feedback">{{ $message }}</div>

                                    Longitude (خط الطول)                                @enderror

                                </label>                                <small class="text-muted">JPG or PDF only (Max: 10MB)</small>

                                <input type="number" step="0.0000001" name="longitude" id="longitude"                            </div>

                                    class="form-control @error('longitude') is-invalid @enderror"

                                    value="{{ old('longitude', $land->longitude) }}" placeholder="e.g., 35.9106">                            <div class="col-md-4 mb-3">

                                @error('longitude')                                <label for="site_plan" class="form-label fw-bold">

                                    <div class="invalid-feedback">{{ $message }}</div>                                    مخطط الموقع

                                @enderror                                </label>

                            </div>                                @if ($land->site_plan)

                        </div>                                    <div class="mb-2">

                                        <a href="{{ asset('storage/' . $land->site_plan) }}" target="_blank"

                        <hr class="my-4">                                            class="btn btn-sm btn-outline-primary">

                                            <i class="bi bi-eye me-1"></i> View Current

                        <h5 class="mb-3 text-orange">                                        </a>

                            <i class="bi bi-file-earmark-text me-2"></i>Documents                                    </div>

                        </h5>                                @endif

                                <input type="file" name="site_plan" id="site_plan"

                        <div class="row">                                    class="form-control @error('site_plan') is-invalid @enderror"

                            <div class="col-md-4 mb-3">                                    accept=".jpg,.jpeg,.pdf">

                                <label for="ownership_doc" class="form-label fw-bold">                                @error('site_plan')

                                    سند الملكية                                    <div class="invalid-feedback">{{ $message }}</div>

                                </label>                                @enderror

                                @if($land->ownership_doc)                                <small class="text-muted">JPG or PDF only (Max: 10MB)</small>

                                    <div class="mb-2">                            </div>

                                        <a href="{{ asset('storage/' . $land->ownership_doc) }}" target="_blank" class="btn btn-sm btn-outline-primary">

                                            <i class="bi bi-eye me-1"></i> View Current                            <div class="col-md-4 mb-3">

                                        </a>                                <label for="zoning_plan" class="form-label fw-bold">

                                    </div>                                    مخطط تنظيمي

                                @endif                                </label>

                                <input type="file" name="ownership_doc" id="ownership_doc"                                @if ($land->zoning_plan)

                                    class="form-control @error('ownership_doc') is-invalid @enderror"                                    <div class="mb-2">

                                    accept=".jpg,.jpeg,.pdf">                                        <a href="{{ asset('storage/' . $land->zoning_plan) }}" target="_blank"

                                @error('ownership_doc')                                            class="btn btn-sm btn-outline-primary">

                                    <div class="invalid-feedback">{{ $message }}</div>                                            <i class="bi bi-eye me-1"></i> View Current

                                @enderror                                        </a>

                                <small class="text-muted">JPG or PDF only (Max: 10MB){{ $land->ownership_doc ? ' - Upload new to replace' : '' }}</small>                                    </div>

                            </div>                                @endif

                                <input type="file" name="zoning_plan" id="zoning_plan"

                            <div class="col-md-4 mb-3">                                    class="form-control @error('zoning_plan') is-invalid @enderror"

                                <label for="site_plan" class="form-label fw-bold">                                    accept=".jpg,.jpeg,.pdf">

                                    مخطط الموقع                                @error('zoning_plan')

                                </label>                                    <div class="invalid-feedback">{{ $message }}</div>

                                @if($land->site_plan)                                @enderror

                                    <div class="mb-2">                                <small class="text-muted">JPG or PDF only (Max: 10MB)</small>

                                        <a href="{{ asset('storage/' . $land->site_plan) }}" target="_blank" class="btn btn-sm btn-outline-primary">                            </div>

                                            <i class="bi bi-eye me-1"></i> View Current                        </div>

                                        </a>

                                    </div>                        <!-- Submit Buttons -->

                                @endif                        <div class="d-flex gap-2 justify-content-end mt-4">

                                <input type="file" name="site_plan" id="site_plan"                            <a href="{{ route('lands.show', $land) }}" class="btn btn-secondary">

                                    class="form-control @error('site_plan') is-invalid @enderror"                                <i class="bi bi-x-circle me-1"></i> Cancel

                                    accept=".jpg,.jpeg,.pdf">                            </a>

                                @error('site_plan')                            <button type="submit" class="btn btn-orange">

                                    <div class="invalid-feedback">{{ $message }}</div>                                <i class="bi bi-check-circle me-1"></i> Update Land

                                @enderror                            </button>

                                <small class="text-muted">JPG or PDF only (Max: 10MB){{ $land->site_plan ? ' - Upload new to replace' : '' }}</small>                        </div>

                            </div>                    </form>

                </div>

                            <div class="col-md-4 mb-3">            </div>

                                <label for="zoning_plan" class="form-label fw-bold">        </div>

                                    مخطط تنظيمي    </div>

                                </label>

                                @if($land->zoning_plan)    <script>

                                    <div class="mb-2">        document.addEventListener('DOMContentLoaded', function() {

                                        <a href="{{ asset('storage/' . $land->zoning_plan) }}" target="_blank" class="btn btn-sm btn-outline-primary">            const siteSelect = document.getElementById('site_id');

                                            <i class="bi bi-eye me-1"></i> View Current            const governorateInput = document.getElementById('governorate');

                                        </a>            const regionInput = document.getElementById('region');

                                    </div>            const mapLocationInput = document.getElementById('map_location');

                                @endif            const latitudeInput = document.getElementById('latitude');

                                <input type="file" name="zoning_plan" id="zoning_plan"            const longitudeInput = document.getElementById('longitude');

                                    class="form-control @error('zoning_plan') is-invalid @enderror"

                                    accept=".jpg,.jpeg,.pdf">            // All sites data from backend

                                @error('zoning_plan')            const allSites = @json($sites);

                                    <div class="invalid-feedback">{{ $message }}</div>

                                @enderror            // Governorate full names mapping (English only)

                                <small class="text-muted">JPG or PDF only (Max: 10MB){{ $land->zoning_plan ? ' - Upload new to replace' : '' }}</small>            const governorateNames = {

                            </div>                'AM': 'Amman',

                        </div>                'IR': 'Irbid',

                'MF': 'Mafraq',

                        <!-- Submit Buttons -->                'AJ': 'Ajloun',

                        <div class="d-flex gap-2 justify-content-end mt-4">                'JA': 'Jerash',

                            <a href="{{ route('lands.show', $land) }}" class="btn btn-secondary">                'BA': 'Balqa',

                                <i class="bi bi-x-circle me-1"></i> Cancel                'ZA': 'Zarqa',

                            </a>                'MA': 'Madaba',

                            <button type="submit" class="btn btn-orange">                'AQ': 'Aqaba',

                                <i class="bi bi-check-circle me-1"></i> Update Land                'KA': 'Karak',

                            </button>                'TF': 'Tafileh',

                        </div>                'MN': 'Ma\'an'

                    </form>            };

                </div>

            </div>            // Governorate to Region mapping

        </div>            const governorateToRegion = {

    </div>                'AM': 'Capital',

                'IR': 'North',

    <!-- Modal for Adding New Zoning Status -->                'MF': 'North',

    <div class="modal fade" id="addZoningModal" tabindex="-1" aria-labelledby="addZoningModalLabel"                'AJ': 'North',

        aria-hidden="true">                'JA': 'North',

        <div class="modal-dialog">                'BA': 'Middle',

            <div class="modal-content">                'ZA': 'Middle',

                <div class="modal-header">                'MA': 'Middle',

                    <h5 class="modal-title" id="addZoningModalLabel">                'AQ': 'South',

                        <i class="bi bi-plus-circle me-2 text-orange"></i>                'KA': 'South',

                        Add New Zoning Status                'TF': 'South',

                    </h5>                'MN': 'South'

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>            };

                </div>

                <div class="modal-body">            // When site is selected, auto-fill governorate and region

                    <div class="mb-3">            siteSelect.addEventListener('change', function() {

                        <label for="new_zoning_name" class="form-label fw-bold">                const selectedSiteId = this.value;

                            Zoning Status Name (Arabic) <span class="text-danger">*</span>

                        </label>                if (selectedSiteId) {

                        <input type="text" id="new_zoning_name" class="form-control"                    const selectedSite = allSites.find(site => site.id == selectedSiteId);

                            placeholder="Example: سكن تجاري">                    if (selectedSite) {

                        <div class="invalid-feedback" id="zoning_error"></div>                        // Use full governorate name instead of code

                    </div>                        governorateInput.value = governorateNames[selectedSite.governorate] || selectedSite

                </div>                            .governorate;

                <div class="modal-footer">                        regionInput.value = governorateToRegion[selectedSite.governorate] || '';

                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">                    }

                        <i class="bi bi-x-circle me-1"></i> Cancel                } else {

                    </button>                    governorateInput.value = '';

                    <button type="button" class="btn btn-orange" id="saveNewZoning">                    regionInput.value = '';

                        <i class="bi bi-check-circle me-1"></i> Save                }

                    </button>            });

                </div>

            </div>            // Extract coordinates from Google Maps URL

        </div>            mapLocationInput.addEventListener('input', function() {

    </div>                const url = this.value;

@endsection                if (!url) return;



@push('scripts')                let lat = null;

    <script>                let lng = null;

        document.addEventListener('DOMContentLoaded', function() {

            const governorateFilter = document.getElementById('governorate_filter');                // Pattern 1: ?q=lat,lng

            const siteSelect = document.getElementById('site_id');                const pattern1 = /[?&]q=(-?\d+\.?\d*),(-?\d+\.?\d*)/;

            const governorateInput = document.getElementById('governorate');                const match1 = url.match(pattern1);

            const regionInput = document.getElementById('region');                if (match1) {

                    lat = match1[1];

            // All sites data from backend                    lng = match1[2];

            const allSites = @json($sites);                }

            const currentSiteId = {{ $land->site_id }};

            const currentGovernorate = '{{ $land->site->governorate ?? '' }}';                // Pattern 2: @lat,lng,zoom

                const pattern2 = /@(-?\d+\.?\d*),(-?\d+\.?\d*),/;

            // Governorate full names mapping (English only)                const match2 = url.match(pattern2);

            const governorateNames = {                if (match2) {

                'AM': 'Amman',                    lat = match2[1];

                'IR': 'Irbid',                    lng = match2[2];

                'MF': 'Mafraq',                }

                'AJ': 'Ajloun',

                'JA': 'Jerash',                // Pattern 3: /place/lat,lng

                'BA': 'Balqa',                const pattern3 = /\/place\/(-?\d+\.?\d*),(-?\d+\.?\d*)/;

                'ZA': 'Zarqa',                const match3 = url.match(pattern3);

                'MA': 'Madaba',                if (match3) {

                'AQ': 'Aqaba',                    lat = match3[1];

                'KA': 'Karak',                    lng = match3[2];

                'TF': 'Tafileh',                }

                'MN': 'Ma\'an'

            };                // Pattern 4: ll=lat,lng

                const pattern4 = /[?&]ll=(-?\d+\.?\d*),(-?\d+\.?\d*)/;

            // Governorate to Region mapping                const match4 = url.match(pattern4);

            const governorateToRegion = {                if (match4) {

                'AM': 'Capital',                    lat = match4[1];

                'IR': 'North',                    lng = match4[2];

                'MF': 'North',                }

                'AJ': 'North',

                'JA': 'North',                // Pattern 5: /maps/place/.../@lat,lng

                'BA': 'Middle',                const pattern5 = /maps\/place\/[^@]+@(-?\d+\.?\d*),(-?\d+\.?\d*)/;

                'ZA': 'Middle',                const match5 = url.match(pattern5);

                'MA': 'Middle',                if (match5) {

                'AQ': 'South',                    lat = match5[1];

                'KA': 'South',                    lng = match5[2];

                'TF': 'South',                }

                'MN': 'South'

            };                if (lat && lng) {

                    latitudeInput.value = lat;

            // When governorate is selected, populate sites                    longitudeInput.value = lng;

            governorateFilter.addEventListener('change', function() {                }

                const selectedGovernorate = this.value;            });



                // Reset sites            // ============ Zoning Status Checkboxes and Badges ============

                siteSelect.innerHTML = '<option value="">-- Select Site --</option>';            const zoningCheckboxes = document.querySelectorAll('.zoning-checkbox');

                governorateInput.value = '';            const badgeContainer = document.getElementById('badgeContainer');

                regionInput.value = '';

            function updateBadges() {

                if (selectedGovernorate) {                badgeContainer.innerHTML = '';

                    // Enable site dropdown                const checkedBoxes = document.querySelectorAll('.zoning-checkbox:checked');

                    siteSelect.disabled = false;

                if (checkedBoxes.length === 0) {

                    // Filter sites by governorate                    badgeContainer.innerHTML = '<em class="no-selection">No items selected</em>';

                    const filteredSites = allSites.filter(site => site.governorate === selectedGovernorate);                    return;

                }

                    if (filteredSites.length > 0) {

                        filteredSites.forEach(site => {                checkedBoxes.forEach(checkbox => {

                            const option = document.createElement('option');                    const label = checkbox.closest('.zoning-option').querySelector('.option-text')

                            option.value = site.id;                        .textContent.trim();

                            option.textContent = `${site.code} - ${site.name}`;                    const badge = document.createElement('span');

                            if (site.id === currentSiteId) {                    badge.className = 'badge-item';

                                option.selected = true;                    badge.innerHTML = `

                            }                        ${label}

                            siteSelect.appendChild(option);                        <i class="bi bi-x-circle" data-checkbox-id="${checkbox.id}"></i>

                        });                    `;

                    } else {                    badgeContainer.appendChild(badge);

                        const option = document.createElement('option');

                        option.value = '';                    // Add click handler to remove badge

                        option.textContent = 'No sites available for this governorate';                    badge.querySelector('i').addEventListener('click', function(e) {

                        option.disabled = true;                        e.stopPropagation();

                        siteSelect.appendChild(option);                        checkbox.checked = false;

                    }                        updateBadges();

                } else {                    });

                    siteSelect.disabled = true;                });

                }            }

            });

            zoningCheckboxes.forEach(checkbox => {

            // When site is selected, auto-fill governorate and region                checkbox.addEventListener('change', updateBadges);

            siteSelect.addEventListener('change', function() {            });

                const selectedSiteId = this.value;

            // Initialize badges on page load

                if (selectedSiteId) {            updateBadges();

                    const selectedSite = allSites.find(site => site.id == selectedSiteId);

                    if (selectedSite) {            // Zoning Search functionality

                        // Use full governorate name instead of code            const zoningSearch = document.getElementById('zoningSearch');

                        governorateInput.value = governorateNames[selectedSite.governorate] || selectedSite            const noResults = document.getElementById('noResults');

                            .governorate;

                        regionInput.value = governorateToRegion[selectedSite.governorate] || '';            zoningSearch.addEventListener('input', function() {

                    }                const searchTerm = this.value.toLowerCase().trim();

                } else {                const zoningItems = document.querySelectorAll('.zoning-option');

                    governorateInput.value = '';                let hasResults = false;

                    regionInput.value = '';

                }                zoningItems.forEach((item) => {

            });                    const name = item.getAttribute('data-name');



            // Initialize with current land's site                    if (searchTerm === '' || (name && name.includes(searchTerm))) {

            if (currentGovernorate) {                        item.style.cssText = 'display: flex !important;';

                governorateFilter.value = currentGovernorate;                        hasResults = true;

                governorateFilter.dispatchEvent(new Event('change'));                    } else {

            }                        item.style.cssText = 'display: none !important;';

                    }

            // Google Maps URL coordinate extraction                });

            const mapLocationInput = document.getElementById('map_location');

            const latitudeInput = document.getElementById('latitude');                // Show/hide no results message

            const longitudeInput = document.getElementById('longitude');                if (noResults) {

                    if (hasResults) {

            mapLocationInput.addEventListener('blur', function() {                        noResults.classList.remove('show');

                const url = this.value.trim();                        noResults.style.display = 'none';

                if (url) {                    } else {

                    extractCoordinates(url);                        noResults.classList.add('show');

                }                        noResults.style.display = 'block';

            });                    }

                }

            function extractCoordinates(url) {            });

                try {

                    // Pattern 1: https://www.google.com/maps/place/32.4011265,36.3359156            // ============ Handle New Zoning Status Creation ============

                    let match = url.match(/place\/([-+]?\d+\.?\d*),([-+]?\d+\.?\d*)/);            const saveNewZoningBtn = document.getElementById('saveNewZoning');

            const newZoningNameInput = document.getElementById('new_zoning_name');

                    // Pattern 2: @32.4011265,36.3359156,17z            const zoningError = document.getElementById('zoning_error');

                    if (!match) {            const addZoningModalEl = document.getElementById('addZoningModal');

                        match = url.match(/@([-+]?\d+\.?\d*),([-+]?\d+\.?\d*)/);

                    }            saveNewZoningBtn.addEventListener('click', async function() {

                const name = newZoningNameInput.value.trim();

                    // Pattern 3: ?q=32.4011265,36.3359156

                    if (!match) {                if (!name) {

                        match = url.match(/[?&]q=([-+]?\d+\.?\d*),([-+]?\d+\.?\d*)/);                    newZoningNameInput.classList.add('is-invalid');

                    }                    zoningError.textContent = 'Please enter zoning status name';

                    zoningError.style.display = 'block';

                    // Pattern 4: /maps?q=32.4011265,36.3359156                    return;

                    if (!match) {                }

                        match = url.match(/maps\?q=([-+]?\d+\.?\d*),([-+]?\d+\.?\d*)/);

                    }                newZoningNameInput.classList.remove('is-invalid');

                zoningError.style.display = 'none';

                    // Pattern 5: 3m2!3d32.4011265!4d36.3359156 (from data parameter)                saveNewZoningBtn.disabled = true;

                    if (!match) {                saveNewZoningBtn.innerHTML =

                        const latMatch = url.match(/!3d([-+]?\d+\.?\d*)/);                    '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

                        const lngMatch = url.match(/!4d([-+]?\d+\.?\d*)/);

                        if (latMatch && lngMatch) {                try {

                            match = [null, latMatch[1], lngMatch[1]];                    const response = await fetch('{{ route('zoning-statuses.store') }}', {

                        }                        method: 'POST',

                    }                        headers: {

                            'Content-Type': 'application/json',

                    if (match && match[1] && match[2]) {                            'X-CSRF-TOKEN': '{{ csrf_token() }}',

                        const lat = parseFloat(match[1]);                            'Accept': 'application/json'

                        const lng = parseFloat(match[2]);                        },

                        body: JSON.stringify({

                        // Validate coordinates (Jordan is roughly 29-33°N, 35-39°E)                            name_ar: name

                        if (lat >= 29 && lat <= 34 && lng >= 34 && lng <= 40) {                        })

                            latitudeInput.value = lat;                    });

                            longitudeInput.value = lng;

                    const data = await response.json();

                            // Show success message

                            showCoordinateMessage('success', 'Coordinates extracted successfully!');                    if (response.ok) {

                        } else {                        // Add new checkbox to the list

                            showCoordinateMessage('warning',                        const scrollArea = document.querySelector('.zoning-scroll-area');

                                'Coordinates extracted but seem outside Jordan. Please verify.');                        const noResultsMsg = document.getElementById('noResults');

                            latitudeInput.value = lat;                        const newOption = document.createElement('label');

                            longitudeInput.value = lng;                        newOption.className = 'zoning-option';

                        }                        newOption.setAttribute('data-name', data.name_ar.toLowerCase());

                    } else {                        newOption.innerHTML = `

                        showCoordinateMessage('warning', 'Could not extract coordinates. Please enter manually.');                            <input type="checkbox" class="zoning-checkbox"

                    }                                name="zoning_statuses[]"

                } catch (error) {                                value="${data.id}"

                    console.error('Error extracting coordinates:', error);                                id="zoning_${data.id}" checked>

                    showCoordinateMessage('error', 'Error extracting coordinates. Please enter manually.');                            <span class="checkbox-custom"></span>

                }                            <span class="option-text">${data.name_ar}</span>

            }                        `;



            function showCoordinateMessage(type, message) {                        // Insert after noResults div

                // Remove any existing message                        if (noResultsMsg.nextSibling) {

                const existingAlert = document.querySelector('.coordinate-alert');                            scrollArea.insertBefore(newOption, noResultsMsg.nextSibling);

                if (existingAlert) {                        } else {

                    existingAlert.remove();                            scrollArea.appendChild(newOption);

                }                        }



                // Create new alert                        // Add event listener to new checkbox

                const alertDiv = document.createElement('div');                        const newCheckbox = newOption.querySelector('.zoning-checkbox');

                alertDiv.className =                        newCheckbox.addEventListener('change', updateBadges);

                    `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'warning'} alert-dismissible fade show coordinate-alert mt-2`;

                alertDiv.innerHTML = `                        // Update badges

                    <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>${message}                        updateBadges();

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

                `;                        // Clear input

                        newZoningNameInput.value = '';

                mapLocationInput.parentElement.appendChild(alertDiv);

                        // Close modal manually (for Boosted compatibility)

                // Auto-dismiss after 5 seconds                        const modalBackdrop = document.querySelector('.modal-backdrop');

                setTimeout(() => {                        addZoningModalEl.classList.remove('show');

                    if (alertDiv.parentElement) {                        addZoningModalEl.style.display = 'none';

                        alertDiv.remove();                        document.body.classList.remove('modal-open');

                    }                        if (modalBackdrop) {

                }, 5000);                            modalBackdrop.remove();

            }                        }

                    } else {

            // ============ Zoning Status Checkboxes and Badges ============                        throw new Error(data.message || 'Error saving zoning status');

            const zoningCheckboxes = document.querySelectorAll('.zoning-checkbox');                    }

            const badgeContainer = document.getElementById('badgeContainer');                } catch (error) {

                    newZoningNameInput.classList.add('is-invalid');

            function updateBadges() {                    zoningError.textContent = error.message;

                badgeContainer.innerHTML = '';                    zoningError.style.display = 'block';

                const checkedBoxes = document.querySelectorAll('.zoning-checkbox:checked');                } finally {

                    saveNewZoningBtn.disabled = false;

                if (checkedBoxes.length === 0) {                    saveNewZoningBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Save';

                    badgeContainer.innerHTML = '<em class="no-selection">No items selected</em>';                }

                    return;            });

                }

            // Clear validation on input

                checkedBoxes.forEach(checkbox => {            newZoningNameInput.addEventListener('input', function() {

                    const label = checkbox.closest('.zoning-option').querySelector('.option-text')                this.classList.remove('is-invalid');

                        .textContent.trim();                zoningError.style.display = 'none';

                    const badge = document.createElement('span');            });

                    badge.className = 'badge-item';        });

                    badge.innerHTML = `    </script>

                        ${label}

                        <i class="bi bi-x-circle" data-checkbox-id="${checkbox.id}"></i>    <!-- Modal for Adding New Zoning Status -->

                    `;    <div class="modal fade" id="addZoningModal" tabindex="-1" aria-labelledby="addZoningModalLabel"

                    badgeContainer.appendChild(badge);        aria-hidden="true">

        <div class="modal-dialog">

                    // Add click handler to remove badge            <div class="modal-content">

                    badge.querySelector('i').addEventListener('click', function(e) {                <div class="modal-header">

                        e.stopPropagation();                    <h5 class="modal-title" id="addZoningModalLabel">

                        checkbox.checked = false;                        <i class="bi bi-plus-circle me-2 text-orange"></i>

                        updateBadges();                        Add New Zoning Status

                    });                    </h5>

                });                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            }                </div>

                <div class="modal-body">

            zoningCheckboxes.forEach(checkbox => {                    <div class="mb-3">

                checkbox.addEventListener('change', updateBadges);                        <label for="new_zoning_name" class="form-label fw-bold">

            });                            Zoning Status Name (Arabic) <span class="text-danger">*</span>

                        </label>

            // Initialize badges on page load                        <input type="text" id="new_zoning_name" class="form-control"

            updateBadges();                            placeholder="Example: سكن تجاري">

                        <div class="invalid-feedback" id="zoning_error"></div>

            // Zoning Search functionality                    </div>

            const zoningSearch = document.getElementById('zoningSearch');                </div>

            const noResults = document.getElementById('noResults');                <div class="modal-footer">

                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">

            zoningSearch.addEventListener('input', function() {                        <i class="bi bi-x-circle me-1"></i> Cancel

                const searchTerm = this.value.toLowerCase().trim();                    </button>

                const zoningItems = document.querySelectorAll('.zoning-option');                    <button type="button" class="btn btn-orange" id="saveNewZoning">

                let hasResults = false;                        <i class="bi bi-check-circle me-1"></i> Save

                    </button>

                zoningItems.forEach((item) => {                </div>

                    const name = item.getAttribute('data-name');            </div>

        </div>

                    if (searchTerm === '' || (name && name.includes(searchTerm))) {    </div>

                        item.style.cssText = 'display: flex !important;';@endsection

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
