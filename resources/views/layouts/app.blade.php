<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIHC')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f1f8ff;
            font-family: 'Poppins', system-ui, 'Segoe UI', sans-serif;
            margin: 0;
            display: flex;
        }

        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
            padding: 0.75rem 0;
        }

        .pagination-info {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .pagination-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .pagination-buttons .btn {
            padding: 0.35rem 0.7rem;
            font-size: 0.9rem;
            min-width: 38px;
            font-weight: 600;
        }

        .sidebar {
            width: 220px;
            height: 100vh;
            position: fixed;
            color: #fff;
            background: linear-gradient(180deg, #5cbdf7, #84fab0);
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar h3 {
            font-weight: 700;
            text-align: center;
            margin-bottom: 24px;
        }

        .sidebar nav a {
            display: block;
            color: #fff;
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 6px;
            font-weight: 500;
            margin-bottom: 6px;
            transition: all 0.2s;
        }

        .sidebar nav a:hover,
        .sidebar nav a.active {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(4px);
        }

        .logout {
            padding: 12px;
            border-top: 1px solid rgba(255, 255, 255, 0.3);
        }

        .main {
            margin-left: 220px;
            width: calc(100% - 220px);
            padding: 24px;
        }

        .welcome {
            background: #a2d9ff;
            border-radius: 8px;
            padding: 12px 18px;
            margin-bottom: 20px;
        }

        /* Style untuk auto-dismiss alert */
        .alert-auto-dismiss {
            position: relative;
            animation: slideIn 0.3s ease, fadeOut 0.5s ease 4.5s forwards;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                flex-direction: row;
                justify-content: space-around;
            }

            .main {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <div>
            <h3>SIHC</h3>
            <nav>
                @if (Auth::check() && Auth::user()->username === 'owner')
                    <a href="{{ url('/owner/dashboard') }}"
                        class="{{ request()->is('owner/dashboard') ? 'active' : '' }}">Home</a>
                    <a href="{{ route('members.index') }}"
                        class="{{ request()->is('members*') ? 'active' : '' }}">Member</a>
                    <a href="{{ route('poins.index') }}" class="{{ request()->is('poins*') ? 'active' : '' }}">Poin</a>
                    <a href="{{ route('owner.reward.index') }}"
                        class="{{ request()->is('owner/reward*') ? 'active' : '' }}">Reward</a>
                    <a href="{{ route('owner.laporan.index') }}"
                        class="{{ request()->is('owner/laporan*') ? 'active' : '' }}">Laporan</a>
                @elseif(Auth::check() && Auth::user()->username === 'kasir')
                    <a href="{{ url('/kasir/dashboard') }}"
                        class="{{ request()->is('kasir/dashboard') ? 'active' : '' }}">Home</a>
                    <a href="{{ route('members.index') }}"
                        class="{{ request()->is('members*') ? 'active' : '' }}">Member</a>
                    <a href="{{ route('poins.index') }}"
                        class="{{ request()->is('poins*') ? 'active' : '' }}">Poin</a>
                @else
                    <a href="{{ url('/') }}">Home</a>
                    <a href="{{ route('members.index') }}">Member</a>
                @endif
            </nav>
        </div>
        <div class="logout">
            <form action="{{ route('logout') }}" method="POST">@csrf
                <button class="btn btn-outline-light btn-sm w-100">Logout</button>
            </form>
        </div>
    </aside>

    <main class="main">
        @if (Auth::check() && (request()->is('owner/dashboard') || request()->is('kasir/dashboard') || request()->is('/')))
            <div class="welcome">
                <h5 class="m-0">
                    Selamat Datang, {{ Auth::user()->username }}
                    @if (Auth::user()->username === 'owner')
                        👑
                    @elseif(Auth::user()->username === 'kasir')
                        💰
                    @endif
                </h5>
            </div>
        @endif

        {{-- NOTIFICATION SECTION --}}
        @if(session('success'))
            <div class="alert alert-success alert-auto-dismiss alert-dismissible fade show" role="alert">
                ✅ {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-auto-dismiss alert-dismissible fade show" role="alert">
                ❌ {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-auto-dismiss alert-dismissible fade show" role="alert">
                ⚠️ {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-auto-dismiss alert-dismissible fade show" role="alert">
                ℹ️ {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')

        @if (View::hasSection('pagination_info') || View::hasSection('pagination_buttons'))
            <div class="pagination-container">
                <div class="pagination-info">
                    @yield('pagination_info')
                </div>
                <div class="pagination-buttons">
                    @yield('pagination_buttons')
                </div>
            </div>
        @endif
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function confirmDelete(button, message = 'Yakin ingin menghapus data ini?') {
            if (confirm(message)) {
                button.closest('form').submit();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const autoAlerts = document.querySelectorAll('.alert-auto-dismiss');
            
            autoAlerts.forEach(function(alert) {
                // Bootstrap auto dismiss
                const bsAlert = new bootstrap.Alert(alert);
                setTimeout(() => {
                    bsAlert.close();
                }, 3000);
            });
        });
    </script>
</body>

</html>