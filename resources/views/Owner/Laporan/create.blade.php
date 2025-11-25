@extends('layouts.app')

@section('title', 'Upload Laporan')

@section('content')
<div class="main-content">
    <div class="card shadow-sm p-4" style="background-color:#eaf6ff;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-primary mb-0">Upload Laporan Baru</h4>
                <small class="text-muted">Upload file laporan transaksi</small>
            </div>
            <a href="{{ route('owner.laporan.index') }}" class="btn btn-secondary btn-sm px-3">
                Kembali
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('owner.laporan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- ✅ TAMBAH INPUT JUDUL LAPORAN -->
            <div class="mb-3">
                <label for="judul_laporan" class="form-label">Judul Laporan <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="judul_laporan" name="judul_laporan" 
                       value="{{ old('judul_laporan') }}" placeholder="Contoh: Laporan Transaksi November 2025" required>
                <div class="form-text">Berikan judul yang deskriptif untuk laporan ini</div>
            </div>

            <div class="mb-4">
                <label for="file_laporan" class="form-label">Pilih File Laporan <span class="text-danger">*</span></label>
                <input type="file" class="form-control" id="file_laporan" name="file_laporan" 
                       accept=".csv,.xlsx,.xls" required>
                <div class="form-text">
                    Format: CSV, Excel (maks. 10MB). 
                    <br><strong>Format CSV harus:</strong> ID Member, Total Pembelian, Tanggal
                </div>
            </div>

            <!-- ✅ INFO FORMAT FILE -->
            <div class="card bg-light mb-4">
                <div class="card-body">
                    <h6 class="card-title">Format File yang Didukung:</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>CSV Format:</strong>
                            <pre class="bg-dark text-light p-2 small">ID Member,Total Pembelian,Tanggal
579,150000,25/11/2025
295,200000,25/11/2025
271,50000,25/11/2025</pre>
                        </div>
                        <div class="col-md-6">
                            <strong>Excel Format:</strong>
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>ID Member</th>
                                        <th>Total Pembelian</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>579</td>
                                        <td>150000</td>
                                        <td>25/11/2025</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-upload me-2"></i>Upload Laporan
                </button>
                <a href="{{ route('owner.laporan.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
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
.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}
pre {
    border-radius: 5px;
    font-size: 12px;
}
</style>
@endsection