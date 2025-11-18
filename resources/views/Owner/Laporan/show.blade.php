@extends('layouts.app')

@section('title', 'Detail Laporan')

@section('content')
<div class="main-content">
    <div class="card shadow-sm border-0 p-4" style="background-color:#eaf6ff;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-primary mb-0">Detail Laporan</h4>
            <a href="{{ route('owner.laporan.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <strong>Judul Laporan:</strong>
                    <p class="mb-0">{{ $laporan->judul_laporan }}</p>
                </div>

                <div class="mb-3">
                    <strong>Tanggal Laporan:</strong>
                    <p class="mb-0">{{ \Carbon\Carbon::parse($laporan->tanggal_laporan)->format('d F Y') }}</p>
                </div>

                <div class="mb-3">
                    <strong>Total Transaksi:</strong>
                    <p class="mb-0">Rp {{ number_format($laporan->total_transaksi, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <strong>Keterangan:</strong>
                    <p class="mb-0">{{ $laporan->keterangan ?: '-' }}</p>
                </div>

                <div class="mb-3">
                    <strong>File Lampiran:</strong>
                    <p class="mb-0">
                        @if($laporan->file_path)
                            <a href="{{ route('owner.laporan.download', $laporan->id) }}" 
                               class="btn btn-outline-primary btn-sm">
                               <i class="fas fa-download"></i> Download File
                            </a>
                            <small class="d-block text-muted">{{ $laporan->file_name }}</small>
                        @else
                            <span class="text-muted">Tidak ada file</span>
                        @endif
                    </p>
                </div>

                <div class="mb-3">
                    <strong>Dibuat Pada:</strong>
                    <p class="mb-0">{{ $laporan->created_at->format('d F Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection