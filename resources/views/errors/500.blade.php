<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.head')
    <style>
        body {
            background-color: black !important;
            background-image: url("https://www.transparenttextures.com/patterns/worn-dots.png");
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px 0;
        }

        .error-container {
            max-width: 900px;
            width: 100%;
            padding: 1rem;
        }

        .error-card {
            border: none !important;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            overflow: hidden;
            width: 100%;
            display: flex;
            flex-direction: row;
            background-color: white;
        }

        .error-image-section {
            flex: 0 0 45%;
            position: relative;
            background-color: black;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .error-content-section {
            flex: 0 0 55%;
            display: flex;
            flex-direction: column;
            padding: 3rem 2rem;
            justify-content: center;
        }

        .error-code {
            font-size: 6rem;
            font-weight: 700;
            color: #dc3545;
            line-height: 1;
            margin-bottom: 1rem;
        }

        .error-title {
            font-size: 2rem;
            font-weight: 600;
            color: #000;
            margin-bottom: 1rem;
        }

        .error-message {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .error-icon {
            font-size: 8rem;
            color: #dc3545;
            opacity: 0.9;
        }

        .btn-back {
            background-color: #ff7900;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 5px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background-color: #e66d00;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 121, 0, 0.3);
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 5px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-left: 1rem;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
        }

        .additional-info {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e9ecef;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            color: #6c757d;
        }

        @media (max-width: 768px) {
            .error-card {
                flex-direction: column;
            }

            .error-image-section,
            .error-content-section {
                flex: 0 0 100%;
            }

            .error-image-section {
                min-height: 200px;
                padding: 1.5rem;
            }

            .error-content-section {
                padding: 2rem 1.5rem;
            }

            .error-code {
                font-size: 4rem;
            }

            .error-title {
                font-size: 1.5rem;
            }

            .error-message {
                font-size: 1rem;
            }

            .error-icon {
                font-size: 5rem;
            }

            .btn-secondary {
                margin-left: 0;
                margin-top: 0.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="error-container">
        <div class="error-card">
            <div class="error-image-section">
                <i class="bi bi-exclamation-triangle error-icon"></i>
            </div>

            <div class="error-content-section">
                <div class="error-code">500</div>
                <h1 class="error-title">Server Error</h1>
                <p class="error-message">
                    Something went wrong on our end. We're working to fix the issue. Please try again later.
                </p>

                <div>
                    <a href="{{ route('dashboard') }}" class="btn-back">
                        <i class="bi bi-house-door"></i>
                        Back to Dashboard
                    </a>
                    <a href="javascript:window.location.reload()" class="btn-secondary">
                        <i class="bi bi-arrow-clockwise"></i>
                        Reload Page
                    </a>
                </div>

                <div class="additional-info">
                    <div class="info-item">
                        <i class="bi bi-info-circle"></i>
                        <small>If this error continues, please contact technical support.</small>
                    </div>
                    <div class="info-item">
                        <i class="bi bi-clock"></i>
                        <small>Error occurred at: <strong>{{ now()->format('Y-m-d H:i:s') }}</strong></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/boosted@5.3.5/dist/js/boosted.bundle.min.js"></script>
</body>

</html>
