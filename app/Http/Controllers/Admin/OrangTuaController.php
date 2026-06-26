<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrangTua;
use App\Models\User;
use Illuminate\Http\Request;

class OrangTuaController extends Controller
{
    public function index()
    {
        $data = OrangTua::with('user', 'siswa')
            ->orderBy('nama')
            ->paginate(100);

        return view('admin.orang_tua.index', compact('data'));
    }

    public function create()
    {
        return view('admin.orang_tua.form', [
            'orangTua' => new OrangTua,
            'users' => User::where('role', 'orang_tua')->orderBy('name')->get(),
            'action' => route('admin.orang-tua.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'nama' => ['required', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255', 'unique:orang_tua,email'],
        ]);

        $validated['password'] = '-';

        OrangTua::create($validated);

        return redirect()->route('admin.orang-tua.index')->with('success', 'Orang tua berhasil disimpan');
    }

    public function edit(OrangTua $orangTua)
    {
        return view('admin.orang_tua.form', [
            'orangTua' => $orangTua,
            'users' => User::where('role', 'orang_tua')->orderBy('name')->get(),
            'action' => route('admin.orang-tua.update', $orangTua),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, OrangTua $orangTua)
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'nama' => ['required', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255', 'unique:orang_tua,email,'.$orangTua->id],
        ]);

        $orangTua->update($validated);

        return redirect()->route('admin.orang-tua.index')->with('success', 'Orang tua berhasil diperbarui');
    }

    public function destroy(OrangTua $orangTua)
    {
        $orangTua->delete();

        return redirect()->route('admin.orang-tua.index')->with('success', 'Orang tua berhasil dihapus');
    }
}
