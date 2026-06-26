<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rombel;
use Illuminate\Http\Request;

class RombelController extends Controller
{
    public function index()
    {
        $data = Rombel::latest()->get();

        return view('admin.rombel.index', compact('data'));
    }

    public function create()
    {
        return view('admin.rombel.form', [
            'rombel' => new Rombel,
            'action' => route('admin.rombel.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:rombel,nama'],
            'tingkat' => ['nullable', 'in:X,XI,XII'],
        ]);

        Rombel::create($validated);

        return redirect()->route('admin.rombel.index')->with('success', 'Rombel berhasil disimpan');
    }

    public function edit(Rombel $rombel)
    {
        return view('admin.rombel.form', [
            'rombel' => $rombel,
            'action' => route('admin.rombel.update', $rombel),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, Rombel $rombel)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:rombel,nama,'.$rombel->id],
            'tingkat' => ['nullable', 'in:X,XI,XII'],
        ]);

        $rombel->update($validated);

        return redirect()->route('admin.rombel.index')->with('success', 'Rombel berhasil diperbarui');
    }

    public function destroy(Rombel $rombel)
    {
        $rombel->delete();

        return redirect()->route('admin.rombel.index')->with('success', 'Rombel berhasil dihapus');
    }
}
