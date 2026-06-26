<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use App\Support\TahunAjaranStatus;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    public function index()
    {
        TahunAjaranStatus::syncToday();

        $data = TahunAjaran::orderByDesc('tanggal_mulai')->get();

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
            'nama' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        TahunAjaran::create([
            'nama' => $request->nama,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
        ]);

        TahunAjaranStatus::syncToday();

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
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        $tahun_ajaran->update([

            'nama' => $request->nama,

            'tanggal_mulai' => $request->tanggal_mulai,

            'tanggal_selesai' => $request->tanggal_selesai,

        ]);

        TahunAjaranStatus::syncToday();

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
