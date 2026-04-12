@extends('layouts.member')
@section('title','Riwayat Top Up')
@section('content')
<table class="table table-bordered table-sm">
    <thead><tr><th>Waktu</th><th>Nominal</th><th>Metode</th><th>Status</th><th>Catatan</th></tr></thead>
    <tbody>@forelse($topups as $t)<tr><td>{{ $t->created_at }}</td><td>Rp {{ number_format($t->nominal,0,',','.') }}</td><td>{{ strtoupper($t->metode) }}</td><td>{{ strtoupper($t->status) }}</td><td>{{ $t->catatan_verifikasi ?? '-' }}</td></tr>@empty<tr><td colspan="5" class="text-center">Belum ada riwayat top up.</td></tr>@endforelse</tbody>
</table>
{{ $topups->links() }}
@endsection
