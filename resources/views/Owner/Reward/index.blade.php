@extends('layouts.app')
@section('title', 'Daftar Reward')
@section('page-title', 'Daftar Reward')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <form action="{{ route('owner.reward.index') }}" method="GET" class="d-flex" style="max-width: 350px;">
        <input type="text" name="search" class="form-control me-2" placeholder="Cari reward..." value="{{ request('search') }}">
        <button class="btn btn-primary">🔍</button>
    </form>
    <a href="{{ route('owner.reward.create') }}" class="btn btn-success">Tambah Reward</a>
</div>

<table class="table table-bordered table-striped align-middle">
    <thead class="table-primary text-center">
        <tr>
            <th>No</th>
            <th>Nama Reward</th>
            <th>Poin Diperlukan</th>
            <th>Deskripsi</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($rewards as $r)
        <tr>
            <td class="text-center">{{ $loop->iteration }}</td>
            <td>{{ $r->nama_reward }}</td>
            <td class="text-center">{{ $r->poin_diperlukan }}</td>
            <td>{{ $r->deskripsi }}</td>
            <td class="text-center">
                <a href="{{ route('owner.reward.show', $r->id_reward) }}" class="btn btn-info btn-sm">Tampil</a>
                <a href="{{ route('owner.reward.edit', $r->id_reward) }}" class="btn btn-warning btn-sm">Edit</a>
                <form action="{{ route('owner.reward.destroy', $r->id_reward) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button onclick="return confirm('Yakin hapus reward ini?')" class="btn btn-danger btn-sm">🗑️</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center text-muted">Belum ada reward</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
