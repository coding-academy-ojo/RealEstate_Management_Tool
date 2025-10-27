@extends('layouts.guest')

@section('image-content')
    <div class="w-100 h-100 d-flex align-items-center justify-content-center" style="background-color: #000;">
        <img src="{{ asset('assets/images/login.png') }}" alt="Real Estate Login" class="img-fluid"
            style="max-height: 100%; max-width: 100%; object-fit: contain;">
    </div>
@endsection

@section('content')
    <div class="login-wrapper">
        <h2 class="text-center mb-4">Welcome Back</h2>
        <p class="text-center text-muted mb-4">Sign in to your Real Estate Management account</p>

        <!-- Session Status -->
        @if (session('status'))
            <div class="alert alert-success mb-4" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="mb-3">
                <label for="email" class="form-label">{{ __('Email') }}</label>
                <input id="email" class="form-control @error('email') is-invalid @enderror" type="email"
                    name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label">{{ __('Password') }}</label>
                <input id="password" class="form-control @error('password') is-invalid @enderror" type="password"
                    name="password" required autocomplete="current-password">
                @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="form-check mb-3">
                <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                <label for="remember_me" class="form-check-label">
                    {{ __('Remember me') }}
                </label>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                @if (Route::has('password.request'))
                    <a class="text-decoration-none small" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <button type="submit" class="btn btn-primary">
                    {{ __('Log in') }}
                </button>
            </div>
        </form>
    </div>
@endsection
