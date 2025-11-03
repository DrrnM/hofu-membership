<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Poin - SIHC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f8ff;
            font-family: "Poppins", sans-serif;
            margin: 0;
            display: flex;
        }
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
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h3>SIHC</h3>
        <a href="{{ url('/owner/dashboard') }}">Home</a>
        <a href="{{ route('members.index') }}">Member</a>
        <a href="{{ route('poins.index') }}" class="fw-bold">Poin</a>
        <a href="{{ route('rewards.index') }}">Reward</a>
        <a href="{{ route('laporans.index') }}">Laporan</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h4>Ubah Poin Member ☕</h4>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-sm btn-outline-danger">Logout</button>
            </form>
        </div>

        <div class="card p-4 shadow-sm">
            <form action="{{ route('poins.update', $poin->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Nama Member</label>
                    <input type="text" class="form-control" value="{{ $poin->member->nama ?? '-' }}" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jumlah Poin</label>
                    <input type="number" name="jumlah_poin" class="form-control" value="{{ $poin->jumlah_poin }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <input type="text" name="keterangan" class="form-control" value="{{ $poin->keterangan }}">
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('poins.index') }}" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
