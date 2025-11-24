@extends('layouts.app')

@section('title', 'Daftar Poin Member')

@section('content')
    <div class="card shadow-sm border-0 p-4" style="background-color:#eaf6ff;">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold text-primary mb-0">Daftar Poin Member</h4>
            
            {{-- SEARCH FORM --}}
            <form action="{{ route('poins.index') }}" method="GET" class="d-flex align-items-center" style="max-width: 300px;">
                <input type="text" name="search" class="form-control form-control-sm" 
                       placeholder="Cari ID atau Nama..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary btn-sm ms-2">Cari</button>
                @if(request('search'))
                    <a href="{{ route('poins.index') }}" class="btn btn-outline-secondary btn-sm ms-2">Clear</a>
                @endif
            </form>

            <span class="text-muted">
                Total: {{ $members->total() }} member
            </span>
        </div>

        {{-- HASIL PENCARIAN --}}
        @if(request('search'))
            <div class="alert alert-info mb-3">
                Hasil pencarian untuk: <strong>"{{ request('search') }}"</strong> 
                ({{ $members->total() }} hasil ditemukan)
            </div>
        @endif

        <table class="table table-bordered table-hover align-middle text-center shadow-sm">
            <thead class="table-primary">
                <tr>
                    <th style="width:5%">No</th>
                    <th style="width:15%">ID Member</th>
                    <th style="width:25%">Nama Member</th>
                    <th style="width:15%">No HP</th>
                    <th style="width:15%">Total Poin</th>
                    <th style="width:15%">Tier</th>
                    <th style="width:10%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $index => $member)
                    <tr>
                        <td>{{ $index + $members->firstItem() }}</td>
                        <td>
                            <strong>{{ $member->id_member }}</strong>
                        </td>
                        <td class="text-start">
                            {{ $member->nama }}
                        </td>
                        <td>
                            {{ $member->no_hp ?? '-' }}
                        </td>
                        <td>
                            <span class="badge bg-primary fs-6">
                                {{ $member->poin }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $member->getColorBadge() }}">
                                {{ $member->getLabelLangganan() }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('members.update-poin', $member->id_member) }}"
                                class="btn btn-success btn-sm text-white">
                                <i class="fas fa-plus-minus"></i> Update Poin
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">
                            @if(request('search'))
                                Tidak ada data member yang cocok dengan pencarian "{{ request('search') }}"
                            @else
                                Belum ada data member.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@if($members->hasPages())
    @section('pagination_info')
        Menampilkan {{ $members->firstItem() ?? 0 }} - {{ $members->lastItem() ?? 0 }} 
        dari {{ $members->total() }} data poin
        @if(request('search'))
            untuk pencarian "{{ request('search') }}"
        @endif
    @endsection

    @section('pagination_buttons')
        {{-- Previous Button --}}
        @if (!$members->onFirstPage())
            <a href="{{ $members->previousPageUrl() }}" class="btn btn-primary btn-sm px-3 py-1">
                ‹
            </a>
        @endif
        
        {{-- Next Button --}}
        @if ($members->hasMorePages())
            <a href="{{ $members->nextPageUrl() }}" class="btn btn-primary btn-sm px-3 py-1">
                ›
            </a>
        @endif
    @endsection
@endif