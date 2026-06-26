@extends('layouts.app')

@section('title','Form Rombel')

@section('content')
<h2 class="fw-bold mb-4">Form Rombel</h2>

<form action="{{ $action }}" method="POST" class="card border-0 shadow-sm">
    @csrf
    @if($method !== 'POST') @method($method) @endif
    <div class="card-body">
        <div class="mb-3">
            <label class="form-label">Nama Rombel</label>
            <input name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $rombel->nama) }}" required>
            @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Tingkat</label>
            <select name="tingkat" class="form-select">
                <option value="">Tidak spesifik</option>
                @foreach(['X','XI','XII'] as $tingkat)
                    <option value="{{ $tingkat }}" @selected(old('tingkat', $rombel->tingkat) === $tingkat)>{{ $tingkat }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('admin.rombel.index') }}" class="btn btn-light">Batal</a>
    </div>
</form>
@endsection
