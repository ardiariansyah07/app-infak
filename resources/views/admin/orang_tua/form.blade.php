@extends('layouts.app')

@section('title','Form Orang Tua')

@section('content')
<h2 class="fw-bold mb-4">Form Orang Tua</h2>

<form action="{{ $action }}" method="POST" class="card border-0 shadow-sm">
    @csrf
    @if($method !== 'POST') @method($method) @endif
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nama</label>
                <input name="nama" class="form-control" value="{{ old('nama', $orangTua->nama) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">User Login</label>
                <select name="user_id" class="form-select">
                    <option value="">Belum ditautkan</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(old('user_id', $orangTua->user_id) == $user->id)>{{ $user->name }} - {{ $user->email }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input name="email" type="email" class="form-control" value="{{ old('email', $orangTua->email) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">No HP</label>
                <input name="no_hp" class="form-control" value="{{ old('no_hp', $orangTua->no_hp) }}">
            </div>
        </div>
        <button class="btn btn-primary mt-4">Simpan</button>
        <a href="{{ route('admin.orang-tua.index') }}" class="btn btn-light mt-4">Batal</a>
    </div>
</form>
@endsection
