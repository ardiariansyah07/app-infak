@extends('layouts.app')

@section('title','Nominal Infak')

@section('content')
<h2 class="fw-bold mb-4">Nominal Infak Anak</h2>

@php
    $sudahMengisi = $isSiswaLogin && $data->isNotEmpty();
@endphp

@if($sudahMengisi)
    <div class="alert alert-info">
        Nominal infak sudah diisi dan tidak bisa diedit dari login siswa. Hubungi petugas infak atau admin jika perlu perubahan.
    </div>
@else
    <form action="{{ route('ortu.komitmen.update') }}" method="POST" class="card border-0 shadow-sm mb-4">
        @csrf @method('PUT')
        <div class="card-body">
            <div class="row g-3 align-items-end">
                @if($siswaAkademik->count() === 1)
                    <input type="hidden" name="siswa_akademik_id" value="{{ $siswaAkademik->first()->id }}">
                    <div class="col-md-6">
                        <label class="form-label">Siswa</label>
                        <input type="text" class="form-control" value="{{ $siswaAkademik->first()->siswa?->nama }}" readonly>
                    </div>
                @else
                    <div class="col-md-6">
                        <label class="form-label">Anak</label>
                        <select name="siswa_akademik_id" class="form-select" required>
                            @foreach($siswaAkademik as $item)
                                <option value="{{ $item->id }}">{{ $item->siswa?->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-md-4">
                    <label class="form-label">Nominal Bulanan</label>
                    <input name="nominal_bulanan" type="number" min="1000" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Simpan</button>
                </div>
            </div>
        </div>
    </form>
@endif

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Anak</th><th>Nominal Saat Ini</th></tr></thead>
            <tbody>
            @forelse($data as $komitmen)
                <tr>
                    <td>{{ $komitmen->siswaAkademik?->siswa?->nama }}</td>
                    <td>Rp {{ number_format($komitmen->nominal_bulanan, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="2" class="text-center py-4 text-muted">Belum ada komitmen.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
