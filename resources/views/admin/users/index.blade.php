@extends('layouts.master')
@section('title','Admin - Data User')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Manajemen User</h4>
    <span class="text-muted small">Kelola akun admin, petugas, owner</span>
</div>
<div class="card panel mb-3">
    <div class="card-header bg-white fw-semibold">Tambah User Baru</div>
    <div class="card-body">
        <form class="row g-3" method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="col-md-3"><input class="form-control" name="nama_lengkap" placeholder="Nama lengkap" required></div>
            <div class="col-md-2"><input class="form-control" name="username" placeholder="Username" required></div>
            <div class="col-md-2"><input type="password" class="form-control" name="password" placeholder="Password" required></div>
            <div class="col-md-2"><select class="form-select" name="role"><option>admin</option><option>petugas</option><option>owner</option></select></div>
            <div class="col-md-2"><select class="form-select" name="status_aktif"><option value="1">Aktif</option><option value="0">Nonaktif</option></select></div>
            <div class="col-md-1 d-grid"><button class="btn btn-primary rounded-pill">Simpan</button></div>
        </form>
    </div>
</div>
<div class="card panel">
<div class="card-header bg-white fw-semibold">Daftar User</div>
<div class="table-responsive">
<table class="table table-sm table-hover mb-0 align-middle">
    <thead class="table-light"><tr><th>ID</th><th>Nama</th><th>Username</th><th>Role</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($users as $u)
        <tr>
            <td>{{ $u->id_user }}</td>
            <td>{{ $u->nama_lengkap }}</td>
            <td>{{ $u->username }}</td>
            <td><span class="badge bg-primary-subtle text-primary">{{ strtoupper($u->role) }}</span></td>
            <td>
                @if((int)$u->status_aktif === 1)
                    <span class="badge bg-success-subtle text-success">Aktif</span>
                @else
                    <span class="badge bg-secondary-subtle text-secondary">Nonaktif</span>
                @endif
            </td>
            <td>
                <form method="POST" action="{{ route('admin.users.update', $u->id_user) }}" class="d-flex gap-1 mb-1 flex-wrap">
                    @csrf @method('PUT')
                    <input class="form-control form-control-sm" style="min-width:180px" name="nama_lengkap" value="{{ $u->nama_lengkap }}" required>
                    <input class="form-control form-control-sm" style="min-width:140px" name="username" value="{{ $u->username }}" required>
                    <input type="password" class="form-control form-control-sm" style="min-width:180px" name="password" placeholder="Password baru (opsional)">
                    <select class="form-select form-select-sm" name="role"><option @selected($u->role==='admin')>admin</option><option @selected($u->role==='petugas')>petugas</option><option @selected($u->role==='owner')>owner</option></select>
                    <select class="form-select form-select-sm" name="status_aktif"><option value="1" @selected((int)$u->status_aktif===1)>Aktif</option><option value="0" @selected((int)$u->status_aktif===0)>Nonaktif</option></select>
                    <button class="btn btn-sm btn-warning rounded-pill">Update</button>
                </form>
                <form method="POST" action="{{ route('admin.users.delete', $u->id_user) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('Hapus user ini?')">Hapus</button></form>
            </td>
        </tr>
    @empty
        <tr><td colspan="6" class="text-center">Data user kosong.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
</div>
<div class="mt-2">{{ $users->links() }}</div>
@endsection
