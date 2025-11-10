@extends('layouts.app')
@section('title', 'Edit Reward')
@section('page-title', 'Edit Reward')

@section('content')
<div class="card shadow-sm p-4">
    <form action="{{ route('owner.reward.update', $reward->id_reward) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nama_reward" class="form-label">Nama Reward</label>
            <input type="text" name="nama_reward" class="form-control" value="{{ $reward->nama_reward }}" required>
        </div>

        <div class="mb-3">
            <label for="poin_diperlukan" class="form-label">Poin Diperlukan</label>
            <input type="number" name="poin_diperlukan" class="form-control" value="{{ $reward->poin_diperlukan }}" required>
        </div>

        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi Reward</label>
            <textarea name="deskripsi" class="form-control" rows="4" required>{{ $reward->deskripsi }}</textarea>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('owner.reward.index') }}" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
