@extends('layouts.app')

@section('title', 'Laporan Transaksi')

@section('content')
<div class="main-content">
    <div class="card shadow-sm p-4" style="background-color:#eaf6ff;">
        
        {{-- Alert untuk success/error message --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                ✅ {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                ❌ {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-primary mb-0">Daftar Laporan</h4>
                <small class="text-muted">Total Semua Transaksi: <strong>Rp {{ number_format($totalSemuaTransaksi, 0, ',', '.') }}</strong></small>
            </div>
            <a href="{{ route('owner.laporan.create') }}" class="btn btn-success btn-sm px-3">
                Upload Laporan
            </a>
        </div>

        <form action="{{ route('owner.laporan.index') }}" method="GET" class="d-flex mb-4" style="max-width: 400px;">
            <input type="date" name="tanggal_dibuat" class="form-control me-2"
                   value="{{ request('tanggal_dibuat') }}">
            <button class="btn btn-primary">Filter</button>
        </form>

        {{-- Tabel --}}
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-primary text-center">
                <tr>
                    <th>No</th>
                    <th>Nama File</th>
                    <th>Tanggal Upload</th>
                    <th>Total Transaksi</th>
                    <th>Aksi</th> {{-- TAMBAH KOLOM INI --}}
                </tr>
            </thead>
            <tbody>
                @forelse ($laporans as $laporan)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $laporan->file_name }}</td>
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($laporan->created_at)->format('d-m-Y H:i') }}
                    </td>
                    <td class="text-center">
                        {{-- TAMPILKAN TOTAL TRANSAKSI --}}
                        Rp {{ number_format($laporan->total_transaksi, 0, ',', '.') }}
                    </td>
                    <td class="text-center">
                        <a href="{{ route('owner.laporan.download', $laporan->id) }}" 
                           class="btn btn-info btn-sm">Download</a>
                        
                        <form action="{{ route('owner.laporan.destroy', $laporan->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Hapus laporan ini?')" 
                                    class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">Belum ada file laporan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection