@extends('layouts.app')

@section('title', 'Dashboard Owner')

@section('content')

  <div class="dashboard-cards d-flex flex-wrap gap-4">
    <div class="card-info flex-fill text-center p-4 rounded shadow-sm" style="background-color: #d4ecff;">
      <h5 class="fw-bold text-secondary mb-2">Total Member</h5>
      <span class="fs-3 fw-bold text-primary">{{ $totalMember ?? 0 }}</span>
    </div>

    <div class="card-info flex-fill text-center p-4 rounded shadow-sm" style="background-color: #c7f9cc;">
      <h5 class="fw-bold text-secondary mb-2">Total Poin</h5>
      <span class="fs-3 fw-bold text-success">{{ $totalPoin ?? 0 }}</span>
    </div>

    <div class="card-info flex-fill text-center p-4 rounded shadow-sm" style="background-color: #fff3cd;">
      <h5 class="fw-bold text-secondary mb-2">Transaksi Hari Ini</h5>
      <span class="fs-3 fw-bold text-warning">{{ $totalTransaksi ?? 0 }}</span>
    </div>
  </div>
</div>
@endsection
