<div class="card border-0 shadow-sm h-100">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="bi bi-key me-2 text-orange"></i>Change Password</h5>
        <small class="text-muted">Use a strong password to keep your account secure.</small>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            @method('put')

            <div class="mb-3">
                <label for="current_password" class="form-label">{{ __('Current password') }}</label>
                <input id="current_password" name="current_password" type="password"
                    class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                    autocomplete="current-password">
                @error('current_password', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">{{ __('New password') }}</label>
                <input id="password" name="password" type="password"
                    class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                    autocomplete="new-password">
                @error('password', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label">{{ __('Confirm new password') }}</label>
                <input id="password_confirmation" name="password_confirmation" type="password"
                    class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                    autocomplete="new-password">
                @error('password_confirmation', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            @if (session('status') === 'password-updated')
                <div class="alert alert-success" role="alert">
                    <i class="bi bi-check-circle me-1"></i>{{ __('Password updated successfully.') }}
                </div>
            @endif

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>{{ __('Update password') }}
                </button>
            </div>
        </form>
    </div>
</div>
