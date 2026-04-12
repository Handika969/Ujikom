<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Member</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #eef4ff; }
        .member-topbar { background: linear-gradient(90deg, #1d4ed8, #2563eb); }
        .member-panel { border: 0; border-radius: 14px; box-shadow: 0 8px 20px rgba(37, 99, 235, 0.12); }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-dark member-topbar">
    <div class="container">
        <span class="navbar-brand">Portal Member</span>
        <form method="POST" action="{{ route('member.logout') }}">@csrf <button class="btn btn-outline-light btn-sm">Logout</button></form>
    </div>
</nav>
<div class="container py-3">
    <div class="member-panel bg-white p-2 mb-3">
        <div class="d-flex flex-wrap gap-2 justify-content-center">
            <a class="btn btn-sm btn-primary rounded-pill" href="{{ route('member.dashboard') }}">Dashboard</a>
            <a class="btn btn-sm btn-outline-primary rounded-pill" href="{{ route('member.topup.form') }}">Isi Ulang</a>
            <a class="btn btn-sm btn-outline-secondary rounded-pill" href="{{ route('member.topup.history') }}">Riwayat Topup</a>
            <a class="btn btn-sm btn-outline-dark rounded-pill" href="{{ route('member.notifications') }}">Notifikasi</a>
        </div>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    @yield('content')
</div>
</body>
</html>
