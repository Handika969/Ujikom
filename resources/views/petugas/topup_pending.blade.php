@extends('layouts.master')
@section('title','Verifikasi Top Up')
@section('content')
<table class="table table-bordered table-sm">
    <thead><tr><th>ID</th><th>Member</th><th>Nominal</th><th>Metode</th><th>Bukti</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($topups as $t)
        <tr>
            <td>#{{ $t->id_topup }}</td>
            <td>{{ $t->member->nama_member ?? '-' }}</td>
            <td>Rp {{ number_format($t->nominal,0,',','.') }}</td>
            <td>{{ strtoupper($t->metode) }}</td>
            <td>
                @if($t->payment_proof_path)
                    @if($t->proof_available ?? false)
                        <a href="{{ route('petugas.topup-proof', $t->id_topup) }}" target="_blank">Lihat Bukti</a>
                    @else
                        <span class="badge bg-warning text-dark">File Bukti Tidak Ditemukan</span>
                    @endif
                @else
                    <span class="badge bg-secondary">Belum Upload Bukti</span>
                @endif
            </td>
            <td>{{ strtoupper($t->status) }}</td>
            <td>
                <form method="POST" action="{{ route('petugas.topup-verify',$t->id_topup) }}" class="d-flex gap-1">
                    @csrf
                    <input type="hidden" name="aksi" value="success">
                    <button class="btn btn-success btn-sm">Setujui</button>
                </form>
                <form method="POST" action="{{ route('petugas.topup-verify',$t->id_topup) }}" class="d-flex gap-1 mt-1">
                    @csrf
                    <input type="hidden" name="aksi" value="failed">
                    <input type="text" name="catatan_verifikasi" class="form-control form-control-sm" placeholder="Alasan gagal">
                    <button class="btn btn-outline-danger btn-sm">Tolak</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="7" class="text-center">Tidak ada top up pending.</td></tr>
    @endforelse
    </tbody>
</table>
{{ $topups->links() }}
@endsection
