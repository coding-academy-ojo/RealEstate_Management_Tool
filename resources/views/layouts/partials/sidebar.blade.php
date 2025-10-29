<aside id="sidebar">
    <style>
        .notification-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 18px;
            height: 18px;
            background-color: #dc3545;
            color: white;
            font-size: 10px;
            font-weight: 600;
            border-radius: 50%;
            position: absolute;
            top: 12px;
            right: 10px;
            z-index: 10;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
            animation: pulse 2s infinite;
            transition: all 0.3s ease;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Notification badge for minimized sidebar */
        #sidebar.minimized .notification-badge {
            top: 6px !important;
            right: 8px !important;
            min-width: 16px !important;
            height: 16px !important;
            font-size: 9px !important;
            border: 1px solid #fff !important;
            transform: scale(0.9) !important;
            display: inline-flex !important;
            animation: pulse-small 2s infinite !important;
        }

        .sidebar-link {
            position: relative;
            overflow: visible;
        }

        .sidebar-link.active .notification-badge {
            background-color: #fd7e14;
            animation: none !important;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
            }

            50% {
                box-shadow: 0 0 0 4px rgba(220, 53, 69, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
            }
        }

        @keyframes pulse-small {
            0% {
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
            }

            50% {
                box-shadow: 0 0 0 2px rgba(220, 53, 69, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
            }
        }

        /* Ensure notification badge stays visible in minimized mode */
        #sidebar.minimized .sidebar-link {
            position: relative;
            overflow: visible !important;
        }

        #sidebar.minimized .sidebar-link .notification-badge {
            position: absolute !important;
            z-index: 1000 !important;
            display: inline-flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Override the span hiding rule for notification badges */
        #sidebar.minimized .sidebar-link span.notification-badge {
            display: inline-flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
    </style>

    <div class="sidebar-header">
        <h5 class="m-0">Real Estate Dashboard</h5>
        <!-- Icon for minimized state -->
        <div class="sidebar-icon d-none">
            <i class="bi bi-building-fill" style="font-size: 1.5rem;"></i>
        </div>
    </div>

    <div class="sidebar-section">
        <h6 class="sidebar-title">NAVIGATION</h6>

        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
            data-tooltip="Dashboard">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('activities.index') }}"
            class="sidebar-link {{ request()->routeIs('activities.*') ? 'active' : '' }}" data-tooltip="Activities">
            <i class="bi bi-clock-history"></i>
            <span>Activities</span>
        </a>

        <a href="{{ route('sites.index') }}" class="sidebar-link {{ request()->routeIs('sites.*') ? 'active' : '' }}"
            data-tooltip="Sites">
            <i class="bi bi-geo-alt"></i>
            <span>Sites</span>
        </a>

        <a href="{{ route('lands.index') }}" class="sidebar-link {{ request()->routeIs('lands.*') ? 'active' : '' }}"
            data-tooltip="Lands">
            <i class="bi bi-map"></i>
            <span>Lands</span>
        </a>

        <a href="{{ route('buildings.index') }}"
            class="sidebar-link {{ request()->routeIs('buildings.*') ? 'active' : '' }}" data-tooltip="Buildings">
            <i class="bi bi-building"></i>
            <span>Buildings</span>
        </a>
    </div>

    <div class="sidebar-section">
        <h6 class="sidebar-title">SERVICES</h6>

        <a href="{{ route('water-services.index') }}"
            class="sidebar-link {{ request()->routeIs('water-services.*') ? 'active' : '' }}"
            data-tooltip="Water Services">
            <i class="bi bi-droplet"></i>
            <span>Water Services</span>
        </a>

        <a href="{{ route('electricity-services.index') }}"
            class="sidebar-link {{ request()->routeIs('electricity-services.*') ? 'active' : '' }}"
            data-tooltip="Electricity Services">
            <i class="bi bi-lightning-charge"></i>
            <span>Electricity</span>
        </a>

        <a href="{{ route('renovations.index') }}"
            class="sidebar-link {{ request()->routeIs('renovations.*') ? 'active' : '' }}"
            data-tooltip="Renovations">
            <i class="bi bi-lightbulb"></i>
            <span>Renovations</span>
        </a>
    </div>

    <div class="sidebar-section">
        <h6 class="sidebar-title">SYSTEM</h6>

        @if (auth()->check() && auth()->user()->isSuperAdmin())
            <a href="{{ route('admin.users.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" data-tooltip="Admin">
                <i class="bi bi-person-gear"></i>
                <span>Admin Management</span>
            </a>
        @endif

        <a href="#" class="sidebar-link" data-tooltip="Reports">
            <i class="bi bi-graph-up"></i>
            <span>Reports</span>
        </a>

        <a href="#" class="sidebar-link" data-tooltip="Settings">
            <i class="bi bi-gear"></i>
            <span>Settings</span>
        </a>
    </div>
</aside>
