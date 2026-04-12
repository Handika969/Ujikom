@extends('layouts.master')
@section('title','Laporan Owner')
@section('content')
<h4 class="mb-3">Dashboard Owner</h4>
<div class="mb-2">
    <a class="btn btn-outline-dark btn-sm rounded-pill" target="_blank" href="{{ route('owner.print', ['start_date' => $start, 'end_date' => $end]) }}">Export PDF (Print)</a>
</div>
<div class="card panel mb-3">
    <div class="card-header bg-white border-0 pb-0 fw-semibold">Filter Laporan</div>
    <div class="card-body">
        <form class="row g-2">
            <div class="col-md-4"><input type="date" name="start_date" value="{{ $start }}" class="form-control"></div>
            <div class="col-md-4"><input type="date" name="end_date" value="{{ $end }}" class="form-control"></div>
            <div class="col-md-4"><button class="btn btn-primary rounded-pill">Terapkan</button></div>
        </form>
    </div>
</div>
<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card panel"><div class="card-body"><div class="text-muted small">Pendapatan Parkir</div><div class="fs-5 fw-bold text-primary">Rp {{ number_format($pendapatanParkir,0,',','.') }}</div></div></div></div>
    <div class="col-md-3"><div class="card panel"><div class="card-body"><div class="text-muted small">Pendapatan Top Up</div><div class="fs-5 fw-bold text-success">Rp {{ number_format($pendapatanTopup,0,',','.') }}</div></div></div></div>
    <div class="col-md-3"><div class="card panel"><div class="card-body"><div class="text-muted small">Total Pendapatan</div><div class="fs-5 fw-bold text-dark">Rp {{ number_format($totalPendapatan,0,',','.') }}</div></div></div></div>
    <div class="col-md-3"><div class="card panel"><div class="card-body"><div class="text-muted small">Total Transaksi Parkir</div><div class="fs-5 fw-bold">{{ $totalKendaraan }}</div></div></div></div>
</div>
<div class="card panel mb-3">
<div class="card-header bg-white fw-semibold">Daftar Top Up Berhasil</div>
<div class="table-responsive">
<table class="table table-hover mb-0">
    <thead class="table-light"><tr><th>ID</th><th>Waktu</th><th>Member</th><th>Nominal</th><th>Metode</th><th>Verifikator</th></tr></thead>
    <tbody>@forelse($topupLaporan as $t)<tr><td>#{{ $t->id_topup }}</td><td>{{ $t->created_at }}</td><td>{{ $t->member->nama_member ?? '-' }}</td><td>Rp {{ number_format((int)$t->nominal,0,',','.') }}</td><td>{{ strtoupper($t->metode) }}</td><td>{{ $t->verifiedBy->nama_lengkap ?? '-' }}</td></tr>@empty<tr><td colspan="6" class="text-center">Tidak ada data top up berhasil.</td></tr>@endforelse</tbody>
</table>
</div>
</div>
<div class="card panel">
<div class="table-responsive">
<table class="table table-hover mb-0">
    <thead class="table-light"><tr><th>Tiket</th><th>Plat</th><th>Keluar</th><th>Biaya</th><th>Petugas</th></tr></thead>
    <tbody>@forelse($laporan as $l)<tr><td>#{{ $l->id_parkir }}</td><td>{{ $l->kendaraan->plat_nomor ?? '-' }}</td><td>{{ $l->waktu_keluar }}</td><td>Rp {{ number_format($l->biaya_total,0,',','.') }}</td><td>{{ $l->user->nama_lengkap ?? '-' }}</td></tr>@empty<tr><td colspan="5" class="text-center">Tidak ada data.</td></tr>@endforelse</tbody>
</table>
</div>
</div>
@endsection
