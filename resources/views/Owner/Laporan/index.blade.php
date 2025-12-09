@extends('layouts.app')

@section('title', 'Laporan Transaksi')

@section('content')
    <div class="main-content">
        <div class="card shadow-sm p-4 bg-white">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold text-primary mb-0">Daftar Laporan</h4>
                    <small class="text-muted">Total Semua Transaksi: <strong>Rp
                            {{ number_format($totalSemuaTransaksi, 0, ',', '.') }}</strong></small>
                </div>
                <a href="{{ route('owner.laporan.create') }}" class="btn btn-success btn-sm px-3">
                    📤 Upload Laporan
                </a>
            </div>

            {{-- Filter --}}
            <form action="{{ route('owner.laporan.index') }}" method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <input type="date" name="tanggal_dibuat" class="form-control" value="{{ request('tanggal_dibuat') }}"
                        placeholder="Filter tanggal">
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

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>No</th>
                            <th>Judul Laporan</th>
                            <th>Nama File</th>
                            <th>Tanggal Upload</th>
                            <th>Waktu Upload</th>
                            <th>Total Transaksi</th>
                            <th>Total Data</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporans as $laporan)
                            <tr>
                                <td class="text-center fw-bold">
                                    {{ ($laporans->currentPage() - 1) * $laporans->perPage() + $loop->iteration }}
                                </td>
                                <td class="text-start">{{ $laporan->judul_laporan }}</td>
                                <td class="text-start">{{ $laporan->file_name }}</td>
                                <td class="text-center">{{ $laporan->created_at->format('d-m-Y') }}</td>
                                <td class="text-center">{{ $laporan->created_at->timezone('Asia/Jakarta')->format('H:i') }}
                                </td>
                                <td class="text-end fw-bold">
                                    Rp {{ number_format($laporan->total_transaksi, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info rounded-pill">{{ $laporan->total_data }} transaksi</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('owner.laporan.download', $laporan->id) }}"
                                            class="btn btn-info btn-sm" title="Download">
                                            <i class="fas fa-download me-1"></i> Download
                                        </a>
                                        <form action="{{ route('owner.laporan.destroy', $laporan->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                onclick="return confirm('Hapus laporan {{ $laporan->judul_laporan }}?')"
                                                class="btn btn-danger btn-sm" title="Hapus">
                                                <i class="fas fa-trash me-1"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <div class="py-3">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="mt-2 mb-0">Belum ada file laporan.</p>
                                        <a href="{{ route('owner.laporan.create') }}" class="btn btn-primary mt-2">
                                            <i class="fas fa-upload me-1"></i> Upload Laporan Pertama
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if ($laporans->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $laporans->links('pagination::bootstrap-5') }}
                </div>
            @endif

            {{-- Info Summary --}}
            @if ($laporans->count() > 0)
                <div class="mt-3 p-3 bg-light rounded">
                    <small class="text-muted">
                        📊 Menampilkan <strong>{{ $laporans->firstItem() }} - {{ $laporans->lastItem() }}</strong>
                        dari total <strong>{{ $laporans->total() }}</strong> laporan
                        dengan total <strong>{{ $laporans->sum('total_data') }}</strong> transaksi
                    </small>
                </div>
            @endif
        </div>
    </div>

    <style>
        /* FORCE TABLE ALIGNMENT */
        .table {
            width: 100% !important;
            table-layout: fixed !important;
            border-collapse: collapse !important;
            margin-bottom: 0 !important;
        }

        /* Force column widths dengan !important */
        .table th:nth-child(1),
        .table td:nth-child(1) {
            width: 5% !important;
            min-width: 50px !important;
            max-width: 50px !important;
            text-align: center !important;
        }

        .table th:nth-child(2),
        .table td:nth-child(2) {
            width: 20% !important;
            min-width: 200px !important;
            max-width: 200px !important;
            text-align: left !important;
        }

        .table th:nth-child(3),
        .table td:nth-child(3) {
            width: 20% !important;
            min-width: 200px !important;
            max-width: 200px !important;
            text-align: left !important;
        }

        .table th:nth-child(4),
        .table td:nth-child(4) {
            width: 15% !important;
            min-width: 120px !important;
            max-width: 120px !important;
            text-align: center !important;
        }

        .table th:nth-child(5),
        .table td:nth-child(5) {
            width: 15% !important;
            min-width: 120px !important;
            max-width: 120px !important;
            text-align: center !important;
        }

        .table th:nth-child(6),
        .table td:nth-child(6) {
            width: 15% !important;
            min-width: 120px !important;
            max-width: 120px !important;
            text-align: right !important;
        }

        .table th:nth-child(7),
        .table td:nth-child(7) {
            width: 10% !important;
            min-width: 100px !important;
            max-width: 100px !important;
            text-align: center !important;
        }

        .table th:nth-child(8),
        .table td:nth-child(8) {
            width: 15% !important;
            min-width: 150px !important;
            max-width: 150px !important;
            text-align: center !important;
        }

        /* Force table cells */
        .table td {
            vertical-align: middle !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            padding: 10px 8px !important;
        }

        /* Force header */
        .table thead th {
            text-align: center !important;
            vertical-align: middle !important;
            font-weight: 600 !important;
            background-color: #e3f2fd !important;
            padding: 12px 8px !important;
            border-bottom: 2px solid #dee2e6 !important;
        }

        /* Table responsive */
        .table-responsive {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            overflow-x: auto;
            background: white;
        }

        /* Main content styling */
        .main-content {
            padding: 20px;
            background: #f8f9fa;
            min-height: calc(100vh - 80px);
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .bg-white {
            background-color: white !important;
        }

        /* Button styling */
        .btn-group .btn {
            margin: 0 2px;
            border-radius: 4px;
        }

        .badge {
            font-size: 0.85em;
            padding: 0.35em 0.65em;
            border-radius: 10px;
        }
    </style>

@endsection
