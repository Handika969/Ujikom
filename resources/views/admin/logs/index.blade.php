@extends('layouts.master')
@section('title','Admin - Log Aktivitas')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Log Aktivitas</h4>
    <span class="text-muted small">Riwayat aktivitas user dan top up</span>
</div>
<div class="card panel">
<div class="card-header bg-white fw-semibold">Aktivitas Sistem</div>
<div class="table-responsive">
<table class="table table-sm table-hover mb-0 align-middle">
    <thead class="table-light"><tr><th>ID Log</th><th>Waktu</th><th>User</th><th>Aktivitas</th></tr></thead>
    <tbody>
    @forelse($logs as $l)
        <tr>
            <td>{{ $l->id_log }}</td>
            <td>{{ $l->waktu_aktivitas }}</td>
            <td>{{ $l->user->nama_lengkap ?? '-' }}</td>
            <td>{{ $l->aktivitas }}</td>
        </tr>
    @empty
        <tr><td colspan="4" class="text-center">Belum ada log aktivitas.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
</div>
<div class="mt-2">{{ $logs->links() }}</div>

<div class="card panel mt-3">
<div class="card-header bg-white fw-semibold">Riwayat Top Up (Siapa Saja)</div>
<div class="table-responsive">
<table class="table table-sm table-hover mb-0 align-middle">
    <thead class="table-light"><tr><th>ID Top Up</th><th>Waktu</th><th>Member</th><th>Nominal</th><th>Metode</th><th>Status</th><th>Verifikator</th></tr></thead>
    <tbody>
    @forelse(($topupLogs ?? collect()) as $t)
        <tr>
            <td>#{{ $t->id_topup }}</td>
            <td>{{ $t->created_at }}</td>
            <td>{{ $t->member->nama_member ?? '-' }}</td>
            <td>Rp {{ number_format((int)$t->nominal, 0, ',', '.') }}</td>
            <td><span class="badge bg-primary-subtle text-primary">{{ strtoupper($t->metode) }}</span></td>
            <td>
                @if($t->status === 'success')
                    <span class="badge bg-success-subtle text-success">SUCCESS</span>
                @elseif($t->status === 'failed')
                    <span class="badge bg-danger-subtle text-danger">FAILED</span>
                @else
                    <span class="badge bg-warning-subtle text-warning">PENDING</span>
                @endif
            </td>
            <td>{{ $t->verifiedBy->nama_lengkap ?? '-' }}</td>
        </tr>
    @empty
        <tr><td colspan="7" class="text-center">Belum ada data top up.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
</div>
@endsection
