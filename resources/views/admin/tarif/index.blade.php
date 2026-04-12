@extends('layouts.master')
@section('title','Admin - Tarif Parkir')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Manajemen Tarif Parkir</h4>
    <span class="text-muted small">Atur tarif per jenis kendaraan</span>
</div>
<div class="card panel">
<div class="card-header bg-white fw-semibold">Daftar Tarif</div>
<table class="table table-sm table-hover mb-0 align-middle">
    <thead class="table-light"><tr><th>ID</th><th>Jenis</th><th>Tarif Normal/Jam</th><th>Tarif Member/Jam (Auto -10%)</th><th>Jumlah Transaksi</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($tarifs as $t)
        <tr>
            <td>{{ $t->id_tarif }}</td>
            <td><span class="badge bg-primary-subtle text-primary">{{ strtoupper($t->jenis_kendaraan) }}</span></td>
            <td>Rp {{ number_format($t->tarif_per_jam,0,',','.') }}</td>
            <td>Rp {{ number_format((int) round(((int)$t->tarif_per_jam) * 0.9),0,',','.') }}</td>
            <td>{{ $t->transaksi_count ?? 0 }}</td>
            <td>
                <div class="d-flex flex-column gap-1 mb-1">
                    <form method="POST" action="{{ route('admin.tarif.update', $t->id_tarif) }}" class="d-flex gap-1 flex-wrap">
                        @csrf @method('PUT')
                        <select class="form-select form-select-sm" name="jenis_kendaraan"><option @selected($t->jenis_kendaraan==='motor')>motor</option><option @selected($t->jenis_kendaraan==='mobil')>mobil</option><option @selected($t->jenis_kendaraan==='lainnya')>lainnya</option></select>
                        <input type="number" min="0" class="form-control form-control-sm" style="min-width:150px" name="tarif_per_jam" value="{{ (int)$t->tarif_per_jam }}" required>
                        <button class="btn btn-sm btn-warning rounded-pill">Update</button>
                    </form>
                    <form method="POST" action="{{ route('admin.tarif.delete', $t->id_tarif) }}" class="d-flex">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('Hapus tarif ini?')">Hapus</button>
                    </form>
                </div>
            </td>
        </tr>
    @empty
        <tr><td colspan="6" class="text-center">Data tarif kosong.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
@endsection
