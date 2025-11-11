<div class="card border-0 shadow-sm h-100">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="bi bi-person me-2 text-orange"></i>Account Information</h5>
        <small class="text-muted">Update your name and email address.</small>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('patch')

            <div class="mb-3">
                <label for="name" class="form-label">{{ __('Full name') }}</label>
                <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $user->name) }}" required autocomplete="name">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">{{ __('Email address') }}</label>
                <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email', $user->email) }}" required autocomplete="username">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            @if (session('status') === 'profile-updated')
                <div class="alert alert-success" role="alert">
                    <i class="bi bi-check-circle me-1"></i>{{ __('Profile updated successfully.') }}
                </div>
            @endif

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-orange">
                    <i class="bi bi-check-circle me-1"></i>{{ __('Save changes') }}
                </button>
            </div>
        </form>
    </div>
</div>
