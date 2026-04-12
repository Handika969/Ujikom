@extends('layouts.master')
@section('title','Checkout')
@section('content')
<div class="card panel border-danger">
     <div class="card-header bg-danger text-white">Proses Keluar</div>
    <div class="card-body">
        <table class="table table-borderless">
            <tr><td>Tiket</td><th>#{{ $transaksi->id_parkir }}</th></tr>
            <tr><td>Plat</td><th>{{ $transaksi->kendaraan->plat_nomor }}</th></tr>
            <tr><td>Tarif / Jam</td><th>Rp {{ number_format((int)($tarifPerJam ?? 0),0,',','.') }} ({{ $isMember ? 'MEMBER' : 'NON-MEMBER' }})</th></tr>
            @if($isMember)
                <tr><td>Diskon Member</td><th>10% dari Rp {{ number_format((int)($tarifNormalPerJam ?? 0),0,',','.') }}</th></tr>
            @endif
            <tr><td>Durasi</td><th>{{ $durasi }} Jam</th></tr>
            <tr><td>Total</td><th>Rp {{ number_format($biaya,0,',','.') }}</th></tr>
            <tr><td>Status Keanggotaan</td><th>{!! $isMember ? 'MEMBER (Saldo: Rp '.number_format((int)$member->saldo,0,',','.').')' : 'NON-MEMBER' !!}</th></tr>
        </table>
        <form method="POST" action="{{ route('petugas.process-exit',$transaksi->id_parkir) }}">@csrf
            <input type="hidden" name="durasi" value="{{ $durasi }}"><input type="hidden" name="biaya" value="{{ $biaya }}">
            <div class="mb-3">
                <label class="form-label">Metode Pembayaran</label>
                <select name="metode_bayar" class="form-select">
                    <option value="tunai">Tunai</option>
                    @if($isMember)<option value="saldo" {{ $saldoCukup ? '' : 'disabled' }}>Saldo Member</option>@endif
                </select>
            </div>
            <button class="btn btn-success w-100 rounded-pill">Selesaikan</button>
        </form>
    </div>
</div>
@endsection
