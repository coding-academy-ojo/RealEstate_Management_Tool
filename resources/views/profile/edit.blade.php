@extends('layouts.app')

@section('content')
    <div class="container-fluid p-0">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">My Profile</h2>
                <p class="text-muted mb-0">Manage your personal details and update your password.</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-lg-6">
                @include('profile.partials.update-profile-information-form')
            </div>
            <div class="col-12 col-lg-6">
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>
@endsection
