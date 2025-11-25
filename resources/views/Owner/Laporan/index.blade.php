@extends('layouts.app')

@section('title', 'Laporan Transaksi')

@section('content')
<div class="main-content">
    <div class="card shadow-sm p-4" style="background-color:#eaf6ff;">
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
                📤 Upload Laporan
            </a>
        </div>

        {{-- Filter Form --}}
        <form action="{{ route('owner.laporan.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="date" name="tanggal_dibuat" class="form-control"
                       value="{{ request('tanggal_dibuat') }}" placeholder="Filter tanggal">
            </div>
            <div class="col-md-3">
                <input type="text" name="periode_laporan" class="form-control"
                       value="{{ request('periode_laporan') }}" placeholder="Filter periode (e.g. November)">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">🔍 Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('owner.laporan.index') }}" class="btn btn-secondary w-100">🔄 Reset</a>
            </div>
        </form>

        {{-- Tabel --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-primary text-center">
                    <tr>
                        <th width="5%">No</th>
                        <th width="25%">Judul Laporan</th>
                        <th width="20%">Nama File</th>
                        <th width="15%">Tanggal Upload</th>
                        <th width="15%">Total Transaksi</th>
                        <th width="15%">Total Data</th>
                        <th width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- ✅ GUNAKAN @forelse UNTUK MULTIPLE DATA --}}
                    @forelse($laporans as $laporan)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $laporan->judul_laporan }}</td>
                        <td>{{ $laporan->file_name }}</td>
                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($laporan->created_at)->format('d-m-Y H:i') }}
                        </td>
                        <td class="text-end">
                            Rp {{ number_format($laporan->total_transaksi, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            {{ $laporan->total_data }} transaksi
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('owner.laporan.download', $laporan->id) }}" 
                                   class="btn btn-info" title="Download">
                                    Download
                                </a>
                                <form action="{{ route('owner.laporan.destroy', $laporan->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Hapus laporan {{ $laporan->judul_laporan }}?')" 
                                            class="btn btn-danger" title="Hapus">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    {{-- ✅ JIKA TIDAK ADA DATA --}}
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <div class="py-3">
                                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                <p class="mt-2 mb-0">Belum ada file laporan.</p>
                                <a href="{{ route('owner.laporan.create') }}" class="btn btn-primary mt-2">
                                    Upload Laporan
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Info Summary --}}
        @if($laporans->count() > 0)
        <div class="mt-3 p-3 bg-light rounded">
            <small class="text-muted">
                📊 Menampilkan <strong>{{ $laporans->count() }}</strong> laporan 
                dengan total <strong>{{ $laporans->sum('total_data') }}</strong> transaksi
            </small>
        </div>
        @endif
    </div>
</div>

<style>
.main-content {
    padding: 20px;
    background: #f8f9fa;
    min-height: calc(100vh - 80px);
}
.card {
    border: none;
    border-radius: 10px;
}
.table th {
    font-weight: 600;
}
.btn-group .btn {
    margin: 0 2px;
}
</style>
@endsection