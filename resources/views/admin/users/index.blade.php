@extends('layouts.app')

@section('title', 'User Management')

@section('breadcrumbs')
    <li class="breadcrumb-item active">User Management</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Admin &amp; Engineer Management</h2>
            <small class="text-muted">Review privileged users, their roles, and their access rights.</small>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-orange">
            <i class="bi bi-plus-circle me-1"></i> Add User
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col" style="width: 12%">Role</th>
                            <th scope="col" style="width: 28%">Privileges</th>
                            <th scope="col">Created</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($admins as $admin)
                            @php
                                $isSuperAdmin = $admin->role === 'super_admin';
                                $isAdmin = $admin->role === 'admin';
                                $isAdminOrAbove = $isSuperAdmin || $isAdmin;

                                $roleLabel = match($admin->role) {
                                    'super_admin' => 'Super Admin',
                                    'admin' => 'Admin',
                                    default => 'Engineer',
                                };

                                $privilegeText = '';

                                if ($isAdminOrAbove) {
                                    $privilegeText = 'Full Access';
                                } elseif (!empty($admin->privileges)) {
                                    $privilegeText = collect($admin->privileges)
                                        ->map(
                                            fn($privilege) => $privilegeLabels[$privilege] ??
                                                \Illuminate\Support\Str::headline(str_replace('_', ' ', $privilege)),
                                        )
                                        ->implode(', ');
                                }
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $admin->name }}</td>
                                <td>{{ $admin->email }}</td>
                                <td>
                                    <span class="badge {{ $isSuperAdmin ? 'bg-danger' : ($isAdmin ? 'bg-primary' : 'bg-secondary') }}">
                                        {{ $roleLabel }}
                                    </span>
                                </td>
                                <td>
                                    @if ($privilegeText !== '')
                                        {{ $privilegeText }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $admin->created_at?->format('d M Y') }}<br>
                                        {{ $admin->created_at?->format('H:i') }}
                                    </small>
                                </td>
                                <td class="text-end">
                                    @if ($isSuperAdmin)
                                        <span class="text-muted">—</span>
                                    @else
                                        <div class="d-inline-flex gap-2">
                                            <a href="{{ route('admin.users.edit', $admin) }}"
                                                class="btn btn-sm btn-outline-orange">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.users.destroy', $admin) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-people" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="mt-3 mb-0">No privileged users found.</p>
                                    <small class="text-muted">Create the first engineer to get started.</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($admins->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $admins->links() }}
            </div>
        @endif
    </div>
@endsection
