@extends('layouts.app')

@section('title', 'Real Estate Dashboard')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card dashboard-card border-0 shadow-lg h-100" style="background-color: #1c1c1c !important;">
                <div class="card-body text-white py-3 px-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-white bg-opacity-25 rounded-circle p-2">
                                <i class="bi bi-geo-alt-fill" style="font-size: 1.4rem; color: #ff7900;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-uppercase small fw-bold" style="color: #ff7900;">Sites</h6>
                            <h3 class="mb-0 mt-1 text-white fw-bold">{{ $stats['total_sites'] }}</h3>
                            <small class="text-white-50" style="font-size: 0.7rem;">
                                <i class="bi bi-arrow-up me-1"></i>
                                {{ $stats['total_sites'] }} Total Properties
                            </small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-black bg-opacity-10 border-0 py-2 px-3">
                    <a href="{{ route('sites.index') }}" class="btn btn-sm text-decoration-none"
                        style="background-color: #ff7900; color: white; border: none;">
                        <i class="bi bi-eye me-1"></i> Manage Sites
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card dashboard-card border-0 shadow-lg h-100" style="background-color: #1c1c1c !important;">
                <div class="card-body text-white py-3 px-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-white bg-opacity-25 rounded-circle p-2">
                                <i class="bi bi-map-fill" style="font-size: 1.4rem; color: #ff7900;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-uppercase small fw-bold" style="color: #ff7900;">Lands</h6>
                            <h3 class="mb-0 mt-1 text-white fw-bold">{{ $stats['total_lands'] }}</h3>
                            <small class="text-white-50" style="font-size: 0.7rem;">
                                <i class="bi bi-geo-alt me-1"></i>
                                Land parcels registered
                            </small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-black bg-opacity-10 border-0 py-2 px-3">
                    <a href="{{ route('lands.index') }}" class="btn btn-sm text-decoration-none"
                        style="background-color: #ff7900; color: white; border: none;">
                        <i class="bi bi-eye me-1"></i> Manage Lands
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card dashboard-card border-0 shadow-lg h-100" style="background-color: #1c1c1c !important;">
                <div class="card-body text-white py-3 px-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-white bg-opacity-25 rounded-circle p-2">
                                <i class="bi bi-building-fill" style="font-size: 1.4rem; color: #ff7900;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-uppercase small fw-bold" style="color: #ff7900;">Buildings</h6>
                            <h3 class="mb-0 mt-1 text-white fw-bold">{{ $stats['total_buildings'] }}</h3>
                            <small class="text-white-50" style="font-size: 0.7rem;">
                                <i class="bi bi-building me-1"></i>
                                {{ $stats['buildings_with_permit'] }} with permits
                            </small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-black bg-opacity-10 border-0 py-2 px-3">
                    <a href="{{ route('buildings.index') }}" class="btn btn-sm text-decoration-none"
                        style="background-color: #ff7900; color: white; border: none;">
                        <i class="bi bi-eye me-1"></i> Manage Buildings
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card dashboard-card border-0 shadow-lg h-100" style="background-color: #1c1c1c !important;">
                <div class="card-body text-white py-3 px-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-white bg-opacity-25 rounded-circle p-2">
                                <i class="bi bi-lightbulb-fill" style="font-size: 1.4rem; color: #ff7900;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-uppercase small fw-bold" style="color: #ff7900;">Renovations</h6>
                            <h3 class="mb-0 mt-1 text-white fw-bold">{{ $stats['total_innovations'] }}</h3>
                            <small class="text-white-50" style="font-size: 0.7rem;">
                                <i class="bi bi-currency-dollar me-1"></i>
                                {{ number_format($stats['total_innovation_cost'], 0) }} JOD invested
                            </small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-black bg-opacity-10 border-0 py-2 px-3">
                    <a href="{{ route('renovations.index') }}" class="btn btn-sm text-decoration-none"
                        style="background-color: #ff7900; color: white; border: none;">
                        <i class="bi bi-eye me-1"></i> View Innovations
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-lg h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="bi bi-map-fill me-2 text-orange"></i>
                            Jordan Interactive Map
                        </h5>
                        {{-- <span class="badge bg-orange">Live Data</span> --}}
                    </div>
                </div>
                <div class="card-body p-2">
                    @include('components.jordan-map', ['data' => $mapData])
                </div>
            </div>
        </div>
    </div>

    <!-- Services Overview Section - Full Width -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-gear-wide-connected me-2 text-orange"></i>
                        Services Overview
                    </h5>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <!-- Water Services Section -->
                        <div class="col-lg-4 mb-3 mb-lg-0">
                            <div class="card h-100 border-0 shadow-lg" style="background-color: #1c1c1c !important;">
                                <div class="card-body p-4 text-white">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-white bg-opacity-25 rounded-circle p-2 me-3">
                                            <i class="bi bi-droplet-fill" style="font-size: 1.5rem; color: #0d6efd;"></i>
                                        </div>
                                        <h6 class="mb-0 text-uppercase fw-bold" style="color: #0d6efd;">Water Services</h6>
                                    </div>
                                    <h2 class="mb-1 text-white fw-bold">{{ $stats['total_water_services'] }}</h2>
                                    <small class="text-white-50 d-block mb-3" style="font-size: 0.75rem;">Active connections</small>

                                    <div class="border-top border-secondary pt-3 mt-3">
                                        <div class="row text-center">
                                            <div class="col-4 border-end border-secondary">
                                                <div class="text-white-50 small" style="font-size: 0.7rem;">Readings</div>
                                                <div class="fw-bold text-white" style="font-size: 0.9rem;">{{ number_format($stats['total_water_readings']) }}</div>
                                            </div>
                                            <div class="col-4 border-end border-secondary">
                                                <div class="text-white-50 small" style="font-size: 0.7rem;">Unpaid</div>
                                                <div class="fw-bold text-warning" style="font-size: 0.9rem;">{{ number_format($stats['unpaid_water_bills']) }}</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="text-white-50 small" style="font-size: 0.7rem;">Outstanding</div>
                                                <div class="fw-bold text-danger" style="font-size: 0.85rem;">{{ number_format($stats['total_water_outstanding'], 0) }} <small class="text-white-50" style="font-size: 0.6rem;">JOD</small></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-black bg-opacity-10 border-0 py-2 px-3">
                                    <a href="{{ route('water.overview') }}" class="btn btn-sm text-decoration-none w-100"
                                        style="background-color: #0d6efd; color: white; border: none;">
                                        <i class="bi bi-arrow-right me-1"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Electricity Services Section -->
                        <div class="col-lg-4 mb-3 mb-lg-0">
                            <div class="card h-100 border-0 shadow-lg" style="background-color: #1c1c1c !important;">
                                <div class="card-body p-4 text-white">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-white bg-opacity-25 rounded-circle p-2 me-3">
                                            <i class="bi bi-lightning-charge-fill" style="font-size: 1.5rem; color: #ffc107;"></i>
                                        </div>
                                        <h6 class="mb-0 text-uppercase fw-bold" style="color: #ffc107;">Electricity Services</h6>
                                    </div>
                                    <h2 class="mb-1 text-white fw-bold">{{ $stats['total_electricity_services'] }}</h2>
                                    <small class="text-white-50 d-block mb-3" style="font-size: 0.75rem;">Power connections</small>

                                    <div class="border-top border-secondary pt-3 mt-3">
                                        <div class="row text-center">
                                            <div class="col-4 border-end border-secondary">
                                                <div class="text-white-50 small" style="font-size: 0.7rem;">Readings</div>
                                                <div class="fw-bold text-white" style="font-size: 0.9rem;">{{ number_format($stats['total_electricity_readings']) }}</div>
                                            </div>
                                            <div class="col-4 border-end border-secondary">
                                                <div class="text-white-50 small" style="font-size: 0.7rem;">Unpaid</div>
                                                <div class="fw-bold text-warning" style="font-size: 0.9rem;">{{ number_format($stats['unpaid_electricity_bills']) }}</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="text-white-50 small" style="font-size: 0.7rem;">Outstanding</div>
                                                <div class="fw-bold text-danger" style="font-size: 0.85rem;">{{ number_format($stats['total_electricity_outstanding'], 0) }} <small class="text-white-50" style="font-size: 0.6rem;">JOD</small></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-black bg-opacity-10 border-0 py-2 px-3">
                                    <a href="{{ route('electricity.overview') }}" class="btn btn-sm text-decoration-none w-100"
                                        style="background-color: #ffc107; color: #000; border: none;">
                                        <i class="bi bi-arrow-right me-1"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Renovations Section -->
                        <div class="col-lg-4">
                            <div class="card h-100 border-0 shadow-lg" style="background-color: #1c1c1c !important;">
                                <div class="card-body p-4 text-white">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-white bg-opacity-25 rounded-circle p-2 me-3">
                                            <i class="bi bi-lightbulb-fill" style="font-size: 1.5rem; color: #ff7900;"></i>
                                        </div>
                                        <h6 class="mb-0 text-uppercase fw-bold" style="color: #ff7900;">Renovations</h6>
                                    </div>
                                    <h2 class="mb-1 text-white fw-bold">{{ $stats['total_innovations'] }}</h2>
                                    <small class="text-white-50 d-block mb-3" style="font-size: 0.75rem;">Total projects</small>

                                    <div class="border-top border-secondary pt-3 mt-3">
                                        <div class="row text-center">
                                            <div class="col-12">
                                                <div class="text-white-50 small" style="font-size: 0.7rem;">Total Investment</div>
                                                <div class="fw-bold text-success" style="font-size: 1.1rem;">{{ number_format($stats['total_innovation_cost'], 0) }} <small class="text-white-50">JOD</small></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-black bg-opacity-10 border-0 py-2 px-3">
                                    <a href="{{ route('renovations.index') }}" class="btn btn-sm text-decoration-none w-100"
                                        style="background-color: #ff7900; color: white; border: none;">
                                        <i class="bi bi-arrow-right me-1"></i> View Projects
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section - Full Width -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-clock-history me-2 text-orange"></i>
                        Recent Activity
                    </h5>
                    <small class="text-muted">Latest updates across all modules</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-3 py-2" style="width: 5%;"></th>
                                    <th class="py-2" style="width: 35%;">Activity</th>
                                    <th class="py-2" style="width: 15%;">Type</th>
                                    <th class="py-2" style="width: 20%;">User</th>
                                    <th class="py-2 text-end pe-3" style="width: 25%;">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stats['recent_activities']->take(5) as $activity)
                                    <tr class="activity-row-dashboard" style="cursor: pointer;" onclick="window.location='{{ $activity['route'] }}'">
                                        <td class="px-3">
                                            <div class="bg-{{ $activity['color'] }} bg-opacity-10 rounded-circle p-1"
                                                style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-{{ $activity['icon'] }} text-{{ $activity['color'] }}"
                                                    style="font-size: 0.75rem;"></i>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark" style="font-size: 0.875rem;">{{ $activity['title'] }}</div>
                                        </td>
                                        <td>
                                            <span class="badge"
                                                style="font-size: 0.65rem; padding: 0.2rem 0.5rem;
                                                @if($activity['type'] === 'site') background-color: #ff7900;
                                                @elseif($activity['type'] === 'building') background-color: #6c757d;
                                                @elseif($activity['type'] === 'land') background-color: #198754;
                                                @elseif(str_contains($activity['type'], 'water')) background-color: #0d6efd;
                                                @elseif(str_contains($activity['type'], 'electric')) background-color: #ffc107; color: #000;
                                                @elseif($activity['type'] === 'innovation') background-color: #dc3545;
                                                @elseif($activity['type'] === 'image') background-color: #6f42c1;
                                                @elseif($activity['type'] === 'zoning') background-color: #20c997;
                                                @else background-color: #adb5bd;
                                                @endif">
                                                @if($activity['type'] === 'site') Site
                                                @elseif($activity['type'] === 'building') Building
                                                @elseif($activity['type'] === 'land') Land
                                                @elseif($activity['type'] === 'water') Water
                                                @elseif($activity['type'] === 'water_reading') Reading
                                                @elseif($activity['type'] === 'water_company') Company
                                                @elseif($activity['type'] === 'electricity') Electricity
                                                @elseif($activity['type'] === 'electric_reading') Reading
                                                @elseif($activity['type'] === 'electricity_company') Company
                                                @elseif($activity['type'] === 'disconnection') Disconnection
                                                @elseif($activity['type'] === 'innovation') Renovation
                                                @elseif($activity['type'] === 'image') Image
                                                @elseif($activity['type'] === 'zoning') Zoning
                                                @else {{ ucfirst($activity['type']) }}
                                                @endif
                                            </span>
                                            @if ($activity['action'] === 'created')
                                                <span class="badge bg-success ms-1" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;">New</span>
                                            @elseif($activity['action'] === 'updated')
                                                <span class="badge bg-primary ms-1" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;">Edit</span>
                                            @elseif($activity['action'] === 'deleted')
                                                <span class="badge bg-danger ms-1" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;">Del</span>
                                            @elseif($activity['action'] === 'restored')
                                                <span class="badge bg-info ms-1" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;">Restore</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="fw-semibold text-dark" style="font-size: 0.75rem;">{{ $activity['subtitle'] }}</small>
                                        </td>
                                        <td class="text-end pe-3">
                                            <small class="text-muted" style="font-size: 0.7rem;">{{ $activity['timestamp']->diffForHumans() }}</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.3;"></i>
                                            <p class="text-muted mt-2 mb-0 small">No recent activities found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Showing last 5 activities
                        </small>
                        <a href="{{ route('activities.index') }}" class="btn btn-orange btn-sm">
                            <i class="bi bi-list-ul me-1"></i>View All Activities
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Chart.js Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Modern Chart.js Configuration
        Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
        Chart.defaults.color = '#6b7280';
        Chart.defaults.plugins.legend.display = true;

        // Add interactive hover effects
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.transition = 'all 0.3s ease';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Animate counters on page load
        function animateCounters() {
            const counters = document.querySelectorAll('h2.fw-bold');
            counters.forEach(counter => {
                const target = parseInt(counter.textContent);
                const increment = target / 50;
                let current = 0;

                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        counter.textContent = target;
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.ceil(current);
                    }
                }, 30);
            });
        }

        // Initialize animations when page loads
        document.addEventListener('DOMContentLoaded', animateCounters);
    </script>

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
            background-color: #ff7900 !important;
            border-color: #ff7900 !important;
            color: white !important;
        }

        .btn-outline-orange:hover {
            background-color: #e56b00 !important;
            border-color: #e56b00 !important;
            color: white !important;
        }

        .btn-outline-orange.active {
            background-color: #ff7900 !important;
            border-color: #ff7900 !important;
            color: white !important;
        }

        .btn-primary {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
        }

        .btn-success {
            background-color: #198754 !important;
            border-color: #198754 !important;
        }

        .btn-info {
            background-color: #0dcaf0 !important;
            border-color: #0dcaf0 !important;
        }

        .btn-warning {
            background-color: #ffc107 !important;
            border-color: #ffc107 !important;
            color: #000 !important;
        }

        .btn-danger {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
        }

        .list-group-item-action:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
            transition: all 0.3s ease;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .text-orange {
            color: #ff7900 !important;
        }

        .card {
            transition: all 0.3s ease;
        }

        .progress-bar {
            transition: width 0.8s ease;
        }

        .chart-container {
            position: relative;
            width: 100%;
            max-height: 400px;
        }

        canvas {
            max-height: 100% !important;
        }

        .activity-row-dashboard {
            transition: all 0.2s ease;
        }

        .activity-row-dashboard:hover {
            background-color: #f8f9fa;
        }
    </style>

@endsection
