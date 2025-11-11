@extends('layouts.guest')

@section('image-content')
    <div class="w-100 h-100 d-flex align-items-center justify-content-center" style="background-color: #000;">
        <img src="{{ asset('assets/images/login.png') }}" alt="Reset Password" class="img-fluid"
            style="max-height: 100%; max-width: 100%; object-fit: contain;">
    </div>
@endsection

@section('content')
    <div class="login-wrapper">
        <h2 class="text-center mb-3">Forgot Password</h2>
        <p class="text-center text-muted mb-4">
            Enter your email address below and we will send you a secure link to reset your password.
        </p>

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="mt-4">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">{{ __('Email address') }}</label>
                <input id="email" class="form-control @error('email') is-invalid @enderror" type="email"
                    name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('login') }}" class="text-decoration-none small">
                    <i class="bi bi-arrow-left me-1"></i>Back to login
                </a>
                <button type="submit" class="btn btn-primary">
                    {{ __('Email Password Reset Link') }}
                </button>
            </div>
        </form>
    </div>
@endsection
