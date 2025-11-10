<!DOCTYPE html>
<html lang="id">
<head>
<<<<<<< HEAD
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title','SIHC')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{background:#f1f8ff;font-family:Poppins,system-ui,Segoe UI,sans-serif;margin:0;display:flex}
    .sidebar{
      width:220px;height:100vh;position:fixed;color:#fff;
      background:linear-gradient(180deg,#5cbdf7,#84fab0);padding:20px 16px;
      display:flex;flex-direction:column;justify-content:space-between
    }
    .sidebar h3{font-weight:700;text-align:center;margin-bottom:24px}
    .sidebar nav a{display:block;color:#fff;text-decoration:none;padding:12px 16px;border-radius:6px}
    .sidebar nav a:hover,.sidebar nav a.active{background:rgba(255,255,255,.2)}
    .sidebar .logout{padding:16px;border-top:1px solid rgba(255,255,255,.3)}
    .main{margin-left:220px;width:calc(100% - 220px);padding:20px}
    .welcome{background:#a2d9ff;border-radius:8px;padding:10px 16px;margin-bottom:20px}
    .card-info{background:#d4ecff;border-radius:12px;padding:20px;box-shadow:0 3px 10px rgba(0,0,0,.1)}
    .dashboard-cards{display:flex;gap:20px;flex-wrap:wrap}
  </style>
=======
    <meta charset="UTF-8">
    <title>SIHOCO Owner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
>>>>>>> b560b58e9f7f9befe66325960fcc942b88e970a4
</head>
<body style="background-color:#f6f8fa;">

<<<<<<< HEAD
  <aside class="sidebar">
    <div>
      <h3>SIHC</h3>
      <nav>
        <a href="/owner/dashboard" class="{{ request()->is('owner/dashboard')?'active':'' }}">Home</a>
        <a href="{{ route('members.index') }}" class="{{ request()->is('members*')?'active':'' }}">Member</a>
        <a href="#">Poin</a>
        <a  href="{{ route('owner.reward.index') }}" class="{{ request()->is('owner/reward*') ? 'active' : '' }}">Reward</a>
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
        <h5 class="m-0">Selamat Datang, {{ Auth::user()->username ?? 'User' }} ☕</h5>
      </div>
    @endif

    @yield('content')
  </main>
=======
<div class="d-flex">
    <!-- Sidebar -->
    <div class="bg-primary text-white p-3" style="width:220px; min-height:100vh;">
        <h4 class="text-center fw-bold mb-4">SIHOCO</h4>
        <a href="/owner/dashboard" class="d-block text-white mb-2">🏠 Home</a>
        <a href="/owner/reward" class="d-block text-white mb-2">🎁 Reward</a>
        <a href="/owner/laporan" class="d-block text-white mb-2">📊 Laporan</a>
        <a href="/logout" class="d-block text-white mt-4">🚪 Exit</a>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4">
        @yield('content')
    </div>
</div>
>>>>>>> b560b58e9f7f9befe66325960fcc942b88e970a4

</body>
</html>
