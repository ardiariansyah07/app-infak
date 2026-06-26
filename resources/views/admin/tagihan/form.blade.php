@extends('layouts.app')

@section('title','Form Tagihan')

@section('content')
<h2 class="fw-bold mb-4">Form Tagihan Infak</h2>

<form action="{{ $action }}" method="POST" class="card border-0 shadow-sm">
    @csrf
    @if($method !== 'POST') @method($method) @endif
    <div class="card-body">
        <div class="mb-3">
            <label class="form-label">Siswa</label>
            <select name="siswa_akademik_id" class="form-select" required>
                @foreach($siswaAkademik as $item)
                    <option value="{{ $item->id }}" @selected(old('siswa_akademik_id', $tagihan->siswa_akademik_id) == $item->id)>
                        {{ $item->siswa?->nama }} - {{ $item->rombel?->nama }} - {{ $item->rayon?->nama }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Periode</label>
            <input name="periode" type="month" class="form-control" value="{{ old('periode', $tagihan->periode) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Nominal</label>
            <input name="nominal" type="number" min="1000" class="form-control" value="{{ old('nominal', $tagihan->nominal) }}" required>
        </div>
        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('admin.tagihan.index') }}" class="btn btn-light">Batal</a>
    </div>
</form>
@endsection
