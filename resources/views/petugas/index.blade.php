@extends('layouts.master')
@section('title','Petugas - Operasional Otomatis')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Operasional Petugas</h4>
</div>
<div class="card panel mb-3">
    <div class="card-body">
        <form class="row g-2">
            <div class="col-md-10">
                <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control" placeholder="Cari tiket / plat / nama member">
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary">Cari</button>
            </div>
        </form>
    </div>
</div>
<div class="row g-2 mb-3">
    <div class="col-md-3"><div class="card panel"><div class="card-body text-center small">Masuk Hari Ini<br><strong>{{ $shiftMasuk ?? 0 }}</strong></div></div></div>
    <div class="col-md-3"><div class="card panel"><div class="card-body text-center small">Keluar Hari Ini<br><strong>{{ $shiftKeluar ?? 0 }}</strong></div></div></div>
    <div class="col-md-3"><div class="card panel"><div class="card-body text-center small">Tunai Shift<br><strong>Rp {{ number_format((int)($shiftTunai ?? 0),0,',','.') }}</strong></div></div></div>
    <div class="col-md-3"><div class="card panel"><div class="card-body text-center small">Saldo Shift<br><strong>Rp {{ number_format((int)($shiftSaldo ?? 0),0,',','.') }}</strong></div></div></div>
</div>
<div class="row g-2 mb-3">
    @foreach(($areas ?? []) as $a)
        <div class="col-md-3">
            <div class="card panel">
                <div class="card-body small">
                    <strong>{{ $a->nama_area }}</strong><br>
                    Kapasitas: {{ $a->kapasitas }}<br>
                    Terisi: {{ $a->terisi }}<br>
                    Sisa: {{ max(0, $a->kapasitas - $a->terisi) }}
                </div>
            </div>
        </div>
    @endforeach
</div>
<div class="card panel">
<div class="table-responsive">
<table class="table table-hover align-middle mb-0">
    <thead class="table-light"><tr><th>Tiket</th><th>Plat</th><th>Jenis Transaksi</th><th>Member</th><th>Masuk</th><th>Gate</th><th class="text-end">Aksi</th></tr></thead>
    <tbody>
    @forelse($transaksi as $t)
        <tr>
            <td>#{{ $t->id_parkir }}</td><td>{{ $t->kendaraan->plat_nomor }}</td>
            <td>
                @if($t->kendaraan->member)
                    <span class="badge bg-success-subtle text-success">MEMBER</span>
                @else
                    <span class="badge bg-secondary-subtle text-secondary">NON-MEMBER</span>
                @endif
            </td>
            <td>{{ $t->kendaraan->member->nama_member ?? '-' }}</td>
            <td>{{ $t->waktu_masuk }}</td><td><span class="badge bg-success-subtle text-success">{{ strtoupper($t->gateway_in_status ?? '-') }}</span></td>
            <td class="text-end">
                <a class="btn btn-danger btn-sm rounded-pill" href="{{ route('petugas.checkout',$t->id_parkir) }}">Proses Keluar</a>
                <a class="btn btn-outline-dark btn-sm rounded-pill" href="{{ route('petugas.lost-ticket',$t->id_parkir) }}">Tiket Hilang</a>
            </td>
        </tr>
    @empty
        <tr><td colspan="7" class="text-center">Belum ada kendaraan parkir.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
</div>
@endsection
