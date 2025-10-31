@extends('layouts.app')

@section('title', 'Water Services Overview')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('water.services.index') }}">Water Services</a></li>
    <li class="breadcrumb-item active">Overview</li>
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
            background-image: url("{{ asset('assets/images/water-drops.png') }}") !important;
            background-repeat: repeat !important;
            background-size: 20px 20px !important;
            opacity: 0.2;
            pointer-events: none;
            z-index: 0;
        }

        #content>* {
            position: relative;
            z-index: 1;
        }

        .hover-lift {
            transition: all 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
        }
    </style>

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-droplet-fill text-primary me-2"></i>
                Water Services Overview
                <small class="text-muted fs-6 fw-normal" style="font-family: 'Segoe UI', Tahoma, sans-serif;">لوحة معلومات
                    خدمات المياه</small>
            </h2>
            <p class="text-muted mb-0">
                Comprehensive insights across services, companies, and consumption metrics
                <span class="text-muted small d-block mt-1">رؤية شاملة للخدمات والشركات ومقاييس الاستهلاك</span>
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('water.services.index') }}" class="btn btn-orange">
                <i class="bi bi-list-ul me-1"></i> All Services
            </a>
            <a href="{{ route('water.companies.index') }}" class="btn btn-outline-orange">
                <i class="bi bi-building-gear me-1"></i> Companies
            </a>
        </div>
    </div>

    @php
        $totalServices = max(1, (int) ($summaryStats['total_services'] ?? 0));
        $inactiveRatio =
            $totalServices > 0 ? round((($summaryStats['inactive_services'] ?? 0) / $totalServices) * 100, 1) : 0;
        $softDeletedRatio =
            $totalServices > 0 ? round((($summaryStats['soft_deleted_services'] ?? 0) / $totalServices) * 100, 1) : 0;
        $cards = [
            [
                'label' => 'Active Services',
                'label_ar' => 'الخدمات النشطة',
                'value' => number_format($summaryStats['active_services'] ?? 0),
                'icon' => 'bi-droplet-fill',
                'icon_bg' => 'bg-success-subtle',
                'icon_color' => 'text-success',
                'border_color' => 'border-success',
                'meta' => ($summaryStats['active_ratio'] ?? 0) . '% of total',
                'meta_ar' => 'من إجمالي ' . number_format($summaryStats['total_services'] ?? 0) . ' خدمة',
                'trend' => '+5.2%',
                'trend_positive' => true,
            ],
            [
                'label' => 'Total Consumption',
                'label_ar' => 'إجمالي الاستهلاك',
                'value' => number_format($summaryStats['consumption_last_12_months'] ?? 0, 0),
                'unit' => 'm³',
                'icon' => 'bi-graph-up-arrow',
                'icon_bg' => 'bg-primary-subtle',
                'icon_color' => 'text-primary',
                'border_color' => 'border-primary',
                'meta' => 'Last 12 months',
                'meta_ar' => 'آخر 12 شهراً',
                'trend' => '+12.8%',
                'trend_positive' => true,
            ],
            [
                'label' => 'Outstanding Bills',
                'label_ar' => 'الفواتير المستحقة',
                'value' => number_format($summaryStats['outstanding_amount'] ?? 0, 0),
                'unit' => 'JOD',
                'icon' => 'bi-cash-stack',
                'icon_bg' => 'bg-danger-subtle',
                'icon_color' => 'text-danger',
                'border_color' => 'border-danger',
                'meta' => 'Unpaid invoices',
                'meta_ar' => 'فواتير غير مدفوعة',
                'trend' => '-3.1%',
                'trend_positive' => true,
            ],
            [
                'label' => 'Avg. Consumption',
                'label_ar' => 'متوسط الاستهلاك',
                'value' => number_format($summaryStats['average_consumption'] ?? 0, 1),
                'unit' => 'm³',
                'icon' => 'bi-speedometer2',
                'icon_bg' => 'bg-warning-subtle',
                'icon_color' => 'text-warning',
                'border_color' => 'border-warning',
                'meta' => 'Per service/month',
                'meta_ar' => 'لكل خدمة شهرياً',
            ],
            [
                'label' => 'Inactive Services',
                'label_ar' => 'الخدمات غير النشطة',
                'value' => number_format($summaryStats['inactive_services'] ?? 0),
                'icon' => 'bi-pause-circle',
                'icon_bg' => 'bg-secondary-subtle',
                'icon_color' => 'text-secondary',
                'border_color' => 'border-secondary',
                'meta' => $inactiveRatio . '% of total',
                'meta_ar' => 'من إجمالي الخدمات',
            ],
        ];
    @endphp

    <!-- Enhanced Stats Cards - Dashboard Style -->
    <div class="row mb-4">
        @foreach ($cards as $card)
            <div class="col mb-3">
                <div class="card dashboard-card border-0 shadow-lg h-100" style="background-color: #1c1c1c !important;">
                    <div class="card-body text-white py-3 px-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-white bg-opacity-25 rounded-circle p-2">
                                    <i class="bi {{ $card['icon'] }} {{ $card['icon_color'] }}"
                                        style="font-size: 1.4rem;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 text-uppercase small fw-bold"
                                    style="color: {{ $card['icon_color'] === 'text-success' ? '#198754' : ($card['icon_color'] === 'text-primary' ? '#0d6efd' : ($card['icon_color'] === 'text-danger' ? '#dc3545' : ($card['icon_color'] === 'text-info' ? '#0dcaf0' : ($card['icon_color'] === 'text-warning' ? '#ffc107' : '#6c757d')))) }};">
                                    {{ $card['label'] }}
                                </h6>
                                <h3 class="mb-0 mt-1 text-white fw-bold">
                                    {{ $card['value'] }}
                                    @if (isset($card['unit']))
                                        <small class="text-white-50" style="font-size: 0.7rem;">{{ $card['unit'] }}</small>
                                    @endif
                                </h3>
                                <small class="text-white-50"
                                    style="font-size: 0.65rem; font-family: 'Segoe UI', sans-serif;">
                                    {{ $card['label_ar'] }}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-black bg-opacity-10 border-0 py-2 px-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-white-50" style="font-size: 0.7rem;">
                                {{ $card['meta'] }}
                            </small>
                            @if (isset($card['trend']))
                                <span class="badge {{ $card['trend_positive'] ? 'bg-success' : 'bg-danger' }} px-2 py-1"
                                    style="font-size: 0.65rem;">
                                    <i class="bi bi-{{ $card['trend_positive'] ? 'arrow-up' : 'arrow-down' }}"></i>
                                    {{ $card['trend'] }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-lg h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">
                                <i class="bi bi-activity me-2 text-primary"></i>
                                Consumption Trend
                            </h5>
                            <small class="text-muted" style="font-family: 'Segoe UI', sans-serif;">اتجاه الاستهلاك - آخر 12
                                شهراً</small>
                        </div>
                        <span class="badge bg-primary">12 Months</span>
                    </div>
                </div>
                <div class="card-body">
                    @if (array_sum($consumptionTrend['series']) > 0)
                        <canvas id="consumptionTrendChart" height="260"></canvas>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-bar-chart" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="mt-3 mb-1 fw-semibold">No consumption data available</p>
                            <p class="small mb-0" style="font-family: 'Segoe UI', sans-serif;">لا توجد بيانات استهلاك متاحة
                                للفترة المحددة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-lg h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-pie-chart-fill me-2 text-info"></i>
                        Company Distribution
                    </h5>
                    <small class="text-muted" style="font-family: 'Segoe UI', sans-serif;">توزيع الشركات حسب عدد
                        الخدمات</small>
                </div>
                <div class="card-body">
                    @if (array_sum($companyBreakdown['series']) > 0)
                        <canvas id="companyDistributionChart" height="260"></canvas>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-pie-chart" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="mt-3 mb-1 fw-semibold">No company data</p>
                            <p class="small mb-0" style="font-family: 'Segoe UI', sans-serif;">أضف شركات المياه لرؤية
                                التوزيع</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-lg h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-geo-alt-fill me-2 text-success"></i>
                        Governorate Coverage
                    </h5>
                    <small class="text-muted" style="font-family: 'Segoe UI', sans-serif;">التغطية حسب المحافظات</small>
                </div>
                <div class="card-body">
                    @if (array_sum($governorateBreakdown['series']) > 0)
                        <canvas id="governorateDistributionChart" height="320"></canvas>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-geo" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="mt-3 mb-1 fw-semibold">No governorate data</p>
                            <p class="small mb-0" style="font-family: 'Segoe UI', sans-serif;">ستظهر البيانات عند ربط
                                الخدمات بالمواقع</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-lg h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">
                                <i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>
                                Outstanding Bills
                            </h5>
                            <small class="text-muted" style="font-family: 'Segoe UI', sans-serif;">أعلى 5 فواتير
                                مستحقة</small>
                        </div>
                        <span class="badge bg-danger">Top 5</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="ps-3">
                                        Service
                                        <small class="text-muted d-block fw-normal"
                                            style="font-size: 0.7rem;">الخدمة</small>
                                    </th>
                                    <th scope="col">
                                        Company
                                        <small class="text-muted d-block fw-normal"
                                            style="font-size: 0.7rem;">الشركة</small>
                                    </th>
                                    <th scope="col" class="text-end pe-3">
                                        Amount
                                        <small class="text-muted d-block fw-normal"
                                            style="font-size: 0.7rem;">المبلغ</small>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($outstandingServices as $entry)
                                    @php
                                        $service = $entry['service'];
                                        $building = $service->building;
                                    @endphp
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-semibold text-primary">{{ $service->registration_number }}
                                            </div>
                                            <div class="text-muted small">
                                                {{ $building?->name ?? 'Unknown building' }}
                                                @if ($entry['last_reading_at'])
                                                    <span class="d-block" style="font-size: 0.7rem;">
                                                        <i
                                                            class="bi bi-clock me-1"></i>{{ $entry['last_reading_at']->format('d M Y') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">
                                                {{ $service->waterCompany?->name ?? ($service->company_name ?? 'N/A') }}
                                            </div>
                                            @if ($service->waterCompany?->name_ar)
                                                <div class="text-muted small"
                                                    style="font-family: 'Segoe UI', sans-serif;">
                                                    {{ $service->waterCompany->name_ar }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3">
                                            <div class="fw-bold text-danger mb-1" style="font-size: 1.1rem;">
                                                {{ number_format($entry['total_due'], 2) }}
                                                <small class="text-muted fw-normal">JOD</small>
                                            </div>
                                            <a href="{{ route('water-services.show', $service->id) }}"
                                                class="btn btn-sm btn-orange">
                                                <i class="bi bi-eye me-1"></i>View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            <i class="bi bi-check-circle text-success fs-3"></i>
                                            <p class="mb-1 mt-2">All bills are settled!</p>
                                            <small style="font-family: 'Segoe UI', sans-serif;">جميع الفواتير مسددة</small>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">
                                <i class="bi bi-clock-history me-2 text-secondary"></i>
                                Recent Readings
                            </h5>
                            <small class="text-muted" style="font-family: 'Segoe UI', sans-serif;">آخر القراءات
                                المسجلة</small>
                        </div>
                        <span class="badge bg-secondary">Latest 6</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="ps-3">
                                        Date
                                        <small class="text-muted d-block fw-normal"
                                            style="font-size: 0.7rem;">التاريخ</small>
                                    </th>
                                    <th scope="col">
                                        Service
                                        <small class="text-muted d-block fw-normal"
                                            style="font-size: 0.7rem;">الخدمة</small>
                                    </th>
                                    <th scope="col">
                                        Consumption
                                        <small class="text-muted d-block fw-normal"
                                            style="font-size: 0.7rem;">الاستهلاك</small>
                                    </th>
                                    <th scope="col">
                                        Bill Amount
                                        <small class="text-muted d-block fw-normal" style="font-size: 0.7rem;">قيمة
                                            الفاتورة</small>
                                    </th>
                                    <th scope="col" class="pe-3">
                                        Status
                                        <small class="text-muted d-block fw-normal"
                                            style="font-size: 0.7rem;">الحالة</small>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentReadings as $reading)
                                    @php
                                        $service = $reading->waterService;
                                        $readingDate = $reading->reading_date ?? $reading->created_at;
                                    @endphp
                                    <tr>
                                        <td class="ps-3">
                                            <span
                                                class="fw-semibold">{{ optional($readingDate)->format('d M Y') ?? 'N/A' }}</span>
                                            <div class="text-muted small" style="font-size: 0.75rem;">
                                                {{ $service?->registration_number ?? 'Unknown' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-primary">
                                                {{ $service?->building?->name ?? 'N/A' }}
                                            </div>
                                            <div class="text-muted small">
                                                {{ $service?->waterCompany?->name ?? ($service?->company_name ?? 'Unknown') }}
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="fw-bold text-info">{{ number_format((float) $reading->consumption_value, 2) }}</span>
                                            <small class="text-muted">m³</small>
                                            <div class="text-muted small" style="font-size: 0.75rem;">
                                                Reading: {{ number_format((float) $reading->current_reading, 2) }}
                                            </div>
                                        </td>
                                        <td>
                                            @if ($reading->bill_amount)
                                                <span
                                                    class="fw-bold">{{ number_format((float) $reading->bill_amount, 2) }}</span>
                                                <small class="text-muted">JOD</small>
                                            @else
                                                <span class="badge bg-light text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="pe-3">
                                            @if ($reading->is_paid)
                                                <span class="badge bg-success px-3 py-2">
                                                    <i class="bi bi-check-circle me-1"></i>Paid
                                                </span>
                                            @else
                                                <span class="badge bg-warning px-3 py-2">
                                                    <i class="bi bi-clock me-1"></i>Pending
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">
                                            <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                            <p class="mt-3 mb-1 fw-semibold">No readings recorded yet</p>
                                            <small style="font-family: 'Segoe UI', sans-serif;">لم يتم تسجيل أي قراءات
                                                بعد</small>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .btn-orange {
            background-color: #ff7900 !important;
            border-color: #ff7900 !important;
            color: white !important;
        }

        .btn-orange:hover {
            background-color: #e56b00 !important;
            border-color: #e56b00 !important;
            color: white !important;
        }

        .btn-outline-orange {
            background-color: transparent !important;
            border-color: #ff7900 !important;
            color: #ff7900 !important;
        }

        .btn-outline-orange:hover {
            background-color: #ff7900 !important;
            border-color: #ff7900 !important;
            color: white !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartColors = [
                '#0d6efd', '#198754', '#fd7e14', '#dc3545', '#6f42c1',
                '#20c997', '#ffc107', '#0dcaf0', '#d63384', '#6c757d'
            ];

            const consumptionData = @json($consumptionTrend);
            const companyData = @json($companyBreakdown);
            const governorateData = @json($governorateBreakdown);

            // Consumption Trend Chart - Enhanced
            const consumptionCtx = document.getElementById('consumptionTrendChart');
            if (consumptionCtx && Array.isArray(consumptionData?.labels) && consumptionData.labels.length) {
                new Chart(consumptionCtx, {
                    type: 'line',
                    data: {
                        labels: consumptionData.labels,
                        datasets: [{
                            label: 'Consumption (m³)',
                            data: consumptionData.series,
                            fill: true,
                            tension: 0.4,
                            backgroundColor: 'rgba(13, 110, 253, 0.1)',
                            borderColor: '#0d6efd',
                            borderWidth: 3,
                            pointBackgroundColor: '#0d6efd',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointHoverBackgroundColor: '#0d6efd',
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 3,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index',
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                borderColor: '#0d6efd',
                                borderWidth: 1,
                                callbacks: {
                                    label: function(context) {
                                        return `Consumption: ${context.parsed.y.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })} m³`;
                                    },
                                },
                            },
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)',
                                    drawBorder: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    callback: function(value) {
                                        return value.toLocaleString() + ' m³';
                                    },
                                },
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                        },
                    },
                });
            }

            // Company Distribution Chart - Enhanced
            const companyCtx = document.getElementById('companyDistributionChart');
            if (companyCtx && Array.isArray(companyData?.labels) && companyData.labels.length) {
                new Chart(companyCtx, {
                    type: 'doughnut',
                    data: {
                        labels: companyData.labels,
                        datasets: [{
                            data: companyData.series,
                            backgroundColor: chartColors.slice(0, companyData.labels.length),
                            borderWidth: 3,
                            borderColor: '#fff',
                            hoverBorderColor: '#fff',
                            hoverBorderWidth: 4,
                            hoverOffset: 8,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 15,
                                    padding: 15,
                                    font: {
                                        size: 12
                                    },
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                },
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return `${label}: ${value} services (${percentage}%)`;
                                    }
                                }
                            },
                        },
                    },
                });
            }

            // Governorate Distribution Chart - Enhanced
            const governorateCtx = document.getElementById('governorateDistributionChart');
            if (governorateCtx && Array.isArray(governorateData?.labels) && governorateData.labels.length) {
                new Chart(governorateCtx, {
                    type: 'bar',
                    data: {
                        labels: governorateData.labels,
                        datasets: [{
                            label: 'Services',
                            data: governorateData.series,
                            backgroundColor: '#198754',
                            borderRadius: 8,
                            maxBarThickness: 40,
                            hoverBackgroundColor: '#157347',
                        }],
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                callbacks: {
                                    label: function(context) {
                                        return `Services: ${context.parsed.x.toLocaleString()}`;
                                    }
                                }
                            },
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)',
                                    drawBorder: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    callback: function(value) {
                                        return value.toLocaleString();
                                    },
                                },
                            },
                            y: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 11,
                                        weight: '500'
                                    }
                                }
                            },
                        },
                    },
                });
            }
        });
    </script>
@endpush
