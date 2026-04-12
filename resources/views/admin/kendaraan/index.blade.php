@extends('layouts.master')
@section('title','Admin - Data Kendaraan')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Manajemen Kendaraan</h4>
    <span class="text-muted small">Data kendaraan dan keterkaitan member</span>
</div>
<div class="card panel mb-3">
    <div class="card-header bg-white fw-semibold">Tambah Kendaraan</div>
    <div class="card-body">
        <form class="row g-3" method="POST" action="{{ route('admin.kendaraan.store') }}">
            @csrf
            <div class="col-md-3"><input class="form-control" name="plat_nomor" placeholder="Plat nomor" required></div>
            <div class="col-md-3"><select class="form-select" name="jenis_kendaraan"><option>motor</option><option>mobil</option><option>lainnya</option></select></div>
            <div class="col-md-4"><input class="form-control" name="pemilik" placeholder="Nama pemilik"></div>
            <div class="col-md-2 d-grid"><button class="btn btn-primary rounded-pill">Simpan</button></div>
        </form>
    </div>
</div>
<div class="card panel">
<div class="card-header bg-white fw-semibold">Daftar Kendaraan</div>
<div class="table-responsive">
<table class="table table-sm table-hover mb-0 align-middle">
    <thead class="table-light"><tr><th>ID</th><th>Plat</th><th>Jenis</th><th>Pemilik</th><th>Member</th><th>Jumlah Transaksi</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($kendaraan as $k)
        <tr>
            <td>{{ $k->id_kendaraan }}</td>
            <td>{{ $k->plat_nomor }}</td>
            <td><span class="badge bg-primary-subtle text-primary">{{ strtoupper($k->jenis_kendaraan) }}</span></td>
            <td>{{ $k->pemilik ?? '-' }}</td>
            <td>{{ $k->member->nama_member ?? '-' }}</td>
            <td>{{ $k->transaksi_count ?? 0 }}</td>
            <td>
                <div class="d-flex flex-column gap-1 mb-1">
                    <form method="POST" action="{{ route('admin.kendaraan.update', $k->id_kendaraan) }}" class="d-flex gap-1 flex-wrap">
                        @csrf @method('PUT')
                        <input class="form-control form-control-sm" style="min-width:140px" name="plat_nomor" value="{{ $k->plat_nomor }}" required>
                        <select class="form-select form-select-sm" name="jenis_kendaraan"><option @selected($k->jenis_kendaraan==='motor')>motor</option><option @selected($k->jenis_kendaraan==='mobil')>mobil</option><option @selected($k->jenis_kendaraan==='lainnya')>lainnya</option></select>
                        <button class="btn btn-sm btn-warning rounded-pill">Update</button>
                    </form>
                    <form method="POST" action="{{ route('admin.kendaraan.delete', $k->id_kendaraan) }}" class="d-flex">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('Hapus kendaraan ini?')">Hapus</button>
                    </form>
                </div>
            </td>
        </tr>
    @empty
        <tr><td colspan="7" class="text-center">Data kendaraan kosong.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
</div>
<div class="mt-2">{{ $kendaraan->links() }}</div>
@endsection
