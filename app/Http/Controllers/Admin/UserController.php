<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $users = User::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'customer'])],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user = User::create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'password' => $validated['password'],
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active'),
        ]);

        ActivityLogger::log('user.created', $user, 'Admin membuat user baru.', [
            'user_email' => $user->email,
            'role' => $user->role,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'customer'])],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $payload = [
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active'),
        ];

        if (filled($validated['password'] ?? null)) {
            $payload['password'] = $validated['password'];
        }

        $user->update($payload);

        ActivityLogger::log('user.updated', $user, 'Admin memperbarui user.', [
            'user_email' => $user->email,
            'role' => $user->role,
        ]);

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ((int) $request->user()->id === (int) $user->id) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Akun yang sedang dipakai tidak bisa dihapus.');
        }

        $email = $user->email;
        $user->delete();

        ActivityLogger::log('user.deleted', $user, 'Admin menghapus user.', [
            'user_email' => $email,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}

