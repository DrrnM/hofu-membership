@extends('layouts.app')

@section('title', 'Kelola Member')

@section('content')
<div class="card shadow-sm border-0 p-4" style="background-color:#eaf6ff;">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-primary mb-0">Kelola Member</h4>
     
        <form action="{{ route('members.index') }}" method="GET" class="d-flex align-items-center" style="max-width: 300px;">
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Cari ID, atau Nama" value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary btn-sm ms-2">Cari</button>
        </form>

        <a href="{{ route('members.create') }}" class="btn btn-success btn-sm px-3">Tambah Member</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle text-center shadow-sm bg-white">
            <thead class="table-primary">
                <tr>
                    <th style="width:15%">ID Member</th>
                    <th style="width:25%">Nama</th>
                    <th style="width:20%">No HP</th>
                    <th style="width:15%">Poin</th>
                    <th style="width:25%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $member)
                <tr>
                    <td class="fw-semibold">{{ $member->member_id }}</td>
                    <td class="text-start">{{ $member->nama }}</td>
                    <td>{{ $member->no_hp }}</td>
                    <td>
                        <span class="badge bg-primary fs-6">{{ $member->poin }}</span>
                    </td>
                    <td>
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="{{ route('members.show', $member->member_id) }}" 
                               class="btn btn-info btn-sm text-white px-3">Tampil</a>
                            <a href="{{ route('members.edit', $member->member_id) }}" 
                               class="btn btn-warning btn-sm px-3">Ubah</a>
                            
                            {{-- PERBAIKAN: Form dengan confirmation --}}
                            <form action="{{ route('members.destroy', $member->member_id) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Hapus member {{ $member->nama }}?\\n\\nSemua data poin dan transaksi juga akan dihapus!')"
                                  class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm px-3">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        Belum ada data member.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($members->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted">
            Menampilkan {{ $members->firstItem() ?? 0 }} - {{ $members->lastItem() ?? 0 }} 
            dari {{ $members->total() }} member
        </div>
        <div class="d-flex gap-2">
            @if (!$members->onFirstPage())
                <a href="{{ $members->previousPageUrl() }}" class="btn btn-primary btn-sm px-3 py-1">
                    ‹
                </a>
            @endif
            
            @if ($members->hasMorePages())
                <a href="{{ $members->nextPageUrl() }}" class="btn btn-primary btn-sm px-3 py-1">
                    ›
                </a>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection