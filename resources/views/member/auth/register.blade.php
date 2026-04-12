<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Member</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height:100vh;">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-success text-white">Daftar Akun Member</div>
                <div class="card-body">
                    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
                    <form method="POST" action="{{ route('member.register.submit') }}">
                        @csrf
                        <div class="mb-2"><label class="form-label">Nama Lengkap</label><input name="nama_member" value="{{ old('nama_member') }}" class="form-control" required></div>
                        <div class="mb-2">
                            <label class="form-label">Username</label>
                            <input id="username_member_input" name="username_member" value="{{ old('username_member') }}" class="form-control" required>
                            <div id="username_member_hint" class="form-text"></div>
                        </div>
                        <div class="mb-2"><label class="form-label">No HP</label><input name="no_hp" value="{{ old('no_hp') }}" maxlength="12" placeholder="08xxxxxxxxxx" class="form-control" required></div>
                        <div class="mb-2"><label class="form-label">Alamat</label><textarea name="alamat" class="form-control" rows="3" placeholder="Jalan, kelurahan, kecamatan..."></textarea></div>
                        <div class="mb-2"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Konfirmasi Password</label><input type="password" name="password_confirmation" class="form-control" required></div>
                        <button class="btn btn-success w-100">Daftar Member</button>
                    </form>
                    <div class="text-center mt-2"><a href="{{ route('member.login') }}">Sudah punya akun? Login</a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
(() => {
const input = document.getElementById('username_member_input');
const hint = document.getElementById('username_member_hint');
let timer = null;
if (!input) return;
function normalizeUsername(value) {
    return (value || '').toLowerCase().replace(/\s+/g, '');
}
input.addEventListener('input', () => {
    input.value = normalizeUsername(input.value);
    clearTimeout(timer);
    timer = setTimeout(async () => {
        const username = normalizeUsername(input.value);
        if (username.length < 3) {
            hint.textContent = 'Username minimal 3 karakter.';
            hint.className = 'form-text text-muted';
            return;
        }
        try {
            const res = await fetch(`{{ route('member.check-username') }}?username_member=${encodeURIComponent(username)}`);
            const data = await res.json();
            hint.textContent = data.message || '';
            hint.className = data.available ? 'form-text text-success' : 'form-text text-danger';
        } catch (e) {
            hint.textContent = 'Gagal cek username.';
            hint.className = 'form-text text-warning';
        }
    }, 300);
});
})();
</script>
</body>
</html>
