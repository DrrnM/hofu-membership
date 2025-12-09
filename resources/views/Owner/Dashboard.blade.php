@extends('layouts.app')

@section('title', 'Dashboard Owner')

@section('content')
    <div class="container-fluid py-4">
        <!-- STATISTIK CARD -->
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
                <h5 class="fw-bold text-secondary mb-2">Total Transaksi</h5>
                <span class="fs-3 fw-bold text-warning">{{ $totalTransaksi ?? 0 }}</span>
            </div>
        </div>

        <!-- CHART SECTION -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow border-0">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-primary">
                            <i class="fas fa-chart-bar me-2"></i> Grafik Transaksi per Bulan
                            <small class="text-muted ms-2" id="chartPeriod">({{ $chartPeriod }})</small>
                        </h5>
                        <div class="btn-group btn-group-sm">
                            <button type="button"
                                class="btn btn-outline-primary {{ $filter == 'all_time' ? 'active' : '' }}"
                                onclick="updateChart('all_time')">Semua Waktu</button>

                            <button type="button"
                                class="btn btn-outline-primary {{ $filter == 'current_year' ? 'active' : '' }}"
                                onclick="updateChart('current_year')">Tahun Ini</button>

                            <button type="button"
                                class="btn btn-outline-primary {{ $filter == 'last_year' ? 'active' : '' }}"
                                onclick="updateChart('last_year')">Tahun Lalu</button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                            <canvas id="ownerChart"></canvas>
                        </div>

                        <!-- Info statistik -->
                        <div class="row text-center mt-4">
                            <div class="col-md-4">
                                <div class="card border-0 bg-light">
                                    <div class="card-body py-3">
                                        <div class="h4 text-primary mb-1" id="totalTransaksiCount"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 bg-light">
                                    <div class="card-body py-3">
                                        <div class="h4 text-success mb-1" id="maxTransaksiMonth"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 bg-light">
                                    <div class="card-body py-3">
                                        <div class="h4 text-info mb-1" id="avgTransaksi"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

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
                    </div>
                    <div class="card-body">
                        @if ($recentTransactions && $recentTransactions->count() > 0)
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
                                                <td><span class="badge bg-success">{{ $transaksi->jumlah_poin }}
                                                        poin</span>
                                                </td>
                                                <td>{{ $transaksi->created_at->format('d M H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center my-4">Belum ada transaksi</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const initialData = {
            labels: @json($chartData['labels'] ?? []),
            transaksi: @json($chartData['transaksi'] ?? [])
        };

        let ownerChart = null;

        function initChart() {
            const ctx = document.getElementById('ownerChart').getContext('2d');

            ownerChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: initialData.labels,
                    datasets: [{
                        label: 'Jumlah Transaksi',
                        data: initialData.transaksi,
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#4e73df',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        function updateChart(filter) {
            document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

            const periodMap = {
                'all_time': 'Semua Waktu',
                'current_year': 'Tahun Ini',
                'last_year': 'Tahun Lalu'
            };
            document.getElementById('chartPeriod').textContent = `(${periodMap[filter]})`;

            fetch(`{{ url('owner/chart-data') }}?filter=${filter}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    ownerChart.data.labels = data.labels;
                    ownerChart.data.datasets[0].data = data.data;
                    ownerChart.update();

                    document.getElementById('totalTransaksiCount').textContent =
                        data.data.reduce((a, b) => a + b, 0);
                    document.getElementById('maxTransaksiMonth').textContent =
                        Math.max(...data.data);
                    document.getElementById('avgTransaksi').textContent =
                        (data.data.reduce((a, b) => a + b, 0) / data.data.length).toFixed(1);
                });
        }

        document.addEventListener('DOMContentLoaded', initChart);
    </script>

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

        .btn-group .btn {
            min-width: 100px;
        }

        .btn-group .btn.active {
            background-color: #4e73df;
            border-color: #4e73df;
            color: white;
        }

        .chart-container {
            background: white;
            border-radius: 8px;
            padding: 10px;
        }

        table.table-sm th {
            font-size: 13px;
            color: #6c757d;
        }

        table.table-sm td {
            font-size: 14px;
            vertical-align: middle;
        }

        .badge {
            font-size: 12px;
            padding: 4px 8px;
        }
    </style>

@endsection
