@extends('Owner.layouts.app')

@section('content')
<h3>Tambah Reward</h3>
<form method="POST" action="{{ route('owner.reward.store') }}">
    @csrf
    <div class="mb-3">
        <label>Nama Reward</label>
        <input type="text" name="nama_reward" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Poin Diperlukan</label>
        <input type="number" name="poin_diperlukan" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Deskripsi</label>
        <textarea name="deskripsi" class="form-control"></textarea>
    </div>
    <button class="btn btn-success">Simpan</button>
</form>
@endsection
