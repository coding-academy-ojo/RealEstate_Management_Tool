<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <button id="sidebarToggle" class="btn p-2 sidebar-toggle-btn" aria-label="Toggle Sidebar"
        style="width: 75px; display: flex; align-items: center; justify-content: center; margin: 0; border-radius: 0;">
        <i class="bi bi-list fs-4"></i>
    </button>

    <div class="container-fluid d-flex justify-content-between align-items-center" style="padding-left: 15px;">
        <div class="d-flex align-items-center">
            <a class="navbar-brand my-0 d-flex align-items-center" href="{{ url('/') }}">
                <img src="{{ asset('assets/images/orange-logo.svg') }}" alt="Logo"
                    style="height: 35px; width: auto; margin-right: 10px;">
                <span class="logo-text" style="font-size: 1.5rem;">{{ config('app.name', 'Real Estate') }}</span>
            </a>
        </div>

        <div class="d-flex align-items-center">
            @auth
                @php
                    // Get notification counts - you can customize this based on your notification system
                    $unreadNotifications = [];
                    $unreadCount = 0;
                @endphp

                <!-- Notifications Dropdown -->
                <div class="dropdown notification-dropdown me-3">
                    <button class="btn position-relative p-2" data-bs-toggle="dropdown" aria-expanded="false"
                        style="color: #fff;">
                        <i class="bi bi-bell fs-5"></i>
                        @if ($unreadCount > 0)
                            <span class="position-absolute translate-middle badge rounded-pill bg-danger">
                                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                <span class="visually-hidden">unread messages</span>
                            </span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end notification-menu shadow-lg rounded-3"
                        style="width: 350px; max-height: 400px; overflow-y: auto;">
                        <li class="dropdown-header d-flex justify-content-between align-items-center py-2">
                            <span class="fw-bold">Notifications</span>
                            @if ($unreadCount > 0)
                                <small class="text-muted">{{ $unreadCount }} unread</small>
                            @endif
                        </li>

                        @forelse($unreadNotifications as $notification)
                            <!-- Notification items will be added here -->
                        @empty
                            <li class="px-3 py-4 text-center text-muted">
                                <i class="bi bi-bell-slash fs-1 d-block mb-2 opacity-50"></i>
                                <p class="mb-0">No new notifications</p>
                            </li>
                        @endforelse

                        @if (count($unreadNotifications) > 0)
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li class="text-center py-2">
                                <button class="btn btn-link btn-sm text-decoration-none" onclick="markAllAsRead()">
                                    <i class="bi bi-check2-all me-1"></i>Mark all as read
                                </button>
                            </li>
                        @endif
                    </ul>
                </div>

                <!-- User Dropdown -->
                <div class="dropdown user-dropdown">
                    <button class="btn dropdown-toggle p-2 p-sm-2 rounded-pill" data-bs-toggle="dropdown"
                        aria-expanded="false"
                        style="background-color: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2);">
                        <i class="bi bi-person-circle"></i>
                        <span class="d-none d-sm-inline">
                            {{ Auth::user()->name ?? 'User' }}
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end rounded-3">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i
                                    class="bi bi-person me-2"></i>Profile</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item"><i
                                        class="bi bi-box-arrow-right me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-light">Login</a>
            @endauth
        </div>
    </div>
</nav>

<style>
    .navbar {
        padding: 0 !important;
    }

    .sidebar-toggle-btn {
        transition: all 0.3s ease;
        color: #fff !important;
        height: 65px;
    }

    .sidebar-toggle-btn:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
    }

    /* When sidebar is minimized, adjust button width */
    #sidebar.minimized~.navbar #sidebarToggle {
        width: 70px !important;
    }

    @media (max-width: 768px) {
        .sidebar-toggle-btn {
            width: 60px !important;
        }
    }

    .notification-dropdown .dropdown-menu {
        border: 0;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .notification-item {
        border: none;
        transition: all 0.2s ease;
        cursor: pointer;
        text-decoration: none !important;
    }

    .notification-item:hover {
        background-color: rgba(253, 126, 20, 0.1);
        transform: translateX(3px);
    }

    .notification-item:active {
        background-color: rgba(253, 126, 20, 0.2);
    }

    .notification-item:focus {
        background-color: rgba(253, 126, 20, 0.1);
        outline: none;
    }

    .dropdown-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .notification-status-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(0, 0, 0, 0.05);
    }

    /* Add a subtle border-left to indicate clickable items */
    .notification-item {
        border-left: 3px solid transparent;
    }

    .notification-item:hover {
        border-left-color: #fd7e14;
    }

    /* Style the chevron icon */
    .notification-item .bi-chevron-right {
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .notification-item:hover .bi-chevron-right {
        opacity: 1;
    }

    /* User dropdown button styling */
    .user-dropdown .btn {
        transition: all 0.3s ease;
        color: #fff !important;
    }

    .user-dropdown .btn:hover {
        background-color: rgba(255, 255, 255, 0.2) !important;
        border-color: rgba(255, 255, 255, 0.3) !important;
        transform: translateY(-1px);
    }

    .user-dropdown .btn:focus {
        box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
    }

    /* Navbar positioning fix */
    .navbar {
        z-index: 1030;
    }

    body {
        padding-top: 56px;
        /* Account for fixed navbar */
    }
</style>
