<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $users = User::orderBy('role')->orderBy('name')->get();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => User::roleLabels(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(User::allowedRoles())],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.settings.users.index')
            ->with('success', 'Akun pengguna berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'userAccount' => $user,
            'roles' => User::roleLabels(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(User::allowedRoles())],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $newRole = $validated['role'];
        $this->ensureCanChangeRole($user, $newRole, $request->user());

        $user->name = $validated['name'];
        $user->email = strtolower($validated['email']);
        $user->role = $newRole;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.settings.users.index')
            ->with('success', 'Akun pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->id === $user->id) {
            return back()->withErrors(['user' => 'Anda tidak dapat menghapus akun Anda sendiri.']);
        }

        if ($user->isSuperAdmin() && User::where('role', User::ROLE_SUPER_ADMIN)->count() <= 1) {
            return back()->withErrors(['user' => 'Tidak dapat menghapus super admin terakhir.']);
        }

        $user->delete();

        return redirect()->route('admin.settings.users.index')
            ->with('success', 'Akun pengguna berhasil dihapus.');
    }

    public function resetPassword(User $user): RedirectResponse
    {
        $newPassword = Str::upper(Str::random(10));
        $user->password = Hash::make($newPassword);
        $user->save();

        return back()->with('success', "Password {$user->email} berhasil direset. Password baru: {$newPassword}");
    }

    private function ensureCanChangeRole(User $targetUser, string $newRole, User $actor): void
    {
        if ($targetUser->id === $actor->id && $newRole === User::ROLE_USER) {
            throw ValidationException::withMessages([
                'role' => 'Anda tidak dapat menurunkan akses akun Anda sendiri menjadi user biasa.',
            ]);
        }

        $isDowngradeSuper = $targetUser->role === User::ROLE_SUPER_ADMIN
            && $newRole !== User::ROLE_SUPER_ADMIN;

        if ($isDowngradeSuper && User::where('role', User::ROLE_SUPER_ADMIN)->count() <= 1) {
            throw ValidationException::withMessages([
                'role' => 'Tidak dapat mengubah role super admin terakhir.',
            ]);
        }
    }
}
