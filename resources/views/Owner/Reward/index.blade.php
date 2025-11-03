@extends('Owner.layouts.app')

@section('content')
<h3>Daftar Reward</h3>
<a href="{{ route('owner.reward.create') }}" class="btn btn-primary mb-3">+ Tambah Reward</a>

<table class="table table-bordered">
    <thead class="table-light">
        <tr>
            <th>Nama Reward</th>
            <th>Poin</th>
            <th>Deskripsi</th>
            <th>Tanggal Dibuat</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rewards as $r)
        <tr>
            <td>{{ $r->nama_reward }}</td>
            <td>{{ $r->poin_diperlukan }}</td>
            <td>{{ $r->deskripsi }}</td>
            <td>{{ $r->tanggal_dibuat }}</td>
            <td>
                <a href="{{ route('owner.reward.edit', $r->id_reward) }}" class="btn btn-warning btn-sm">Edit</a>
                <form action="{{ route('owner.reward.destroy', $r->id_reward) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button onclick="return confirm('Yakin hapus reward ini?')" class="btn btn-danger btn-sm">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
