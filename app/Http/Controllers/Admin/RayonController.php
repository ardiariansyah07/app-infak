<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Rayon;
use Illuminate\Http\Request;

class RayonController extends Controller
{
    public function index()
    {
        $data = Rayon::with('guru')->latest()->get();

        return view('admin.rayon.index', compact('data'));
    }

    public function create()
    {
        $guru = Guru::where('aktif', true)->orderBy('nama')->get();

        return view('admin.rayon.form', [
            'rayon' => new Rayon,
            'guru' => $guru,
            'action' => route('admin.rayon.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:rayon,nama'],
            'guru_id' => ['required', 'exists:guru,id'],
        ]);

        Rayon::create($validated);

        return redirect()->route('admin.rayon.index')->with('success', 'Rayon berhasil disimpan');
    }

    public function edit(Rayon $rayon)
    {
        $guru = Guru::where('aktif', true)->orderBy('nama')->get();

        return view('admin.rayon.form', [
            'rayon' => $rayon,
            'guru' => $guru,
            'action' => route('admin.rayon.update', $rayon),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, Rayon $rayon)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:rayon,nama,'.$rayon->id],
            'guru_id' => ['required', 'exists:guru,id'],
        ]);

        $rayon->update($validated);

        return redirect()->route('admin.rayon.index')->with('success', 'Rayon berhasil diperbarui');
    }

    public function destroy(Rayon $rayon)
    {
        $rayon->delete();

        return redirect()->route('admin.rayon.index')->with('success', 'Rayon berhasil dihapus');
    }
}
