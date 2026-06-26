@extends('layouts.app')

@section('content')
<nav aria-label="breadcrumb">

    <ol class="breadcrumb">

        <li class="breadcrumb-item">

            <a href="{{ url('/admin/dashboard') }}">
                Dashboard
            </a>

        </li>

        <li class="breadcrumb-item active">
            Tahun Ajaran
        </li>

    </ol>

</nav>

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="page-title mb-1">
            Tahun Ajaran
        </h2>

        <p class="text-muted mb-0">
            Kelola data tahun ajaran sekolah
        </p>

    </div>

    <div class="d-flex gap-2 flex-wrap">
        @include('components.import-actions', ['master' => 'tahun-ajaran'])
        <a href="{{ route('admin.tahun-ajaran.create') }}"
       class="btn btn-primary">

        <i class="bi bi-plus-circle"></i>

        Tambah Tahun Ajaran

        </a>
    </div>

</div>

<div class="row mb-4">

    <div class="col-md-3">

        <div class="card stat-card">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <small>Total Tahun Ajaran</small>

                        <h2>{{ $data->count() }}</h2>

                    </div>

                    <div
                        class="stat-icon"
                        style="background:#dbeafe">

                        <i class="bi bi-calendar-range text-primary"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<table class="table table-bordered">

    <thead>
        <tr>
            <th>Nama</th>
            <th>Mulai</th>
            <th>Selesai</th>
            <th>Status</th>
            <th width="150">Aksi</th>
        </tr>
    </thead>

    <tbody>

    @forelse($data as $item)

    <tr>

        <td>{{ $item->nama }}</td>

        <td>{{ $item->tanggal_mulai }}</td>

        <td>{{ $item->tanggal_selesai }}</td>

        <td>

            @if($item->aktif)

            <span class="badge bg-success">
                Aktif
            </span>

            @else

            <span class="badge bg-secondary">
                Nonaktif
            </span>

            @endif

        </td>

        <td>

            <a href="{{ route('admin.tahun-ajaran.edit',$item->id) }}"
                class="btn btn-warning btn-sm">

                    <i class="bi bi-pencil-square"></i>

                    Edit

            </a>

            <form
                action="{{ route('admin.tahun-ajaran.destroy',$item->id) }}"
                method="POST"
                class="delete-form">

                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="btn btn-danger btn-sm">

                    <i class="bi bi-trash"></i>

                    Hapus

                </button>

            </form>

        </td>

    </tr>

    @empty

    <tr>
        <td colspan="5">
            Belum ada data
        </td>
    </tr>

    @endforelse

    </tbody>

</table>

@endsection
