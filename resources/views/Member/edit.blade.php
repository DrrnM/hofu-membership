@extends('layouts.app')

@section('title', 'Edit Member')

@section('content')
<div class="main-content p-4" style="margin-left:220px; background-color:#f1f8ff; min-height:100vh;">
    <div class="card shadow-sm p-4" style="background-color:#eaf6ff; max-width:600px; margin:auto;">
        <h4 class="fw-bold text-primary mb-4 text-center">Edit Member</h4>

        <form action="{{ route('members.update', ['id' => $member->id_member]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="nama" class="form-control" value="{{ old('nama', $member->nama) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">No HP</label>
                <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $member->no_hp) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Poin</label>
                <input type="number" name="poin" class="form-control" value="{{ old('poin', $member->poin) }}" min="0">
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('members.index') }}" class="btn btn-secondary">⬅ Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
