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

        <form action="{{ route('owner.laporan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="file_laporan" class="form-label">Pilih File Laporan</label>
                <input type="file" class="form-control" id="file_laporan" name="file_laporan" 
                       accept=".csv,.xlsx,.xls" required>
                <div class="form-text">Format: CSV, Excel (maks. 2MB)</div>
            </div>
            <button type="submit" class="btn btn-success">Upload Laporan</button>
            <a href="{{ route('owner.laporan.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection