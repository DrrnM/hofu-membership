@extends('Owner.layouts.app')

@section('content')
<h3>Edit Reward</h3>
<form method="POST" action="{{ route('owner.reward.update', $reward->id_reward) }}">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label>Nama Reward</label>
        <input type="text" name="nama_reward" class="form-control" value="{{ $reward->nama_reward }}" required>
    </div>
    <div class="mb-3">
        <label>Poin Diperlukan</label>
        <input type="number" name="poin_diperlukan" class="form-control" value="{{ $reward->poin_diperlukan }}" required>
    </div>
    <div class="mb-3">
        <label>Deskripsi</label>
        <textarea name="deskripsi" class="form-control">{{ $reward->deskripsi }}</textarea>
    </div>
    <button class="btn btn-primary">Update</button>
</form>
@endsection
