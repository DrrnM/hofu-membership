@extends('Owner.layouts.app')
@section('title', 'Tambah Reward')
@section('page-title', '➕ Tambah Reward')

@section('content')
<div class="card shadow-sm p-4">
    <form action="{{ route('owner.reward.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nama_reward" class="form-label">Nama Reward</label>
            <input type="text" name="nama_reward" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="poin_diperlukan" class="form-label">Poin Diperlukan</label>
            <input type="number" name="poin_diperlukan" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi Reward</label>
            <textarea name="deskripsi" class="form-control" rows="4" required></textarea>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('owner.reward.index') }}" class="btn btn-secondary">⬅ Kembali</a>
            <button type="submit" class="btn btn-success">💾 Simpan</button>
        </div>
    </form>
</div>
@endsection
