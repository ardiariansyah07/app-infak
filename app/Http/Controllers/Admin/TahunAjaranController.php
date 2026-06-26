<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    public function index()
    {
        $data = TahunAjaran::latest()->get();

        return view(
            'admin.tahun_ajaran.index',
            compact('data')
        );
    }

    public function create()
    {
        return view('admin.tahun_ajaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
        ]);

        if ($request->aktif) {

            TahunAjaran::query()
                ->update([
                    'aktif' => 0,
                ]);

        }

        TahunAjaran::create([
            'nama' => $request->nama,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'aktif' => $request->aktif ? 1 : 0,
        ]);

        return redirect()
            ->route('admin.tahun-ajaran.index')
            ->with('success', 'Data berhasil disimpan');
    }

    public function edit(TahunAjaran $tahun_ajaran)
    {
        return view(
            'admin.tahun_ajaran.edit',
            compact('tahun_ajaran')
        );
    }

    public function update(
        Request $request,
        TahunAjaran $tahun_ajaran
    ) {
        if ($request->aktif) {

            TahunAjaran::query()
                ->update([
                    'aktif' => 0,
                ]);

        }

        $tahun_ajaran->update([

            'nama' => $request->nama,

            'tanggal_mulai' => $request->tanggal_mulai,

            'tanggal_selesai' => $request->tanggal_selesai,

            'aktif' => $request->aktif ? 1 : 0,

        ]);

        return redirect()
            ->route('admin.tahun-ajaran.index')
            ->with('success', 'Data berhasil diupdate');
    }

    public function destroy(TahunAjaran $tahun_ajaran)
    {
        $tahun_ajaran->delete();

        return back()
            ->with('success', 'Data berhasil dihapus');
    }
}
