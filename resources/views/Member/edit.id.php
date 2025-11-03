@extends('layouts.app')

@section('title', 'Edit Member')
@section('page-title', '✏️ Edit Member')

@section('content')
<div class="main-content p-4">
    <div class="card shadow-sm border-0 p-4" style="background-color:#eaf6ff; max-width:600px; margin:auto;">
        <h4 class="fw-bold text-primary mb-3 text-center">Edit Data Member</h4>

        <form action="{{ route('members.update', $member->id_member) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nama" class="form-label fw-semibold">Nama Member</label>
                <input type="text" name="nama" id="nama" class="form-control"
                       value="{{ old('nama', $member->nama) }}" required>
                @error('nama')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label for="no_hp" class="form-label fw-semibold">No. Telepon</label>
                <input type="text" name="no_hp" id="no_hp" class="form-control"
                       value="{{ old('no_hp', $member->no_hp) }}" required>
                @error('no_hp')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label for="poin" class="form-label fw-semibold">Poin</label>
                <input type="number" name="poin" id="poin" class="form-control"
                       value="{{ old('poin', $member->poin) }}" min="0" required>
                @error('poin')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('members.index') }}" class="btn btn-secondary px-4">⬅ Kembali</a>
                <button type="submit" class="btn btn-primary px-4">💾 Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
