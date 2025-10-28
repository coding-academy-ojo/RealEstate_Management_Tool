@php
    $selectedPrivileges = old('privileges', $admin->privileges ?? []);
    if (!is_array($selectedPrivileges)) {
        $selectedPrivileges = [];
    }
@endphp

@push('styles')
    <style>
        .privilege-checkbox .form-check-input {
            border-radius: 10px !important;
        }
    </style>
@endpush

<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" value="{{ old('name', $admin->name ?? '') }}"
            class="form-control @error('name') is-invalid @enderror" placeholder="Enter full name" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" id="email" value="{{ old('email', $admin->email ?? '') }}"
            class="form-control @error('email') is-invalid @enderror" placeholder="name@example.com" required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="role" class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
            @foreach ($availableRoles as $roleKey => $roleLabel)
                <option value="{{ $roleKey }}"
                    {{ old('role', $admin->role ?? 'engineer') === $roleKey ? 'selected' : '' }}>{{ $roleLabel }}
                </option>
            @endforeach
        </select>
        @error('role')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="password" class="form-label fw-semibold">
            Password @isset($admin)
            <small class="text-muted">(leave blank to keep current)</small>@else<span class="text-danger">*</span>
            @endisset
        </label>
        <input type="password" name="password" id="password"
            class="form-control @error('password') is-invalid @enderror" placeholder="Minimum 8 characters"
            @unless (isset($admin)) required @endunless>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="password_confirmation" class="form-label fw-semibold">Confirm Password
            @isset($admin)
            <small class="text-muted">(optional)</small>@else<span class="text-danger">*</span>
            @endisset
        </label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
            placeholder="Re-enter password" @unless (isset($admin)) required @endunless>
    </div>

    <div class="col-12" id="privileges-section">
        <label class="form-label fw-semibold">Privileges</label>
        <div class="d-flex flex-wrap gap-2 mb-3">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="select-all-privileges">
                <i class="bi bi-check2-all me-1"></i> Select All
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="clear-privileges">
                <i class="bi bi-x-circle me-1"></i> Clear
            </button>
        </div>

        <div class="row g-3">
            @foreach ($availablePrivileges as $privilege)
                @php
                    $inputId = 'privilege_' . $privilege;
                    $isChecked = in_array($privilege, $selectedPrivileges, true);
                    $label =
                        $privilegeLabels[$privilege] ??
                        \Illuminate\Support\Str::headline(str_replace('_', ' ', $privilege));
                @endphp
                <div class="col-md-4">
                    <div class="form-check privilege-checkbox">
                        <input class="form-check-input" type="checkbox" id="{{ $inputId }}" name="privileges[]"
                            value="{{ $privilege }}" {{ $isChecked ? 'checked' : '' }}>
                        <label class="form-check-label" for="{{ $inputId }}">
                            {{ $label }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>

        <small class="text-muted d-block mt-2" id="privileges-note">
            Selecting "Sites, Lands &amp; Buildings" automatically grants all available privileges.
        </small>
        <small class="text-muted d-block mt-2" id="super-admin-note" style="display: none;">
            Super Admins automatically receive full access; privilege options are disabled.
        </small>

        @error('privileges')
            <div class="text-danger small mt-2">{{ $message }}</div>
        @enderror
        @error('privileges.*')
            <div class="text-danger small mt-2">{{ $message }}</div>
        @enderror
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllBtn = document.getElementById('select-all-privileges');
            const clearBtn = document.getElementById('clear-privileges');
            const privilegeCheckboxes = Array.from(document.querySelectorAll('input[name="privileges[]"]'));
            const estateCheckbox = document.getElementById('privilege_sites_lands_buildings');
            const roleSelect = document.getElementById('role');
            const privilegesSection = document.getElementById('privileges-section');
            const privilegesNote = document.getElementById('privileges-note');
            const superAdminNote = document.getElementById('super-admin-note');

            const toggleAll = (checked) => {
                privilegeCheckboxes.forEach(checkbox => {
                    checkbox.checked = checked;
                });
            };

            if (selectAllBtn && clearBtn) {
                selectAllBtn.addEventListener('click', function() {
                    toggleAll(true);
                });

                clearBtn.addEventListener('click', function() {
                    toggleAll(false);
                });
            }

            if (estateCheckbox) {
                estateCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        toggleAll(true);
                    }
                });

                privilegeCheckboxes
                    .filter(checkbox => checkbox !== estateCheckbox)
                    .forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            if (!this.checked && estateCheckbox.checked) {
                                estateCheckbox.checked = false;
                            }
                        });
                    });
            }

            const syncPrivilegesForRole = () => {
                if (!roleSelect) {
                    return;
                }

                const isSuperAdmin = roleSelect.value === 'super_admin';

                privilegeCheckboxes.forEach(checkbox => {
                    checkbox.disabled = isSuperAdmin;
                    if (isSuperAdmin) {
                        checkbox.checked = false;
                    }
                });

                if (selectAllBtn) {
                    selectAllBtn.disabled = isSuperAdmin;
                }
                if (clearBtn) {
                    clearBtn.disabled = isSuperAdmin;
                }

                privilegesSection.classList.toggle('opacity-50', isSuperAdmin);
                if (privilegesNote) {
                    privilegesNote.style.display = isSuperAdmin ? 'none' : 'block';
                }
                if (superAdminNote) {
                    superAdminNote.style.display = isSuperAdmin ? 'block' : 'none';
                }
            };

            syncPrivilegesForRole();

            if (roleSelect) {
                roleSelect.addEventListener('change', syncPrivilegesForRole);
            }
        });
    </script>
@endpush
