@extends('layouts.guest')

@section('image-content')
    <div class="w-100 h-100 d-flex align-items-center justify-content-center" style="background-color: #000;">
        <img src="{{ asset('assets/images/login.png') }}" alt="Reset Password" class="img-fluid"
            style="max-height: 100%; max-width: 100%; object-fit: contain;">
    </div>
@endsection

@section('content')
    <div class="login-wrapper">
        <h2 class="text-center mb-3">Set a New Password</h2>
        <p class="text-center text-muted mb-4">
            Choose a strong password to secure your Orange Real Estate account.
        </p>

        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="mb-3">
                <label for="email" class="form-label">{{ __('Email address') }}</label>
                <input id="email" class="form-control @error('email') is-invalid @enderror" type="email"
                    name="email" value="{{ old('email', $request->email) }}" required readonly>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">{{ __('New password') }}</label>
                <input id="password" class="form-control @error('password') is-invalid @enderror" type="password"
                    name="password" required autocomplete="new-password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label">{{ __('Confirm new password') }}</label>
                <input id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror"
                    type="password" name="password_confirmation" required autocomplete="new-password">
                @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('login') }}" class="text-decoration-none small">
                    <i class="bi bi-arrow-left me-1"></i>Back to login
                </a>
                <button type="submit" class="btn btn-primary">
                    {{ __('Reset Password') }}
                </button>
            </div>
        </form>
    </div>
@endsection
