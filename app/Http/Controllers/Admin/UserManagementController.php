<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\AdminWelcomeMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * Available privilege keys that can be assigned to engineers.
     */
    private const PRIVILEGES = [
        'sites_lands_buildings' => 'Sites, Lands & Buildings',
        'water' => 'Water Services',
        'electricity' => 'Electricity Services',
        'renovation' => 'Renovations',
    ];

    private const ROLES = [
        'engineer' => 'Engineer',
        'admin' => 'Admin',
    ];

    private function authorizeSuperAdmin(): void
    {
        $user = Auth::user();

        abort_unless($user instanceof User && $user->isSuperAdmin(), 403);
    }

    /**
     * Display a listing of the admin users (engineers and admins).
     */
    public function index()
    {
        $this->authorizeSuperAdmin();

        $admins = User::whereIn('role', ['super_admin', 'admin', 'engineer'])
            ->orderByRaw("CASE WHEN role = 'super_admin' THEN 0 WHEN role = 'admin' THEN 1 ELSE 2 END")
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.users.index', [
            'admins' => $admins,
            'availablePrivileges' => array_keys(self::PRIVILEGES),
            'privilegeLabels' => self::PRIVILEGES,
            'availableRoles' => self::ROLES,
        ]);
    }

    /**
     * Show the form for creating a new admin user.
     */
    public function create()
    {
        $this->authorizeSuperAdmin();

        return view('admin.users.create', [
            'availablePrivileges' => array_keys(self::PRIVILEGES),
            'privilegeLabels' => self::PRIVILEGES,
            'availableRoles' => self::ROLES,
        ]);
    }

    /**
     * Store a newly created admin user.
     */
    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(array_keys(self::ROLES))],
            'privileges' => ['nullable', 'array'],
            'privileges.*' => ['string', Rule::in(array_keys(self::PRIVILEGES))],
        ]);

        $plainPassword = $data['password'];
        $role = $data['role'];

        // Admin role gets all privileges (stored as null like super_admin)
        // Engineer role gets specific privileges from the form
        $privileges = in_array($role, ['super_admin', 'admin'])
            ? null
            : $this->normalizePrivileges($data['privileges'] ?? []);

        $friendlyPrivileges = null;
        if (is_array($privileges)) {
            $friendlyPrivileges = array_map(fn ($key) => self::PRIVILEGES[$key] ?? $key, $privileges);
        }

        unset($data['privileges']);
        unset($data['role']);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $role,
            'privileges' => $privileges,
        ]);

        Mail::to($user->email)->send(new AdminWelcomeMail($user, $plainPassword, $friendlyPrivileges));

        $roleLabel = self::ROLES[$user->role] ?? ucfirst(str_replace('_', ' ', $user->role));

        return redirect()
            ->route('admin.users.index')
            ->with('success', "{$roleLabel} {$user->name} created successfully.");
    }

    /**
     * Show the form for editing the specified admin user.
     */
    public function edit(User $user)
    {
        $this->authorizeSuperAdmin();

        return view('admin.users.edit', [
            'admin' => $user,
            'availablePrivileges' => array_keys(self::PRIVILEGES),
            'privilegeLabels' => self::PRIVILEGES,
            'availableRoles' => self::ROLES,
        ]);
    }

    /**
     * Update the specified admin user in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->authorizeSuperAdmin();

        abort_if($user->isSuperAdmin(), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(array_keys(self::ROLES))],
            'privileges' => ['nullable', 'array'],
            'privileges.*' => ['string', Rule::in(array_keys(self::PRIVILEGES))],
        ]);

        $role = $data['role'];
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $role,
            // Admin role gets all privileges (stored as null like super_admin)
            // Engineer role gets specific privileges from the form
            'privileges' => in_array($role, ['super_admin', 'admin'])
                ? null
                : $this->normalizePrivileges($data['privileges'] ?? []),
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        $roleLabel = self::ROLES[$user->role] ?? ucfirst(str_replace('_', ' ', $user->role));

        return redirect()
            ->route('admin.users.index')
            ->with('success', "{$roleLabel} {$user->name} updated successfully.");
    }

    /**
     * Ensure privilege combinations remain consistent before persisting.
     */
    private function normalizePrivileges(array $privileges): array
    {
        $privileges = array_values(array_unique($privileges));

        if (in_array('sites_lands_buildings', $privileges, true)) {
            return ['sites_lands_buildings'];
        }

        return $privileges;
    }

    /**
     * Remove the specified admin user from storage.
     */
    public function destroy(User $user)
    {
        $this->authorizeSuperAdmin();

        abort_if($user->isSuperAdmin(), 403);

        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Engineer {$name} deleted successfully.");
    }
}
