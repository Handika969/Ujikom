@extends('layouts.master')
@section('title','Proses Tiket Hilang')
@section('content')
<div class="card panel border-dark">
    <div class="card-header bg-dark text-white">Proses Tiket Hilang</div>
    <div class="card-body">
        <table class="table table-borderless mb-3">
            <tr><td>Tiket</td><th>#{{ $transaksi->id_parkir }}</th></tr>
            <tr><td>Plat</td><th>{{ $transaksi->kendaraan->plat_nomor }}</th></tr>
            <tr><td>Tarif / Jam</td><th>Rp {{ number_format((int)($tarifPerJam ?? 0),0,',','.') }} ({{ $transaksi->kendaraan->member ? 'MEMBER' : 'NON-MEMBER' }})</th></tr>
            @if($transaksi->kendaraan->member)
                <tr><td>Diskon Member</td><th>10% dari tarif normal dan denda normal</th></tr>
            @endif
            <tr><td>Durasi</td><th>{{ $durasi }} Jam</th></tr>
            <tr><td>Biaya Dasar</td><th>Rp {{ number_format($biayaDasar,0,',','.') }}</th></tr>
            <tr><td>Denda Tiket Hilang</td><th>Rp {{ number_format($dendaDefault,0,',','.') }} @if($transaksi->kendaraan->member)<small class="text-muted">(member bayar 90% dari Rp {{ number_format((int)($dendaNormal ?? 0),0,',','.') }})</small>@endif</th></tr>
            <tr><td>Total Bayar</td><th>Rp {{ number_format($biayaTotal,0,',','.') }}</th></tr>
        </table>
        <form method="POST" action="{{ route('petugas.process-lost-ticket', $transaksi->id_parkir) }}">
            @csrf
            <input type="hidden" name="durasi" value="{{ $durasi }}">
            <input type="hidden" name="biaya_dasar" value="{{ $biayaDasar }}">
            <input type="hidden" name="denda_tiket_hilang" value="{{ $dendaDefault }}">
            <div class="mb-3">
                <label class="form-label">Denda Tiket Hilang</label>
                <input type="text" class="form-control" value="Rp {{ number_format($dendaDefault,0,',','.') }}" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Metode Pembayaran</label>
                <select name="metode_bayar" class="form-select">
                    <option value="tunai">Tunai</option>
                    @if($transaksi->kendaraan->member)
                        <option value="saldo">Saldo Member</option>
                    @endif
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Alasan (Opsional)</label>
                <input type="text" name="alasan" class="form-control" maxlength="150" placeholder="Contoh: tiket rusak / hilang oleh pelanggan">
            </div>
            <button class="btn btn-dark rounded-pill">Selesaikan Tiket Hilang</button>
        </form>
    </div>
</div>
@endsection
