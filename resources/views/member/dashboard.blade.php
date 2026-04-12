@extends('layouts.member')
@section('title','Dashboard Member')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Dashboard Member</h4>
    <span class="text-muted small">{{ now()->format('d M Y') }}</span>
</div>
<div class="card member-panel mb-3">
    <div class="card-body d-flex justify-content-between align-items-center">
        <span class="text-muted">Saldo Saat Ini</span>
        <span class="fs-4 fw-bold text-primary">Rp {{ number_format((int)$member->saldo,0,',','.') }}</span>
    </div>
</div>
<div class="card member-panel mb-3">
    <div class="card-header bg-white border-0 fw-semibold">Data Akun Member</div>
    <div class="card-body">
        <div><span class="text-muted">Nama Member:</span> <strong>{{ $member->nama_member }}</strong></div>
        <div class="mt-1"><span class="text-muted">No HP:</span> <strong>{{ $member->no_hp }}</strong></div>
        <div class="mt-1"><span class="text-muted">Alamat:</span> <strong>{{ $member->alamat ?? '-' }}</strong></div>
    </div>
</div>
<div class="card member-panel mb-3">
    <div class="card-header bg-white border-0 fw-semibold">QR Member untuk Scan di Gate Petugas</div>
    <div class="card-body">
        <p class="mb-2 text-muted">Tunjukkan kode ini ke scanner petugas saat masuk area parkir.</p>
        <div class="fw-bold fs-5 text-success">{{ $member->kode_qr_member }}</div>
    </div>
</div>
<div class="row g-3">
    <div class="col-md-6"><div class="card member-panel"><div class="card-header bg-white border-0 fw-semibold">Riwayat Parkir</div><div class="card-body p-0"><table class="table table-sm mb-0"><tbody>@forelse($riwayatParkir as $r)<tr><td>#{{ $r->id_parkir }}</td><td>{{ $r->waktu_keluar }}</td><td>Rp {{ number_format($r->biaya_total,0,',','.') }}</td></tr>@empty<tr><td class="text-center">Belum ada riwayat parkir.</td></tr>@endforelse</tbody></table></div></div></div>
    <div class="col-md-6"><div class="card member-panel"><div class="card-header bg-white border-0 fw-semibold">Mutasi Saldo</div><div class="card-body p-0"><table class="table table-sm mb-0"><tbody>@forelse($mutasi as $m)<tr><td><span class="badge {{ $m->tipe === 'kredit' ? 'bg-success' : 'bg-danger' }}">{{ strtoupper($m->tipe) }}</span></td><td>Rp {{ number_format($m->nominal,0,',','.') }}</td><td>{{ $m->created_at }}</td></tr>@empty<tr><td class="text-center">Belum ada mutasi saldo.</td></tr>@endforelse</tbody></table></div></div></div>
</div>
@endsection
