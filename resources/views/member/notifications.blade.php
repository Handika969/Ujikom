@extends('layouts.member')
@section('title','Notifikasi')
@section('content')
<div class="list-group">
    @forelse($notifikasi as $n)
        <div class="list-group-item"><strong>{{ $n->judul }}</strong><br>{{ $n->isi }}<div class="small text-muted">{{ $n->created_at }}</div></div>
    @empty
        <div class="list-group-item text-center">Belum ada notifikasi.</div>
    @endforelse
</div>
{{ $notifikasi->links() }}
@endsection
