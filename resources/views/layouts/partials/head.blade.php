<!-- Meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ config('app.name', 'Real Estate Management') }}</title>

<!-- Favicon -->
<link rel="icon" type="image/svg+xml"
    href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'><path fill='%23007bff' d='M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.707 1.5Z'/><path fill='%23007bff' d='m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293l6-6Z'/></svg>">

<!-- Copyright © 2014 Monotype Imaging Inc. All rights reserved -->
<!-- Boosted CSS -->
<link href="https://cdn.jsdelivr.net/npm/boosted@5.3.7/dist/css/orange-helvetica.min.css" rel="stylesheet"
    integrity="sha384-A0Qk1uKfS1i83/YuU13i2nx5pk79PkIfNFOVzTcjCMPGKIDj9Lqx9lJmV7cdBVQZ" crossorigin="anonymous">
<link href="https://cdn.jsdelivr.net/npm/boosted@5.3.7/dist/css/boosted.min.css" rel="stylesheet"
    integrity="sha384-Dg1JMmsMyxGWA26yEd/Wk3KTjzjp//GXdW4u4c+K/j6GYT5gsZoxBGK8Hq++sDbV" crossorigin="anonymous">

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<!-- Custom Orange Theme styles -->
<link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/breadcrumbs.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/login.css') }}" rel="stylesheet">

<!-- Global Styles -->
<style>
    #content {
        background-image: url("https://www.transparenttextures.com/patterns/climpek.png");
        background-color: #f8f9fa !important;
    }

    /* Orange theme styling - Solid Buttons */
    .btn-orange {
        background-color: #FF7900 !important;
        border-color: #FF7900 !important;
        color: white !important;
    }

    .btn-orange:hover {
        background-color: #e56b00 !important;
        border-color: #e56b00 !important;
        color: white !important;
    }

    .btn-outline-orange {
        background-color: #FF7900 !important;
        border-color: #FF7900 !important;
        color: white !important;
    }

    .btn-outline-orange:hover {
        background-color: #e56b00 !important;
        border-color: #e56b00 !important;
        color: white !important;
    }

    .btn-outline-primary {
        background-color: #0d6efd !important;
        border-color: #0d6efd !important;
        color: white !important;
    }

    .btn-outline-primary:hover {
        background-color: #0b5ed7 !important;
        border-color: #0b5ed7 !important;
        color: white !important;
    }

    .btn-outline-success {
        background-color: #198754 !important;
        border-color: #198754 !important;
        color: white !important;
    }

    .btn-outline-success:hover {
        background-color: #157347 !important;
        border-color: #157347 !important;
        color: white !important;
    }

    .btn-outline-danger {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
        color: white !important;
    }

    .btn-outline-danger:hover {
        background-color: #bb2d3b !important;
        border-color: #bb2d3b !important;
        color: white !important;
    }

    .btn-outline-warning {
        background-color: #ffc107 !important;
        border-color: #ffc107 !important;
        color: #000 !important;
    }

    .btn-outline-warning:hover {
        background-color: #ffca2c !important;
        border-color: #ffca2c !important;
        color: #000 !important;
    }

    .btn-outline-info {
        background-color: #0dcaf0 !important;
        border-color: #0dcaf0 !important;
        color: #000 !important;
    }

    .btn-outline-info:hover {
        background-color: #31d2f2 !important;
        border-color: #31d2f2 !important;
        color: #000 !important;
    }

    .text-orange {
        color: #FF7900 !important;
    }

    .bg-orange {
        background-color: #FF7900 !important;
    }

    .border-orange {
        border-color: #FF7900 !important;
    }

    /* Form focus states with orange */
    .form-control:focus,
    .form-select:focus {
        border-color: rgba(255, 107, 53, 0.5) !important;
        box-shadow: 0 0 0 0.25rem rgba(255, 107, 53, 0.25) !important;
    }

    /* Improve animations and transitions */
    .btn,
    .badge,
    .page-link,
    .stats-item,
    .form-control,
    .form-select,
    .input-group {
        transition: all 0.2s ease;
    }

    /* Card styling */
    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.07) !important;
    }

    /* Custom Pagination Styles */
    .pagination {
        gap: 0.25rem !important;
        margin-bottom: 0 !important;
    }

    .pagination .page-item {
        margin: 0 !important;
    }

    .pagination .page-link {
        border-radius: 0.25rem !important;
        border: 1px solid #dee2e6 !important;
        color: #495057 !important;
        padding: 0.25rem 0.5rem !important;
        font-size: 0.875rem !important;
        min-width: 2rem !important;
        height: 2rem !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.15s ease !important;
        line-height: 1 !important;
    }

    .pagination .page-link:hover {
        background-color: #f8f9fa !important;
        border-color: #FF7900 !important;
        color: #FF7900 !important;
        text-decoration: none !important;
    }

    .pagination .page-item.active .page-link {
        background-color: #FF7900 !important;
        border-color: #FF7900 !important;
        color: white !important;
        font-weight: 600 !important;
        z-index: 1 !important;
    }

    .pagination .page-item.disabled .page-link {
        background-color: #fff !important;
        border-color: #dee2e6 !important;
        color: #adb5bd !important;
        cursor: not-allowed !important;
        opacity: 0.6 !important;
    }
</style>

<!-- SortableJS for drag-and-drop image reordering -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

@yield('styles')
