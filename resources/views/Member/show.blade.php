@extends('layouts.app')
@section('title', 'Detail Member')
@section('content')
<div class="main-content p-4">
    <div class="card shadow-sm p-4" style="background-color:#eaf6ff; max-width:600px; margin:auto;">
        <h4 class="fw-bold text-primary mb-3 text-center">Detail Member</h4>
        <p><strong>ID:</strong> {{ $member->id_member }}</p>
        <p><strong>Nama:</strong> {{ $member->nama }}</p>
        <p><strong>No HP:</strong> {{ $member->no_hp }}</p>
        <p><strong>Poin:</strong> {{ $member->poin }}</p>
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('members.index') }}" class="btn btn-secondary">⬅ Kembali</a>
            <a href="{{ route('members.edit', $member->id_member) }}" class="btn btn-warning">✏️ Edit</a>
        </div>
    </div>
</div>
@endsection
<<<<<<< HEAD
@extends('layouts.app')

@section('title', 'Detail Member')

@section('content')
<div class="card shadow-sm p-4">
    <h4 class="fw-bold text-primary mb-3">👤 Detail Member</h4>
    <table class="table table-bordered">
        <tr><th>ID Member</th><td>{{ $member->id_member }}</td></tr>
        <tr><th>Nama</th><td>{{ $member->nama }}</td></tr>
        <tr><th>No HP</th><td>{{ $member->no_hp }}</td></tr>
        <tr><th>Poin</th><td>{{ $member->poin }}</td></tr>
    </table>
    <a href="{{ route('members.index') }}" class="btn btn-secondary mt-3">⬅ Kembali</a>
</div>
@endsection
=======
>>>>>>> b560b58e9f7f9befe66325960fcc942b88e970a4
