@extends('layouts.app')

@section('title', 'Kelola Poin Member')
@section('page-title', 'Kelola Poin Member ☕')

@section('content')
<div class="card shadow-sm border-0 p-4" style="background-color:#eaf6ff;">


    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif


    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-primary mb-0">Daftar Poin Member</h4>
    </div>

    <table class="table table-bordered table-hover align-middle text-center shadow-sm">
        <thead class="table-primary">
            <tr>
                <th style="width:5%">No</th>
                <th style="width:30%">Nama Member</th>
                <th style="width:20%">Jumlah Poin</th>
                <th style="width:25%">Keterangan</th>
                <th style="width:20%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($poins as $index => $poin)
                <tr>
                    <td>{{ $index + $poins->firstItem() }}</td>
                    <td>{{ $poin->member->nama ?? '-' }}</td>
                    <td><span class="badge bg-primary fs-6">{{ $poin->jumlah_poin }}</span></td>
                    <td>{{ $poin->keterangan ?? '-' }}</td>
                    <td>
                        <a href="{{ route('poins.edit', $poin->id) }}" class="btn btn-warning btn-sm text-white">✏️ Ubah</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-3">Belum ada data poin.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- 🔄 Pagination --}}
    <div class="d-flex justify-content-end mt-3">
        {{ $poins->links() }}
    </div>
</div>
@endsection
