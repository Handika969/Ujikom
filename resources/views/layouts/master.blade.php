<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - E-Parking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f3f6fb; }
        .topbar { background: linear-gradient(90deg, #0f172a, #1e293b); }
        .panel { border: 0; border-radius: 14px; box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08); }
        .nav-chip { border-radius: 999px; }
    </style>
</head>
<body>
<nav class="navbar navbar-dark topbar">
    <div class="container">
        <span class="navbar-brand fw-semibold">E-Parking System</span>
        <div class="d-flex align-items-center gap-2">
            <span class="text-white-50 small">{{ auth()->user()->nama_lengkap ?? '' }} ({{ strtoupper(auth()->user()->role ?? '') }})</span>
            <form method="POST" action="{{ route('logout') }}">@csrf <button class="btn btn-outline-light btn-sm">Logout</button></form>
        </div>
    </div>
</nav>
<div class="container py-3">
    @if(auth()->check())
        <div class="panel bg-white p-2 mb-3">
            <div class="d-flex flex-wrap gap-2 justify-content-center">
                @if(auth()->user()->role === 'admin')
                    <a class="btn btn-sm btn-primary nav-chip" href="{{ route('admin.dashboard') }}">Dashboard</a>
                    <a class="btn btn-sm btn-outline-primary nav-chip" href="{{ route('admin.users') }}">User</a>
                    <a class="btn btn-sm btn-outline-primary nav-chip" href="{{ route('admin.tarif') }}">Tarif</a>
                    <a class="btn btn-sm btn-outline-primary nav-chip" href="{{ route('admin.area') }}">Area Parkir</a>
                    <a class="btn btn-sm btn-outline-primary nav-chip" href="{{ route('admin.kendaraan') }}">Kendaraan</a>
                    <a class="btn btn-sm btn-outline-dark nav-chip" href="{{ route('admin.logs') }}">Log Aktivitas</a>
                @elseif(auth()->user()->role === 'petugas')
                    <a class="btn btn-sm btn-success nav-chip" href="{{ route('petugas.dashboard') }}">Transaksi</a>
                    <a class="btn btn-sm btn-outline-success nav-chip" href="{{ route('petugas.entry', ['mode' => 'member']) }}">Masuk Member</a>
                    <a class="btn btn-sm btn-outline-success nav-chip" href="{{ route('petugas.entry', ['mode' => 'non-member']) }}">Masuk Non-Member</a>
                    <a class="btn btn-sm btn-outline-secondary nav-chip" href="{{ route('petugas.history') }}">Riwayat Keluar</a>
                    <a class="btn btn-sm btn-outline-warning nav-chip" href="{{ route('petugas.topup-pending') }}">Topup Cash</a>
                @elseif(auth()->user()->role === 'owner')
                    <a class="btn btn-sm btn-primary nav-chip" href="{{ route('owner.dashboard') }}">Laporan</a>
                @endif
            </div>
        </div>
    @endif
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    @yield('content')
</div>
</body>
</html>
