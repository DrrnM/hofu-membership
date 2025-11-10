@extends('layouts.app')

@section('title', 'Ubah Poin Member')
@section('page-title', 'Ubah Poin Member ☕')

@section('content')
<div class="card shadow-sm border-0 p-4" style="background-color:#eaf6ff; max-width:600px; margin:auto;">
    <h4 class="fw-bold text-primary mb-3 text-center">Ubah Poin Member</h4>

    <form action="{{ route('poins.update', $poin->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Nama Member --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Nama Member</label>
            <input type="text" class="form-control" value="{{ $poin->member->nama ?? '-' }}" readonly>
        </div>

        {{-- Jumlah Poin --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Jumlah Poin</label>
            <input type="number" name="jumlah_poin" class="form-control" 
                   value="{{ old('jumlah_poin', $poin->jumlah_poin) }}" required>
            @error('jumlah_poin')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Keterangan --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Keterangan</label>
            <input type="text" name="keterangan" class="form-control"
                   value="{{ old('keterangan', $poin->keterangan) }}">
            @error('keterangan')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Tombol --}}
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('poins.index') }}" class="btn btn-secondary px-4">⬅ Kembali</a>
            <button type="submit" class="btn btn-primary px-4">💾 Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
