@extends('layouts.app')

@section('title', 'Detail Member')

@section('content')
<div class="main-content p-4" style="margin-left:220px; background-color:#f1f8ff; min-height:100vh;">
    <div class="card shadow-sm p-4" style="background-color:#eaf6ff; max-width:600px; margin:auto;">
        <h4 class="fw-bold text-primary mb-4 text-center">Detail Member</h4>

        <table class="table table-bordered bg-white">
            <tr><th style="width:40%">ID Member</th><td>{{ $member->id_member }}</td></tr>
            <tr><th>Nama</th><td>{{ $member->nama }}</td></tr>
            <tr><th>No HP</th><td>{{ $member->no_hp }}</td></tr>
            <tr><th>Poin</th><td><span class="badge bg-primary">{{ $member->poin }}</span></td></tr>
        </table>

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('members.index') }}" class="btn btn-secondary">Kembali</a>
            <a href="{{ route('members.edit', ['id' => $member->id_member]) }}" class="btn btn-warning">✏️ Edit</a>
        </div>
    </div>
</div>
@endsection
