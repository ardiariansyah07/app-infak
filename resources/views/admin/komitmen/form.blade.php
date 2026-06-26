@extends('layouts.app')

@section('title','Form Komitmen Infak')

@section('content')
<h2 class="fw-bold mb-4">Form Komitmen Infak</h2>

<form action="{{ $action }}" method="POST" class="card border-0 shadow-sm">
    @csrf
    @if($method !== 'POST') @method($method) @endif
    <div class="card-body">
        <div class="mb-3">
            <label class="form-label">Siswa</label>
            <select name="siswa_akademik_id" class="form-select" data-searchable-select data-placeholder="Pilih data..." required>
                @foreach($siswaAkademik as $item)
                    <option value="{{ $item->id }}" @selected(old('siswa_akademik_id', $komitmen->siswa_akademik_id) == $item->id)>
                        {{ $item->siswa?->nama }} ({{ $item->siswa?->nis }}) - {{ $item->rayon?->nama }} - {{ $item->rombel?->nama }} - {{ $item->tahunAjaran?->nama }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Nominal Bulanan</label>
            <input name="nominal_bulanan" type="number" min="1000" class="form-control" value="{{ old('nominal_bulanan', $komitmen->nominal_bulanan) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mulai Bulan</label>
            <input name="mulai_bulan" type="date" class="form-control" value="{{ old('mulai_bulan', optional($komitmen->mulai_bulan)->format('Y-m-d')) }}">
        </div>
        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route(($prefix ?? 'admin') . '.komitmen-infak.index') }}" class="btn btn-light">Batal</a>
    </div>
</form>
@endsection
