<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.head')
    @yield('styles')
</head>

<body>
    @include('layouts.partials.navbar')

    <div class="app-wrapper">
        <div class="sidebar-overlay"></div>

        @include('layouts.partials.sidebar')

        <main id="content" >
            @include('layouts.partials.breadcrumbs')

            <div class="main-content">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Tooltip container for sidebar tooltips -->
    <div id="sidebar-tooltip" class="sidebar-tooltip-container"></div>

    @include('layouts.partials.scripts')
</body>

</html>
