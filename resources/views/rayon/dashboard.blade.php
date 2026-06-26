@extends('layouts.app')

@section('title','Dashboard Pembimbing Rayon')

@section('content')

<h2 class="mb-4">Dashboard Pembimbing Rayon</h2>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="text-muted">Siswa Aktif di Rayon Binaan</div>
        <h3>{{ $siswaRayon }}</h3>
    </div>
</div>

<a href="{{ route('rayon.siswa.index') }}" class="btn btn-primary mt-4">
    Lihat Siswa Rayon
</a>

@endsection
