<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem Parkir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: radial-gradient(circle at top, #dbeafe, #eff6ff 45%, #f8fafc); }
        .login-card { border: 0; border-radius: 16px; box-shadow: 0 12px 28px rgba(15, 23, 42, 0.14); }
    </style>
</head>
<body class="bg-light d-flex align-items-center" style="min-height:100vh;">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card login-card">
                <div class="card-header bg-dark text-white text-center fw-semibold">Login</div>
                <div class="card-body">
                    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
                    <form method="POST" action="/login">@csrf
                        <div class="mb-3"><label class="form-label">Username</label><input name="username" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
                        <button class="btn btn-dark w-100 rounded-pill">Masuk</button>
                    </form>
                    <div class="text-center mt-2"><a href="{{ route('member.register') }}">Belum punya akun member? Daftar di sini</a></div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
