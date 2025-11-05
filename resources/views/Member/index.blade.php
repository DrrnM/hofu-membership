@extends('layouts.app')
@section('title', 'Kelola Member')
@section('content')

<div class="card shadow-sm p-4" style="background-color:#eaf6ff;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-primary">Kelola Member</h4>
        <form action="{{ route('members.index') }}" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Cari berdasarkan nama / ID..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary btn-sm ms-2">🔍</button>
        </form>
        <a href="{{ route('members.create') }}" class="btn btn-success btn-sm px-3">+ Tambah</a>
    </div>

    <table class="table table-striped table-hover align-middle text-center shadow-sm">
        <thead style="background-color:#b5e0ff;">
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>No HP</th>
                <th>Poin</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($members as $member)
            <tr>
                <td>{{ $member->id_member }}</td>
                <td>{{ $member->nama }}</td>
                <td>{{ $member->no_hp }}</td>
                <td><span class="badge bg-primary">{{ $member->poin }}</span></td>
                <td>
                    <a href="{{ route('members.show', $member->id_member) }}" class="btn btn-info btn-sm text-white">👁️</a>
                    <a href="{{ route('members.edit', $member->id_member) }}" class="btn btn-warning btn-sm">✏️</a>
                    <form action="{{ route('members.destroy', $member->id_member) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus member ini?')">🗑️</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-muted">Belum ada data member.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
