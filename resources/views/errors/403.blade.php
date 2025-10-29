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
            color: #ff7900;
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
            color: #ff7900;
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
        }
    </style>
</head>

<body>
    <div class="error-container">
        <div class="error-card">
            <div class="error-image-section">
                <i class="bi bi-shield-lock error-icon"></i>
            </div>

            <div class="error-content-section">
                <div class="error-code">403</div>
                <h1 class="error-title">Access Forbidden</h1>
                <p class="error-message">
                    You don't have the necessary privileges to perform this action. This page or feature is restricted
                    to users with specific permissions.
                </p>

                <div>
                    <a href="{{ route('dashboard') }}" class="btn-back">
                        <i class="bi bi-house-door"></i>
                        Back to Dashboard
                    </a>
                </div>

                <div class="additional-info">
                    <div class="info-item">
                        <i class="bi bi-info-circle"></i>
                        <small>If you believe this is an error, please contact your system administrator.</small>
                    </div>
                    @auth
                        <div class="info-item">
                            <i class="bi bi-person-badge"></i>
                            <small>Logged in as: <strong>{{ auth()->user()->email }}</strong></small>
                        </div>
                        <div class="info-item">
                            <i class="bi bi-shield-check"></i>
                            <small>Role: <strong>{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</strong></small>
                        </div>
                        @if (auth()->user()->role !== 'super_admin' && !empty(auth()->user()->privileges))
                            <div class="info-item">
                                <i class="bi bi-key"></i>
                                <small>Your Privileges:
                                    <strong>
                                        @php
                                            $privilegeLabels = [
                                                'sites_lands_buildings' => 'Sites, Lands & Buildings',
                                                'water' => 'Water Services',
                                                'electricity' => 'Electricity Services',
                                                'renovation' => 'Renovations',
                                            ];
                                            $userPrivileges = collect(auth()->user()->privileges)
                                                ->map(
                                                    fn($priv) => $privilegeLabels[$priv] ??
                                                        ucfirst(str_replace('_', ' ', $priv)),
                                                )
                                                ->implode(', ');
                                        @endphp
                                        {{ $userPrivileges }}
                                    </strong>
                                </small>
                            </div>
                        @elseif(auth()->user()->role === 'super_admin')
                            <div class="info-item">
                                <i class="bi bi-key"></i>
                                <small>Your Privileges: <strong>Full Access</strong></small>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/boosted@5.3.5/dist/js/boosted.bundle.min.js"></script>
</body>

</html>
