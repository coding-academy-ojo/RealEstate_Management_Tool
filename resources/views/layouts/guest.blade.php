<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.head')
    <!-- Additional auth-specific styles -->
    <style>
        body {
            background-color: black !important;
            background-image: url("https://www.transparenttextures.com/patterns/worn-dots.png");
            display: flex;
            align-items: center;
            /* Center for normal login pages */
            justify-content: center;
            min-height: 100vh;
            /* Changed from fixed height to minimum height */
            padding: 20px 0;
            /* Add padding for better spacing */
        }

        /* Activation pages specific adjustments - override centering */
        body.activation-page {
            align-items: flex-start !important;
            /* Start from top for activation pages */
        }

        .auth-container {
            max-width: 1000px;
            width: 100%;
            padding: 1rem;
        }

        .auth-card {
            border: none !important;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            overflow: hidden;
            width: 100%;
            display: flex;
            /* Make the card display as flex */
            flex-direction: row;
            /* Ensure row direction for left/right layout */
        }

        .auth-image-section {
            flex: 0 0 50%;
            /* Take up 50% of the card's width */
            position: relative;
            background-color: black;
            /* Set background color to black */
            overflow: hidden;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-header-mobile {
            background-color: black;
            color: white;
            padding: 0.8rem 1.5rem;
            display: none;
            /* Hidden by default on desktop */
            align-items: center;
            justify-content: flex-start;
            width: 100%;
        }

        .auth-form-section {
            flex: 0 0 50%;
            /* Take up 50% of the card's width */
            display: flex;
            flex-direction: column;
        }

        .auth-header {
            background-color: black;
            color: white;
            padding: 0.8rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }

        .auth-body {
            flex: 1;
            background-color: white;
            padding: 1.5rem;
        }

        .logo-container {
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: auto;
            background-color: #ffffff;
            border-radius: 8px;
        }

        .logo-container img {
            max-height: 50px;
            height: 50px;
            width: auto;
            display: block;
            /* Ensure the image is displayed as a block element */
        }

        .auth-header h4 {
            margin: 0;
            font-size: 1.2rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            body {
                padding: 10px 0;
                /* Reduced padding for mobile */
                align-items: flex-start;
                /* Align to top on mobile for better content fit */
            }

            .auth-container {
                padding: 0.5rem;
                /* Reduced container padding */
            }

            .auth-card {
                flex-direction: column;
                /* Stack vertically on smaller screens */
                min-height: auto;
                /* Allow natural height */
            }

            .auth-image-section,
            .auth-form-section {
                flex: 0 0 100%;
                /* Take full width on smaller screens */
            }

            .auth-image-section {
                min-height: 200px;
                /* Reduced height for mobile */
                order: 2;
                /* Make image second on small screens */
            }

            .auth-form-section {
                order: 3;
                /* Make form third on small screens */
            }

            .auth-header {
                display: none;
                /* Hide the desktop header on small screens */
            }

            .auth-header-mobile {
                display: flex;
                /* Show the mobile header on small screens */
                order: 1;
                /* Keep mobile header at the top */
            }
        }
    </style>
</head>

<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <!-- For small screens, we need a different layout structure -->
            <div class="auth-header-mobile">
                <div class="logo-container">
                    <img src="{{ asset('assets/images/orange-logo.svg') }}" alt="Logo">
                </div>
                <h4>{{ config('app.name', 'Real Estate') }}</h4>
            </div>

            <div class="auth-image-section">
                @hasSection('image-content')
                    @yield('image-content')
                @else
                    <div class="text-center p-4">
                        <i class="bi bi-building-fill text-white" style="font-size: 4rem;"></i>
                        <h3 class="text-white mt-3">Real Estate Management</h3>
                        <p class="text-white-50">Manage your properties efficiently</p>
                    </div>
                @endif
            </div>

            <div class="auth-form-section">
                <div class="auth-header">
                    <div class="logo-container">
                        <img src="{{ asset('assets/images/orange-logo.svg') }}" alt="Logo">
                    </div>
                    <h4>{{ config('app.name', 'Real Estate') }}</h4>
                </div>
                <div class="auth-body">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/boosted@5.3.5/dist/js/boosted.bundle.min.js"></script>
</body>

</html>
