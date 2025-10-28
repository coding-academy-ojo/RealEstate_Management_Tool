@extends('layouts.app')

@section('title', 'Edit User')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Admin Management</a></li>
    <li class="breadcrumb-item active">Edit User</li>
@endsection

@section('content')
    <div class="mb-4">
        <h2 class="mb-1">Edit User</h2>
        <p class="text-muted mb-0">Update user details, change their role, and adjust access as needed.</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.users.update', $admin) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.users._form', ['admin' => $admin, 'availableRoles' => $availableRoles])

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light">
                        <i class="bi bi-arrow-left me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-orange">
                        <i class="bi bi-check-circle me-1"></i> Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
