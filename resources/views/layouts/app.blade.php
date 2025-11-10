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

    /* Sidebar */
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

    /* Main Content */
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
        <a href="{{ url('/owner/dashboard') }}" class="{{ request()->is('owner/dashboard') ? 'active' : '' }}">Home</a>
        <a href="{{ route('members.index') }}" class="{{ request()->is('members*') ? 'active' : '' }}">Member</a>
        <a href="{{ route('poins.index')}}">Poin</a>
        <a href="{{ route('owner.reward.index') }}" class="{{ request()->is('owner/reward*') ? 'active' : '' }}">Reward</a>
        <a href="{{ route('owner.laporan.index') }}" class="{{ request()->is('owner/laporan*') ? 'active' : '' }}">Laporan</a>
      </nav>
    </div>
    <div class="logout">
      <form action="{{ route('logout') }}" method="POST">@csrf
        <button class="btn btn-outline-light btn-sm w-100">Logout</button>
      </form>
    </div>
  </aside>

  <main class="main">
    @if(request()->is('owner/dashboard'))
      <div class="welcome">
        <h5 class="m-0">Selamat Datang, {{ Auth::user()->username ?? 'Owner' }} ☕</h5>
      </div>
    @endif

    @yield('content')
  </main>
</body>
</html>
