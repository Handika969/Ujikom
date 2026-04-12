@extends('layouts.master')
@section('title','Riwayat Keluar')
@section('content')
<table class="table table-bordered table-sm">
    <thead><tr><th>Tiket</th><th>Plat</th><th>Jenis Transaksi</th><th>Member</th><th>Keluar</th><th>Biaya</th><th>Metode</th></tr></thead>
    <tbody>@forelse($riwayat as $r)<tr><td>#{{ $r->id_parkir }}</td><td>{{ $r->kendaraan->plat_nomor ?? '-' }}</td><td>@if($r->kendaraan->member)<span class="badge bg-success-subtle text-success">MEMBER</span>@else<span class="badge bg-secondary-subtle text-secondary">NON-MEMBER</span>@endif</td><td>{{ $r->kendaraan->member->nama_member ?? '-' }}</td><td>{{ $r->waktu_keluar }}</td><td>Rp {{ number_format($r->biaya_total,0,',','.') }}</td><td>{{ strtoupper($r->metode_bayar ?? '-') }}</td></tr>@empty<tr><td colspan="7" class="text-center">Belum ada data</td></tr>@endforelse</tbody>
</table>
{{ $riwayat->links() }}
@endsection
