@extends('layouts.member')
@section('title','Isi Ulang Saldo')
@section('content')
<div class="card member-panel">
    <div class="card-header bg-success text-white">Isi Ulang Saldo</div>
    <div class="card-body">
        <p class="mb-3">Saldo saat ini: <strong class="text-primary fs-5">Rp {{ number_format((int)$member->saldo,0,',','.') }}</strong></p>
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('member.topup.submit') }}" enctype="multipart/form-data">@csrf
            <div class="mb-3">
                <label class="form-label">Nominal</label>
                <input type="number" name="nominal" min="10000" placeholder="Minimal 10000" value="{{ old('nominal') }}" class="form-control @error('nominal') is-invalid @enderror" required>
                @error('nominal')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Metode Isi Ulang</label>
                <select name="metode" class="form-select @error('metode') is-invalid @enderror">
                    <option value="cash" {{ old('metode') === 'cash' ? 'selected' : '' }}>Setor Tunai ke Petugas</option>
                    <option value="qris" {{ old('metode') === 'qris' ? 'selected' : '' }}>QRIS</option>
                    <option value="va" {{ old('metode') === 'va' ? 'selected' : '' }}>Virtual Account</option>
                    <option value="ewallet" {{ old('metode') === 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                </select>
                @error('metode')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Upload Bukti Bayar (Opsional)</label>
                <input type="file" name="payment_proof" class="form-control @error('payment_proof') is-invalid @enderror" accept=".jpg,.jpeg,.png,.pdf">
                @error('payment_proof')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button class="btn btn-success rounded-pill px-4">Proses Top Up</button>
        </form>
    </div>
</div>
@endsection
