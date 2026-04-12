@extends('layouts.master')
@section('title','Dashboard Admin')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Dashboard Admin</h4>
    <span class="text-muted small">{{ now()->format('d M Y H:i') }}</span>
</div>

<div class="card panel mb-3">
    <div class="card-header bg-white fw-semibold">Ringkasan User</div>
    <div class="card-body d-flex justify-content-between align-items-center">
        <div>
            <div class="text-muted small">Total User Sistem</div>
            <div class="fs-3 fw-bold text-primary">{{ $totalUser }}</div>
        </div>
        <a href="{{ route('admin.users') }}" class="btn btn-primary rounded-pill">Kelola User</a>
    </div>
</div>

<div class="text-muted small mb-2">Ringkasan Operasional Hari Ini</div>
<div class="row g-3">
    <div class="col-md-4"><div class="card panel bg-success text-white"><div class="card-body"><div class="small opacity-75">Masuk Hari Ini</div><div class="fs-3 fw-bold">{{ $masukHariIni }}</div></div></div></div>
    <div class="col-md-4"><div class="card panel bg-info text-white"><div class="card-body"><div class="small opacity-75">Keluar Hari Ini</div><div class="fs-3 fw-bold">{{ $keluarHariIni }}</div></div></div></div>
    <div class="col-md-4"><div class="card panel bg-dark text-white"><div class="card-body"><div class="small opacity-75">Sedang Parkir</div><div class="fs-3 fw-bold">{{ $sedangParkir }}</div></div></div></div>
</div>
<div class="card panel mt-3">
    <div class="card-body d-flex justify-content-between align-items-center">
        <span class="text-muted">Pendapatan Hari Ini</span>
        <span class="fs-4 fw-bold text-primary">Rp {{ number_format($pendapatanHariIni,0,',','.') }}</span>
    </div>
</div>
@endsection
