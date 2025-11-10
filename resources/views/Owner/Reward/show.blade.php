@extends('layouts.app')
@section('title', 'Detail Reward')
@section('page-title', 'Tampil')

@section('content')
<div class="card shadow-sm p-4">
    <h5 class="fw-bold">{{ $reward->nama_reward }}</h5>
    <p><strong>Poin Diperlukan:</strong> {{ $reward->poin_diperlukan }}</p>
    <p><strong>Deskripsi:</strong> {{ $reward->deskripsi }}</p>

    <div class="mt-4">
        <a href="{{ route('owner.reward.edit', $reward->id_reward) }}" class="btn btn-warning">Edit</a>
        <a href="{{ route('owner.reward.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>
@endsection
