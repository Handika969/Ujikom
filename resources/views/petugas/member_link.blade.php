@extends('layouts.master')
@section('title','Daftarkan Member dari Plat')
@section('content')
<div class="card">
    <div class="card-header">Link Member ke Plat Kendaraan</div>
    <div class="card-body">
        <form method="POST" action="{{ route('petugas.member-link-save') }}">@csrf
            <div class="mb-2"><label>Plat Nomor</label><input name="plat_nomor" value="{{ old('plat_nomor',$plat) }}" class="form-control text-uppercase" required></div>
            <div class="mb-2"><label>Jenis Kendaraan</label><select name="jenis_kendaraan" class="form-select">@php($j=old('jenis_kendaraan',$kendaraan->jenis_kendaraan ?? 'motor'))<option value="motor" {{ $j==='motor'?'selected':'' }}>Motor</option><option value="mobil" {{ $j==='mobil'?'selected':'' }}>Mobil</option><option value="lainnya" {{ $j==='lainnya'?'selected':'' }}>Lainnya</option></select></div>
            <div class="mb-2"><label>Nama Member</label><input name="nama_member" value="{{ old('nama_member',$kendaraan->member->nama_member ?? '') }}" class="form-control" required></div>
            <div class="mb-2"><label>No HP</label><input name="no_hp" maxlength="12" placeholder="08xxxxxxxxxx" value="{{ old('no_hp',$kendaraan->member->no_hp ?? '') }}" class="form-control" required></div>
            <div class="mb-2"><label>Username Member</label><input name="username_member" value="{{ old('username_member',$kendaraan->member->username_member ?? '') }}" class="form-control"></div>
            <div class="mb-2"><label>Password Member</label><input type="password" name="password_member" class="form-control"></div>
            <div class="mb-2"><label>Alamat</label><textarea name="alamat" class="form-control">{{ old('alamat',$kendaraan->member->alamat ?? '') }}</textarea></div>
            <button class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
@endsection
