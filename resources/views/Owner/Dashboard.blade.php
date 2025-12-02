{{-- resources/views/Owner/Dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Owner')

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

        <!-- CHART SECTION - BULANAN SAJA -->
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card shadow border-0">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-primary">
                            <i class="fas fa-chart-line me-2"></i> Grafik Poin per Bulan
                        </h5>
                        <div class="btn-group btn-group-sm">
                            {{-- URUTAN BERUBAH: Semua Waktu dulu --}}
                            <button type="button" class="btn btn-outline-primary active"
                                onclick="updateChart('all_time')">Semua Waktu</button>
                            <button type="button" class="btn btn-outline-primary"
                                onclick="updateChart('current_year')">Tahun Ini</button>
                            <button type="button" class="btn btn-outline-primary" onclick="updateChart('last_year')">Tahun
                                Lalu</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                            <canvas id="monthlyLineChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TOP MEMBERS -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow border-0 h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 text-primary">
                            <i class="fas fa-trophy me-2"></i> Top 5 Member
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $topMembers = \App\Models\Member::orderByDesc('poin')->limit(5)->get();
                        @endphp

                        @if ($topMembers->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($topMembers as $index => $member)
                                    <div
                                        class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-3">
                                        <div>
                                            <span class="badge bg-primary me-2">#{{ $index + 1 }}</span>
                                            <span class="fw-medium">{{ $member->nama }}</span>
                                        </div>
                                        <span class="badge bg-success rounded-pill">{{ number_format($member->poin) }}
                                            poin</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center my-4">Belum ada data member</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- Load Chart.js dari CDN -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            // Data dari controller
            const chartData = @json($chartData ?? []);

            // 1. LINE CHART BULANAN
            const monthlyLineCtx = document.getElementById('monthlyLineChart').getContext('2d');
            let monthlyLineChart = new Chart(monthlyLineCtx, {
                type: 'line',
                data: {
                    labels: chartData.monthly?.labels || [],
                    datasets: [{
                        label: 'Poin per Bulan',
                        data: chartData.monthly?.poin || [],
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#4e73df',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    return `Poin: ${context.parsed.y.toLocaleString('id-ID')}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Bulan'
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Total Poin'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });

            // 2. BAR CHART BULANAN
            const monthlyBarCtx = document.getElementById('monthlyBarChart').getContext('2d');
            let monthlyBarChart = new Chart(monthlyBarCtx, {
                type: 'bar',
                data: {
                    labels: chartData.monthly?.labels || [],
                    datasets: [{
                        label: 'Poin per Bulan',
                        data: chartData.monthly?.poin || [],
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });

            // Fungsi update chart dengan AJAX
            function updateChart(filter) {
                // Update button active state
                document.querySelectorAll('.btn-group .btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                event.target.classList.add('active');

                // Fetch data baru berdasarkan filter
                fetch(`{{ route('owner.chart.data') }}?filter=${filter}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update kedua chart
                            monthlyLineChart.data.labels = data.labels;
                            monthlyLineChart.data.datasets[0].data = data.data;
                            monthlyLineChart.update();

                            monthlyBarChart.data.labels = data.labels;
                            monthlyBarChart.data.datasets[0].data = data.data;
                            monthlyBarChart.update();

                            // Update statistik jika ada
                            if (data.stats) {
                                updateStats(data.stats);
                            }
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            // Fungsi update statistik (opsional)
            function updateStats(stats) {
                // Implement jika ingin update statistik via AJAX
                console.log('Stats updated:', stats);
            }
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

            .chart-container {
                position: relative;
            }

            .stat-card {
                transition: all 0.3s;
                height: 100%;
            }

            .stat-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            }
        </style>
    @endsection
