@extends('layouts.master')
@section('title','Admin - Area Parkir')
@section('content')
<div class="card panel mb-3">
    <div class="card-header bg-white fw-semibold">Tambah Area Parkir</div>
    <div class="card-body">
        <form class="row g-3" method="POST" action="{{ route('admin.area.store') }}">
            @csrf
            <div class="col-md-4"><input class="form-control" name="nama_area" placeholder="Nama area" required></div>
            <div class="col-md-3"><input type="number" min="1" class="form-control" name="kapasitas" placeholder="Kapasitas" required></div>
            <div class="col-md-3"><input type="number" min="0" class="form-control" name="terisi" placeholder="Terisi" value="0" required></div>
            <div class="col-md-2 d-grid"><button class="btn btn-primary rounded-pill">Simpan</button></div>
        </form>
    </div>
</div>
<div class="card panel">
<div class="card-header bg-white fw-semibold">Daftar Area</div>
<table class="table table-sm table-hover mb-0 align-middle">
    <thead class="table-light"><tr><th>ID</th><th>Area</th><th>Kapasitas</th><th>Terisi</th><th>Validasi</th><th></th></tr></thead>
    <tbody>
    @forelse($areas as $a)
        <tr>
            <td>{{ $a->id_area }}</td>
            <td>{{ $a->nama_area }}</td>
            <td>{{ $a->kapasitas }}</td>
            <td>{{ $a->terisi }}</td>
            <td>
                @if($a->terisi > $a->kapasitas)
                    <span class="badge bg-danger">Melebihi Kapasitas</span>
                @elseif($a->terisi === $a->kapasitas)
                    <span class="badge bg-warning text-dark">Penuh</span>
                @else
                    <span class="badge bg-success">Normal</span>
                @endif
            </td>
            <td>
                <form method="POST" action="{{ route('admin.area.update', $a->id_area) }}" class="d-flex gap-1 mb-1 flex-wrap">
                    @csrf @method('PUT')
                    <input class="form-control form-control-sm" style="min-width:180px" name="nama_area" value="{{ $a->nama_area }}" required>
                    <input type="number" min="1" class="form-control form-control-sm" style="min-width:110px" name="kapasitas" value="{{ $a->kapasitas }}" required>
                    <button class="btn btn-sm btn-warning rounded-pill">Update</button>
                </form>
                <form method="POST" action="{{ route('admin.area.delete', $a->id_area) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('Hapus area ini?')">Hapus</button></form>
            </td>
        </tr>
    @empty
        <tr><td colspan="6" class="text-center">Data area kosong.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
@endsection
