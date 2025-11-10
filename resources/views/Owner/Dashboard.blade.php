@extends('layouts.app')

@section('title', 'Dashboard Owner')

<<<<<<< HEAD
@section('content')
<div class="main-content" style="margin-left: 220px; padding: 20px; background-color: #f1f8ff;">
    
    {{-- 🟦 Header - hanya di Home --}}
    <div class="header bg-info bg-opacity-25 p-3 rounded mb-4 shadow-sm d-flex justify-content-between align-items-center">
        <h4 class="fw-semibold text-primary mb-0">Selamat Datang, Owner ☕</h4>
    </div>

    {{-- 🧾 Dashboard Cards --}}
    <div class="dashboard-cards d-flex flex-wrap gap-4">
        <div class="card-info flex-fill text-center p-4 rounded shadow-sm" style="background-color: #d4ecff;">
            <h5 class="fw-bold text-secondary mb-2">Total Member</h5>
            <span class="fs-3 fw-bold text-primary">{{ $totalMember ?? 0 }}</span>
=======
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
            background-color: rgba(255, 255, 255, 0.2);
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
    <!-- Sidebar -->
    <div class="sidebar">
        <h3>SIHC</h3>
        <a href="#">Home</a>
        <a href="{{ route('members.index') }}">Member</a>
        <a href="#">Poin</a>
        <a href="#">Reward</a>
        <a href="#">Laporan</a>
    </div> -->

 <div class="main-content">
        <div class="header">
            <h4>Selamat Datang, Owner ☕</h4>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-sm btn-outline-danger">Logout</button>
            </form>
>>>>>>> b560b58e9f7f9befe66325960fcc942b88e970a4
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
<<<<<<< HEAD
</div>
@endsection
=======
</body>
</html>
>>>>>>> b560b58e9f7f9befe66325960fcc942b88e970a4
