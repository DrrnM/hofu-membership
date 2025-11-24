@extends('layouts.app')

@section('title', 'Edit Poin Member')

@section('content')
<div class="main-content p-4" style="margin-left:220px; background-color:#f1f8ff; min-height:100vh;">
    <div class="card shadow-sm p-4" style="background-color:#eaf6ff; max-width:600px; margin:auto;">
        <h4 class="fw-bold text-primary mb-4 text-center">Edit Poin - {{ $member->nama }}</h4>

        <div class="mb-3 p-3 bg-white rounded">
            <p class="mb-1"><strong>ID Member:</strong> {{ $member->id_member }}</p>
            <p class="mb-1"><strong>Nama:</strong> {{ $member->nama }}</p>
            <p class="mb-0"><strong>Poin Saat Ini:</strong> <span class="badge bg-primary">{{ $member->poin }}</span></p>
            <p class="mb-0"><strong>Tier:</strong> <span class="badge bg-{{ $member->getColorBadge() }}">{{ $member->getLabelLangganan() }}</span></p>
        </div>

        <form action="{{ route('poins.update', $member->id_member) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Jumlah Poin Baru</label>
                <input type="number" name="poin" class="form-control" 
                       value="{{ old('poin', $member->poin) }}" min="0" required>
                <small class="text-muted">Update poin member secara langsung</small>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('poins.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Update Poin</button>
            </div>
        </form>
    </div>
</div>
@endsection