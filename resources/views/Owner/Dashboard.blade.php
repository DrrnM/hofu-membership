<!-- <!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Owner - SIHC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f8ff;
            font-family: "Poppins", sans-serif;
            margin: 0;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 220px;
            background: linear-gradient(180deg, #5cbdf7, #84fab0);
            color: white;
            height: 100vh;
            padding-top: 20px;
            position: fixed;
        }

        .sidebar h3 {
            text-align: center;
            font-weight: 700;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            font-weight: 500;
        }

        .sidebar a:hover {
            background-color: rgba(255,255,255,0.2);
            border-radius: 5px;
        }

        /* Main content */
        .main-content {
            margin-left: 220px;
            padding: 20px;
            width: calc(100% - 220px);
        }

        .header {
            background-color: #a2d9ff;
            padding: 10px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dashboard-cards {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .card-info {
            flex: 1;
            min-width: 220px;
            background-color: #d4ecff;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            font-weight: 600;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .card-info h4 {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .card-info span {
            font-size: 24px;
            font-weight: 700;
        }

        .image-banner img {
            width: 100%;
            border-radius: 12px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    Sidebar -->
    <!-- <div class="sidebar">
        <h3>SIHC</h3>
        <a href="#">Home</a>
        <a href="{{ route('members.index') }}">Member</a>
        <a href="#">Poin</a>
        <a href="#">Reward</a>
        <a href="#">Laporan</a>
    </div> -->

    <!-- Main Content -->
    <!-- <div class="main-content">
        <div class="header">
            <h4>Selamat Datang, Owner ☕</h4>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-sm btn-outline-danger">Logout</button>
            </form>
        </div>

        <div class="image-banner">
            <img src="{{ asset('images/hofu.jpg') }}" alt="Hofu Coffee">
        </div>

        <div class="dashboard-cards">
            <div class="card-info">
                <h4>Total Member</h4>
                <span>{{ $totalMember ?? 0 }}</span>
            </div>
            <div class="card-info">
                <h4>Total Poin</h4>
                <span>{{ $totalPoin ?? 0 }}</span>
            </div>
            <div class="card-info">
                <h4>Transaksi Hari Ini</h4>
                <span>{{ $totalTransaksi ?? 0 }}</span>
            </div>
        </div>
    </div>

</body>
</html> -->

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Owner - SIHC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f8ff;
            font-family: "Poppins", sans-serif;
            margin: 0;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 220px;
            background: linear-gradient(180deg, #5cbdf7, #84fab0);
            color: white;
            height: 100vh;
            padding-top: 20px;
            position: fixed;
        }

        .sidebar h3 {
            text-align: center;
            font-weight: 700;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            font-weight: 500;
        }

        .sidebar a:hover {
            background-color: rgba(255,255,255,0.2);
            border-radius: 5px;
        }

        /* Main content */
        .main-content {
            margin-left: 220px;
            padding: 20px;
            width: calc(100% - 220px);
        }

        .header {
            background-color: #a2d9ff;
            padding: 10px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dashboard-cards {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .card-info {
            flex: 1;
            min-width: 220px;
            background-color: #d4ecff;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            font-weight: 600;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .card-info h4 {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .card-info span {
            font-size: 24px;
            font-weight: 700;
        }

        .image-banner img {
            width: 100%;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        table {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
        }

        th {
            background-color: #a2d9ff;
            color: #333;
            text-align: center;
        }

        td {
            vertical-align: middle !important;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h3>SIHC</h3>
        <a href="{{ route('owner.dashboard') }}">Home</a>
        <a href="{{ route('members.index') }}">Member</a>
        <a href="#">Poin</a>
        <a href="{{ route('owner.reward.index') }}">Reward</a>
        <a href="{{ route('owner.laporan.index') }}">Laporan</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h4>Selamat Datang, Owner ☕</h4>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-sm btn-outline-danger">Logout</button>
            </form>
        </div>

        <!-- Banner -->
        <div class="image-banner">
            <img src="{{ asset('images/hofu.jpg') }}" alt="Hofu Coffee">
        </div>

        <!-- Dashboard Summary -->
        <div class="dashboard-cards">
            <div class="card-info">
                <h4>Total Member</h4>
                <span>{{ $totalMember ?? 0 }}</span>
            </div>
            <div class="card-info">
                <h4>Total Poin</h4>
                <span>{{ $totalPoin ?? 0 }}</span>
            </div>
            <div class="card-info">
                <h4>Transaksi Hari Ini</h4>
                <span>{{ $totalTransaksi ?? 0 }}</span>
            </div>
        </div>

        <!-- Aktivitas Terbaru -->
        <div class="mt-5">
            <h5 class="mb-3 fw-bold">📊 Aktivitas Terbaru</h5>
            <table class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Member</th>
                        <th>Total Transaksi</th>
                        <th>Poin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($laporanTerbaru as $lap)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ $lap->tanggal }}</td>
                            <td>{{ $lap->member->nama ?? '-' }}</td>
                            <td class="text-end">Rp {{ number_format($lap->total_transaksi, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $lap->poin_diperoleh }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Belum ada transaksi terbaru</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
