@extends('layouts.app')

@section('title','Form Rayon')

@section('content')
<h2 class="fw-bold mb-4">Form Rayon</h2>

<form action="{{ $action }}" method="POST" class="card border-0 shadow-sm">
    @csrf
    @if($method !== 'POST') @method($method) @endif
    <div class="card-body">
        <div class="mb-3">
            <label class="form-label">Nama Rayon</label>
            <input name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $rayon->nama) }}" required>
            @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Pembimbing Rayon</label>
            <select name="guru_id" class="form-select @error('guru_id') is-invalid @enderror" required>
                <option value="">Pilih guru</option>
                @foreach($guru as $item)
                    <option value="{{ $item->id }}" @selected(old('guru_id', $rayon->guru_id) == $item->id)>{{ $item->nama }}</option>
                @endforeach
            </select>
            @error('guru_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('admin.rayon.index') }}" class="btn btn-light">Batal</a>
    </div>
</form>
@endsection
