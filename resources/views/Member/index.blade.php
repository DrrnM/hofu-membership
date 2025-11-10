@extends('layouts.app')
<<<<<<< HEAD

@section('title', 'Kelola Member')

@section('content')
<div class="main-content" style="margin-left: 220px; padding: 20px; background-color: #f1f8ff;">
    <div class="card shadow-sm border-0 p-4" style="background-color:#eaf6ff;">
        {{-- 🧭 Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold text-primary mb-0">👥 Kelola Member</h4>

            {{-- 🔍 Form Pencarian --}}
            <form action="{{ route('members.index') }}" method="GET" class="d-flex align-items-center" style="max-width: 300px;">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Cari ID / Nama / No HP..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary btn-sm ms-2">🔍</button>
            </form>

            {{-- ➕ Tambah (Hanya untuk Owner) --}}
            @if(Auth::user()->username === 'owner')
                <a href="{{ route('members.create') }}" class="btn btn-success btn-sm px-3">➕ Tambah</a>
            @endif
        </div>

        {{-- 📋 Tabel Data Member --}}
        <table class="table table-hover align-middle text-center shadow-sm">
            <thead class="table-primary">
                <tr>
                    <th style="width:10%">ID</th>
                    <th style="width:25%">Nama</th>
                    <th style="width:25%">No HP</th>
                    <th style="width:15%">Poin</th>
                    <th style="width:25%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $member)
                <tr>
                    <td class="fw-semibold">{{ $member->id_member }}</td>
                    <td>{{ $member->nama }}</td>
                    <td>{{ $member->no_hp }}</td>
                    <td><span class="badge bg-primary">{{ $member->poin }}</span></td>
                    <td>
                        {{-- 👁️ Tampil --}}
                        <a href="{{ route('members.show', $member->id_member) }}" class="btn btn-info btn-sm text-white">👁️</a>

                        {{-- ✏️ Edit & 🗑️ Hapus (Owner saja) --}}
                        @if(Auth::user()->username === 'owner')
                            <a href="{{ route('members.edit', $member->id_member )}}" class="btn btn-warning btn-sm">✏️</a>
                            <form action="{{ route('members.destroy', $member->id_member )}}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Yakin ingin menghapus member ini?')" class="btn btn-danger btn-sm">
                                    🗑️
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-3">Belum ada data member.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
=======
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
>>>>>>> b560b58e9f7f9befe66325960fcc942b88e970a4
</div>
@endsection
