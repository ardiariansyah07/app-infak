<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\PasswordPolicy;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('q')->toString();
        $roles = $this->roles();

        $data = User::orderBy('role')
            ->when($search !== '', function ($query) use ($search, $roles) {
                $matchingRoles = collect($roles)
                    ->filter(fn ($label, $value) => str_contains(strtolower($label), strtolower($search)) || str_contains(strtolower($value), strtolower($search)))
                    ->keys()
                    ->values();

                $query->where(function ($query) use ($search, $matchingRoles) {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->when($matchingRoles->isNotEmpty(), fn ($query) => $query->orWhereIn('role', $matchingRoles));
                });
            })
            ->orderBy('name')
            ->paginate(100)
            ->withQueryString();

        return view('admin.user.index', compact('data', 'roles', 'search'));
    }

    public function create()
    {
        $user = new User;
        $roles = $this->roles();
        $action = route('admin.user.store');
        $method = 'POST';

        return view('admin.user.form', compact('user', 'roles', 'action', 'method'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(array_keys($this->roles()))],
            'password' => ['required', PasswordPolicy::rule(), 'confirmed'],
        ]);

        User::create($validated);

        return redirect()
            ->route('admin.user.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    public function edit(User $user)
    {
        $roles = $this->roles();
        $action = route('admin.user.update', $user);
        $method = 'PUT';

        return view('admin.user.form', compact('user', 'roles', 'action', 'method'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(array_keys($this->roles()))],
            'password' => ['nullable', PasswordPolicy::rule(), 'confirmed'],
        ]);

        if (blank($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('admin.user.index')
            ->with('success', 'User berhasil diperbarui');
    }

    public function destroy(Request $request, User $user)
    {
        if ($request->user()->is($user)) {
            return back()->with('error', 'User yang sedang login tidak bisa dihapus');
        }

        $user->delete();

        return redirect()
            ->route('admin.user.index')
            ->with('success', 'User berhasil dihapus');
    }

    private function roles(): array
    {
        return [
            User::ROLE_ADMIN => 'Admin Utama',
            User::ROLE_PETUGAS => 'Petugas Infak',
            User::ROLE_PEMBIMBING => 'Pembimbing Rayon/Guru',
            User::ROLE_ORANG_TUA => 'Siswa/Keluarga',
        ];
    }
}
