@extends('Owner.layouts.app')

@section('content')
<h3>Laporan Transaksi</h3>

<form method="GET" class="row mb-3">
    <div class="col-md-4">
        <input type="text" name="periode_laporan" class="form-control" placeholder="Cari periode..." value="{{ request('periode_laporan') }}">
    </div>
    <div class="col-md-4">
        <input type="date" name="tanggal_dibuat" class="form-control" value="{{ request('tanggal_dibuat') }}">
    </div>
    <div class="col-md-4">
        <button class="btn btn-primary w-100">Cari</button>
    </div>
</form>

<table class="table table-bordered">
    <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>Periode</th>
            <th>Tanggal Dibuat</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($laporans as $l)
        <tr>
            <td>{{ $l->id_laporan }}</td>
            <td>{{ $l->periode_laporan }}</td>
            <td>{{ $l->tanggal_dibuat }}</td>
            <td>
                <form action="{{ route('owner.laporan.destroy', $l->id_laporan) }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus laporan ini?')">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
