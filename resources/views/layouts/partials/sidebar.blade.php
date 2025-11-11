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

        .sidebar-dropdown {
            display: flex;
            flex-direction: column;
        }

        .sidebar-dropdown .dropdown-toggle {
            width: 100%;
            text-align: left;
            cursor: pointer;
            background: transparent;
            border: none;
            padding: 12px 16px;
            color: #f1f1f1;
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
            margin: 2px 0;
        }

        .sidebar-dropdown .dropdown-toggle::after {
            display: none !important;
        }

        .sidebar-dropdown .dropdown-toggle:hover {
            background: rgba(255, 121, 0, 0.15);
            border-left: 3px solid var(--orange-primary);
            color: white;
        }

        .sidebar-dropdown .dropdown-toggle.active {
            background: rgba(255, 121, 0, 0.25);
            border-left: 3px solid var(--orange-primary);
            color: white;
        }

        .sidebar-dropdown .dropdown-toggle i:first-child {
            margin-right: 12px;
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
        }

        .sidebar-dropdown .chevron {
            margin-left: auto;
            margin-right: 8px;
            font-size: 0.75rem;
            transition: transform 0.2s ease;
        }

        .sidebar-dropdown.open .chevron {
            transform: rotate(180deg);
        }

        .sidebar-submenu {
            display: none;
            flex-direction: column;
            gap: 0;
            background: rgba(0, 0, 0, 0.2);
            margin: 0;
            padding: 0;
        }

        .sidebar-dropdown.open .sidebar-submenu {
            display: flex;
        }

        .sidebar-sublink {
            display: flex;
            align-items: center;
            gap: 0;
            padding: 12px 16px 12px 45px;
            color: #f1f1f1;
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
            margin: 0;
            font-size: 0.95rem;
        }

        .sidebar-sublink i {
            margin-right: 12px;
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
            transition: transform 0.2s;
        }

        .sidebar-sublink:hover {
            background: rgba(255, 121, 0, 0.15);
            border-left: 3px solid var(--orange-primary);
            color: white;
        }

        .sidebar-sublink:hover i {
            transform: translateX(2px);
            color: var(--orange-primary);
        }

        .sidebar-sublink.active {
            background: rgba(255, 121, 0, 0.25);
            border-left: 3px solid var(--orange-primary);
            color: white;
        }

        .sidebar-sublink.active i {
            color: var(--orange-primary);
        }

        #sidebar.minimized .sidebar-dropdown .sidebar-submenu {
            display: none !important;
        }

        #sidebar.minimized .sidebar-dropdown .chevron {
            display: none;
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
    </div>

    <div class="sidebar-section">
        <h6 class="sidebar-title">PROPERTIES</h6>

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

        @php
            $waterSectionActive = request()->routeIs('water.overview') ||
                request()->routeIs('water.services.index') ||
                request()->routeIs('water-services.*') ||
                request()->routeIs('water.bills.index') ||
                request()->routeIs('water.companies.*');

            $electricitySectionActive = request()->routeIs('electricity.overview') ||
                request()->routeIs('electricity.services.index') ||
                request()->routeIs('electric.*') ||
                request()->routeIs('electricity.bills.index') ||
                request()->routeIs('electricity.companies.*');
        @endphp

        <div class="sidebar-dropdown {{ $waterSectionActive ? 'open' : '' }}">
            <button type="button"
                class="sidebar-link dropdown-toggle {{ $waterSectionActive ? 'active' : '' }}"
                data-sidebar-toggle="water"
                data-tooltip="Water Services"
                aria-expanded="{{ $waterSectionActive ? 'true' : 'false' }}">
                <i class="bi bi-droplet"></i>
                <span>Water Services</span>
                <i class="bi bi-chevron-down chevron"></i>
            </button>

            <div class="sidebar-submenu">
                <a href="{{ route('water.overview') }}"
                    class="sidebar-sublink {{ request()->routeIs('water.overview') ? 'active' : '' }}">
                    <i class="bi bi-graph-up"></i>
                    <span>Water Overview</span>
                </a>
                <a href="{{ route('water.services.index') }}"
                    class="sidebar-sublink {{ request()->routeIs('water.services.index') || request()->routeIs('water-services.*') ? 'active' : '' }}">
                    <i class="bi bi-list-ul"></i>
                    <span>Water Services</span>
                </a>
                <a href="{{ route('water.bills.index') }}"
                    class="sidebar-sublink {{ request()->routeIs('water.bills.index') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i>
                    <span>Water Bills</span>
                </a>
                <a href="{{ route('water.companies.index') }}"
                    class="sidebar-sublink {{ request()->routeIs('water.companies.*') ? 'active' : '' }}">
                    <i class="bi bi-building-gear"></i>
                    <span>Water Companies</span>
                </a>
            </div>
        </div>

        <div class="sidebar-dropdown {{ $electricitySectionActive ? 'open' : '' }}">
            <button type="button"
                class="sidebar-link dropdown-toggle {{ $electricitySectionActive ? 'active' : '' }}"
                data-sidebar-toggle="electricity"
                data-tooltip="Electricity Services"
                aria-expanded="{{ $electricitySectionActive ? 'true' : 'false' }}">
                <i class="bi bi-lightning-charge"></i>
                <span>Electricity Services</span>
                <i class="bi bi-chevron-down chevron"></i>
            </button>

            <div class="sidebar-submenu">
                <a href="{{ route('electricity.overview') }}"
                    class="sidebar-sublink {{ request()->routeIs('electricity.overview') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Electricity Overview</span>
                </a>
                <a href="{{ route('electricity.services.index') }}"
                    class="sidebar-sublink {{ request()->routeIs('electricity.services.index') || request()->routeIs('electric.*') ? 'active' : '' }}">
                    <i class="bi bi-list-ul"></i>
                    <span>Electricity Services</span>
                </a>
                <a href="{{ route('electricity.bills.index') }}"
                    class="sidebar-sublink {{ request()->routeIs('electricity.bills.index') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i>
                    <span>Electricity Bills</span>
                </a>
                <a href="{{ route('electricity.companies.index') }}"
                    class="sidebar-sublink {{ request()->routeIs('electricity.companies.*') ? 'active' : '' }}">
                    <i class="bi bi-building-gear"></i>
                    <span>Electricity Companies</span>
                </a>
            </div>
        </div>

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

        <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" data-tooltip="Exports & Reports">
            <i class="bi bi-file-earmark-spreadsheet"></i>
            <span>Exports & Reports</span>
        </a>

        <a href="#" class="sidebar-link" data-tooltip="Settings">
            <i class="bi bi-gear"></i>
            <span>Settings</span>
        </a>
    </div>
</aside>

<!-- Floating dropdown menu for minimized sidebar -->
<div id="floating-water-menu" class="floating-sidebar-menu" style="display: none; position: fixed; background: #1c1c1c; min-width: 240px; border-radius: 8px; box-shadow: 4px 4px 20px rgba(0, 0, 0, 0.5); z-index: 9999; padding: 0.5rem 0; border-left: 3px solid #ff7900;">
    <a href="{{ route('water.overview') }}"
        class="sidebar-sublink {{ request()->routeIs('water.overview') ? 'active' : '' }}"
        style="display: flex; align-items: center; padding: 12px 20px; color: #f1f1f1; text-decoration: none; border-left: 3px solid transparent; transition: all 0.2s ease;">
        <i class="bi bi-graph-up" style="margin-right: 12px; font-size: 1.1rem; width: 24px; text-align: center;"></i>
        <span>Water Overview</span>
    </a>
    <a href="{{ route('water.services.index') }}"
        class="sidebar-sublink {{ request()->routeIs('water.services.index') || request()->routeIs('water-services.*') ? 'active' : '' }}"
        style="display: flex; align-items: center; padding: 12px 20px; color: #f1f1f1; text-decoration: none; border-left: 3px solid transparent; transition: all 0.2s ease;">
        <i class="bi bi-list-ul" style="margin-right: 12px; font-size: 1.1rem; width: 24px; text-align: center;"></i>
        <span>Water Services</span>
    </a>
    <a href="{{ route('water.bills.index') }}"
        class="sidebar-sublink {{ request()->routeIs('water.bills.index') ? 'active' : '' }}"
        style="display: flex; align-items: center; padding: 12px 20px; color: #f1f1f1; text-decoration: none; border-left: 3px solid transparent; transition: all 0.2s ease;">
        <i class="bi bi-receipt" style="margin-right: 12px; font-size: 1.1rem; width: 24px; text-align: center;"></i>
        <span>Water Bills</span>
    </a>
    <a href="{{ route('water.companies.index') }}"
        class="sidebar-sublink {{ request()->routeIs('water.companies.*') ? 'active' : '' }}"
        style="display: flex; align-items: center; padding: 12px 20px; color: #f1f1f1; text-decoration: none; border-left: 3px solid transparent; transition: all 0.2s ease;">
        <i class="bi bi-building-gear" style="margin-right: 12px; font-size: 1.1rem; width: 24px; text-align: center;"></i>
        <span>Water Companies</span>
    </a>
</div>

<div id="floating-electricity-menu" class="floating-sidebar-menu" style="display: none; position: fixed; background: #1c1c1c; min-width: 240px; border-radius: 8px; box-shadow: 4px 4px 20px rgba(0, 0, 0, 0.5); z-index: 9999; padding: 0.5rem 0; border-left: 3px solid #ff7900;">
    <a href="{{ route('electricity.overview') }}"
        class="sidebar-sublink {{ request()->routeIs('electricity.overview') ? 'active' : '' }}"
        style="display: flex; align-items: center; padding: 12px 20px; color: #f1f1f1; text-decoration: none; border-left: 3px solid transparent; transition: all 0.2s ease;">
        <i class="bi bi-speedometer2" style="margin-right: 12px; font-size: 1.1rem; width: 24px; text-align: center;"></i>
        <span>Electricity Overview</span>
    </a>
    <a href="{{ route('electricity.services.index') }}"
        class="sidebar-sublink {{ request()->routeIs('electricity.services.index') || request()->routeIs('electric.*') ? 'active' : '' }}"
        style="display: flex; align-items: center; padding: 12px 20px; color: #f1f1f1; text-decoration: none; border-left: 3px solid transparent; transition: all 0.2s ease;">
        <i class="bi bi-list-ul" style="margin-right: 12px; font-size: 1.1rem; width: 24px; text-align: center;"></i>
        <span>Electricity Services</span>
    </a>
    <a href="{{ route('electricity.bills.index') }}"
        class="sidebar-sublink {{ request()->routeIs('electricity.bills.index') ? 'active' : '' }}"
        style="display: flex; align-items: center; padding: 12px 20px; color: #f1f1f1; text-decoration: none; border-left: 3px solid transparent; transition: all 0.2s ease;">
        <i class="bi bi-receipt" style="margin-right: 12px; font-size: 1.1rem; width: 24px; text-align: center;"></i>
        <span>Electricity Bills</span>
    </a>
    <a href="{{ route('electricity.companies.index') }}"
        class="sidebar-sublink {{ request()->routeIs('electricity.companies.*') ? 'active' : '' }}"
        style="display: flex; align-items: center; padding: 12px 20px; color: #f1f1f1; text-decoration: none; border-left: 3px solid transparent; transition: all 0.2s ease;">
        <i class="bi bi-building-gear" style="margin-right: 12px; font-size: 1.1rem; width: 24px; text-align: center;"></i>
        <span>Electricity Companies</span>
    </a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const dropdownButtons = document.querySelectorAll('#sidebar [data-sidebar-toggle]');
        const floatingMenus = Array.from(document.querySelectorAll('.floating-sidebar-menu'));

        function hideFloatingMenus(exceptMenu) {
            floatingMenus.forEach(function(menu) {
                if (!exceptMenu || menu !== exceptMenu) {
                    menu.style.display = 'none';
                }
            });
        }

        dropdownButtons.forEach(function(button) {
            const container = button.closest('.sidebar-dropdown');
            if (!container) {
                return;
            }

            const submenu = container.querySelector('.sidebar-submenu');
            if (!submenu) {
                return;
            }

            const toggleKey = button.getAttribute('data-sidebar-toggle');
            const floatingMenu = toggleKey ? document.getElementById(`floating-${toggleKey}-menu`) : null;

            button.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();

                const isMinimized = sidebar.classList.contains('minimized');

                if (isMinimized && floatingMenu) {
                    const rect = button.getBoundingClientRect();
                    const isVisible = floatingMenu.style.display === 'block';

                    if (isVisible) {
                        floatingMenu.style.display = 'none';
                    } else {
                        hideFloatingMenus(floatingMenu);
                        floatingMenu.style.top = rect.top + 'px';
                        floatingMenu.style.left = '70px';
                        floatingMenu.style.display = 'block';
                    }
                } else {
                    const isOpen = container.classList.toggle('open');
                    button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

                    if (isOpen) {
                        dropdownButtons.forEach(function(otherButton) {
                            if (otherButton === button) {
                                return;
                            }
                            const otherContainer = otherButton.closest('.sidebar-dropdown');
                            if (otherContainer) {
                                otherContainer.classList.remove('open');
                                otherButton.setAttribute('aria-expanded', 'false');
                            }
                        });
                    }
                }
            });

            document.addEventListener('click', function(e) {
                if (!floatingMenu || floatingMenu.style.display !== 'block') {
                    return;
                }

                if (!container.contains(e.target) && !floatingMenu.contains(e.target)) {
                    floatingMenu.style.display = 'none';
                }
            });
        });
    });
</script>
