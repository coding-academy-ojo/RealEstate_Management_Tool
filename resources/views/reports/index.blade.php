@extends('layouts.app')

@section('title', 'Data Export & Reports')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1"><i class="bi bi-file-earmark-spreadsheet me-2"></i>Data Export & Reports</h1>
                <p class="text-muted mb-0">Export your data in CSV format for analysis and reporting</p>
            </div>
        </div>

        <!-- Statistics Overview -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="bi bi-building text-primary" style="font-size: 2rem;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-muted small">Total Sites</div>
                                <div class="fs-4 fw-bold">{{ number_format($stats['total_sites']) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="bi bi-house text-success" style="font-size: 2rem;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-muted small">Total Buildings</div>
                                <div class="fs-4 fw-bold">{{ number_format($stats['total_buildings']) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="bi bi-diagram-3 text-info" style="font-size: 2rem;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-muted small">Total Lands</div>
                                <div class="fs-4 fw-bold">{{ number_format($stats['total_lands']) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="bi bi-hammer text-warning" style="font-size: 2rem;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-muted small">Renovations</div>
                                <div class="fs-4 fw-bold">{{ number_format($stats['total_renovations']) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Cards -->
        <div class="row g-4">
            <!-- Full Hierarchy Export -->
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-1"><i class="bi bi-file-earmark-excel text-success me-2"></i>Export Full
                                Hierarchy</h5>
                            <p class="text-muted small mb-0">Export all Sites with their Lands and Buildings in a single
                                Excel file with merged cells for better readability.</p>
                        </div>
                        <form action="{{ route('reports.export.all') }}" method="POST">
                            @csrf
                            <input type="hidden" name="format" value="xlsx">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Services Section -->
            <div class="col-12 mt-5">
                <h5 class="mb-3"><i class="bi bi-plug me-2"></i>Utilities & Services</h5>
            </div>

            <!-- Water Services Export -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded p-2">
                                    <i class="bi bi-droplet-fill text-primary" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="card-title mb-1">Water Services Report</h6>
                                <p class="text-muted small mb-0">{{ number_format($stats['total_water_services']) }}
                                    services</p>
                            </div>
                        </div>
                        <p class="small text-muted">Complete water services report with summary statistics by governorate
                            AND detailed readings including consumption, billing amounts, and payment tracking.</p>
                        <form action="{{ route('reports.water-services-report') }}" method="POST" class="mt-3">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="bi bi-file-earmark-excel me-2"></i>Generate Report
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Electricity Services Export -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-warning bg-opacity-10 rounded p-2">
                                    <i class="bi bi-lightning-charge-fill text-warning" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="card-title mb-1">Electricity Services Report</h6>
                                <p class="text-muted small mb-0">{{ number_format($stats['total_electricity_services']) }}
                                    services</p>
                            </div>
                        </div>
                        <p class="small text-muted">Complete electricity services report with summary statistics AND
                            detailed readings including imported/exported energy, consumption, billing, and payment
                            tracking.</p>
                        <form action="{{ route('reports.electricity-services-report') }}" method="POST" class="mt-3">
                            @csrf
                            <button type="submit" class="btn btn-outline-warning w-100">
                                <i class="bi bi-file-earmark-excel me-2"></i>Generate Report
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Renovations Export -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-danger bg-opacity-10 rounded p-2">
                                    <i class="bi bi-hammer text-danger" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="card-title mb-1">Renovations Report</h6>
                                <p class="text-muted small mb-0">{{ number_format($stats['total_renovations']) }}
                                    renovations</p>
                            </div>
                        </div>
                        <p class="small text-muted">Two-sheet export combining governorate cost summary with a detailed
                            ledger of every renovation and its linked asset.</p>
                        <form action="{{ route('reports.renovations-report') }}" method="POST" class="mt-3">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-file-earmark-excel me-2"></i>Generate Report
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Information Note -->
        <div class="alert alert-info mt-4" role="alert">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Note:</strong> The Full Hierarchy export includes all sites, lands, and buildings with merged cells.
            Water and Electricity Services reports include TWO sheets: (1) Summary by governorate with totals, and (2)
            Detailed readings with consumption and billing data. Renovations report now ships with TWO sheets covering
            governorate cost KPIs and detailed renovation records.
        </div>
    </div>

    <style>
        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
            transform: translateY(-2px);
        }

        .transition {
            transition: all 0.3s ease;
        }
    </style>
@endsection
