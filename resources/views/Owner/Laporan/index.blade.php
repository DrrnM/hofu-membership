@extends('Owner.layouts.app')
@section('title', 'Laporan Transaksi')
@section('page-title', '📈 Laporan Transaksi')

@section('content')
<div class="card shadow-sm p-4">
    <form action="{{ route('owner.laporan.index') }}" method="GET" class="d-flex mb-4" style="max-width: 400px;">
        <input type="text" name="search" class="form-control me-2" placeholder="Cari nama member..." value="{{ request('search') }}">
        <button class="btn btn-primary">🔍</button>
    </form>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-primary text-center">
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Member</th>
                <th>Total Transaksi</th>
                <th>Poin</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($laporan as $lap)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center">{{ $lap->tanggal }}</td>
                <td>{{ $lap->member->nama ?? '-' }}</td>
                <td class="text-end">Rp {{ number_format($lap->total_transaksi, 0, ',', '.') }}</td>
                <td class="text-center">{{ $lap->poin_diperoleh }}</td>
                <td class="text-center">
                    <form action="{{ route('owner.laporan.destroy', $lap->id_laporan) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Hapus laporan ini?')" class="btn btn-danger btn-sm">🗑️</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted">Belum ada data laporan</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
