@extends('layouts.app')

@section('title', 'Laporan Transaksi')
@section('page-title', 'Laporan Transaksi')

@section('content')
<div class="card shadow-sm p-4" style="background-color:#eaf6ff;">
    
    <form action="{{ route('owner.laporan.index') }}" method="GET" class="d-flex mb-4" style="max-width: 400px;">
        <input type="date" name="tanggal_dibuat" class="form-control me-2"
               value="{{ request('tanggal_dibuat') }}">
        <button class="btn btn-primary">Filter</button>
    </form>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-primary text-center">
            <tr>
                <th>No</th>
                <th>Tanggal Dibuat</th>
                <th>Total Transaksi</th>
                <th>Total Poin</th>
                <th>Total Pendapatan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($laporans as $lap)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center">
                    {{ \Carbon\Carbon::parse($lap->tanggal_dibuat)->format('d-m-Y') }}
                </td>
                <td class="text-center">{{ $lap->total_transaksi }}</td>
                <td class="text-center">{{ $lap->total_poin }}</td>
                <td class="text-end">Rp {{ number_format($lap->total_pendapatan, 0, ',', '.') }}</td>
                <td class="text-center">
                    <form action="{{ route('owner.laporan.destroy', $lap->id_laporan) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button onclick="return confirm('Hapus laporan ini?')" class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted">Belum ada data laporan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
