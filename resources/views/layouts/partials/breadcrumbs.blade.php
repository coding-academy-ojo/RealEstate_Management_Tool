<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-white p-3 rounded-3 shadow-sm border-0">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}" class="text-decoration-none d-flex align-items-center">
                <i class="bi bi-house-door-fill me-1 text-orange"></i>
                <span>Home</span>
            </a>
        </li>
        <!-- Dynamic breadcrumb items can be added in specific views -->
        @yield('breadcrumbs')
        <!-- Fallback to show at least current section -->
        @hasSection('breadcrumbs')
        @else
            <li class="breadcrumb-item active fw-medium" aria-current="page">
                <span class="text-orange">@yield('title', 'Dashboard')</span>
            </li>
        @endif
    </ol>
</nav>
