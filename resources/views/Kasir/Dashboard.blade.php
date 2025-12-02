@extends('layouts.app')

@section('title', 'Dashboard Kasir')

@section('content')
    <div class="container-fluid py-4">
        <!-- STATS CARDS -->
        <div class="dashboard-cards d-flex flex-wrap gap-4 mb-5">
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

        <!-- CHART SECTION -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow border-0">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 text-primary">
                            <i class="fas fa-chart-line me-2"></i> Aktivitas 7 Hari Terakhir
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($chartData['has_data'])
                            <!-- Jika ada data -->
                            <div class="chart-container" style="position: relative; height: 250px; width: 100%;">
                                <canvas id="kasirChart"></canvas>
                            </div>
                        @else
                            <!-- Jika tidak ada data -->
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-chart-bar fa-4x text-muted"></i>
                                </div>
                                <h5 class="text-muted mb-2">Belum ada data grafik</h5>
                                <p class="text-muted mb-4">
                                    Data grafik akan muncul setelah ada transaksi dalam beberapa hari.
                                </p>
                                <div class="row justify-content-center">
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body text-center">
                                                <div class="h4 text-primary mb-1">{{ $totalTransaksi }}</div>
                                                <div class="text-muted small">Transaksi Hari Ini</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- RECENT TRANSACTIONS -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow border-0">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-primary">
                            <i class="fas fa-history me-2"></i> Transaksi Terbaru
                        </h5>
                        <a href="{{ route('kasir.transaksi') }}" class="btn btn-sm btn-outline-primary">
                            Lihat Semua
                        </a>
                    </div>
                    <div class="card-body">
                        @php
                            $recentTransactions = \App\Models\Transaksi::with('member')
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get();
                        @endphp

                        @if ($recentTransactions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Member</th>
                                            <th>Total</th>
                                            <th>Poin</th>
                                            <th>Waktu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentTransactions as $transaksi)
                                            <tr>
                                                <td>#{{ $transaksi->id }}</td>
                                                <td>{{ $transaksi->member->nama ?? 'Guest' }}</td>
                                                <td>Rp {{ number_format($transaksi->total_pembelian, 0, ',', '.') }}</td>
                                                <td><span class="badge bg-success">{{ $transaksi->jumlah_poin }} poin</span>
                                                </td>
                                                <td>{{ $transaksi->created_at->format('H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center my-4">Belum ada transaksi hari ini</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($chartData['has_data'])
        <!-- Load Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            // Data untuk kasir
            const kasirCtx = document.getElementById('kasirChart').getContext('2d');
            const kasirChart = new Chart(kasirCtx, {
                type: 'line',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [{
                        label: 'Poin Harian',
                        data: @json($chartData['poin']),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }, {
                        label: 'Jumlah Transaksi',
                        data: @json($chartData['transaksi']),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        </script>
    @endif

    <style>
        .dashboard-cards {
            margin-bottom: 2rem;
        }

        .card-info {
            min-width: 200px;
            transition: transform 0.2s;
        }

        .card-info:hover {
            transform: translateY(-5px);
        }
    </style>
@endsection
