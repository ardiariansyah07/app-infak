<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Guru::latest()->get();

        return view(
            'admin.guru.index',
            compact('data')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.guru.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([

            'nip' => 'required|unique:guru,nip',

            'nama' => 'required',

            'jenis_kelamin' => 'required',

        ]);

        Guru::create([

            'nip' => $request->nip,

            'nama' => $request->nama,

            'jenis_kelamin' => $request->jenis_kelamin,

            'no_hp' => $request->no_hp,

            'email' => $request->email,

            'alamat' => $request->alamat,

            'aktif' => $request->aktif ? 1 : 0,

        ]);

        return redirect()
            ->route('admin.guru.index')
            ->with('success', 'Data guru berhasil disimpan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Guru $guru)
    {
        return view(
            'admin.guru.edit',
            compact('guru')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Guru $guru)
    {
        $request->validate([

            'nip' => 'required|unique:guru,nip,'.$guru->id,

            'nama' => 'required',

            'jenis_kelamin' => 'required',

        ]);

        $guru->update([

            'nip' => $request->nip,

            'nama' => $request->nama,

            'jenis_kelamin' => $request->jenis_kelamin,

            'no_hp' => $request->no_hp,

            'email' => $request->email,

            'alamat' => $request->alamat,

            'aktif' => $request->aktif ? 1 : 0,

        ]);

        return redirect()
            ->route('admin.guru.index')
            ->with('success', 'Data guru berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guru $guru)
    {
        $guru->delete();

        return redirect()
            ->route('admin.guru.index')
            ->with('success', 'Data guru berhasil dihapus');
    }
}
