@extends('layouts.app')

@section('title', 'Kelola Member')

@section('content')
<div class="main-content" style="margin-left: 220px; padding: 20px; background-color: #f1f8ff;">
    <div class="card shadow-sm border-0 p-4" style="background-color:#eaf6ff;">
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold text-primary mb-0">Kelola Member</h4>
         
            <form action="{{ route('members.index') }}" method="GET" class="d-flex align-items-center" style="max-width: 300px;">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Cari ID" value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary btn-sm ms-2">Cari</button>
            </form>

            <a href="{{ route('members.create') }}" class="btn btn-success btn-sm px-3">Tambah</a>
        </div>

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
                        <!-- PERBAIKAN: ganti $member->id menjadi $member->id_member -->
                        <a href="{{ route('members.show', $member->id_member) }}" 
                           class="btn btn-info btn-sm text-white">Tampil</a>

                        <a href="{{ route('members.edit', $member->id_member) }}" 
                           class="btn btn-warning btn-sm">Ubah</a>

                        <form action="{{ route('members.destroy', $member->id_member) }}" 
                              method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Yakin ingin menghapus member ini?')" 
                                    class="btn btn-danger btn-sm">
                                Hapus
                            </button>
                        </form>
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
</div>
@endsection