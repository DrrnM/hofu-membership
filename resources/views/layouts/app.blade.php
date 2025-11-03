<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hofu Coffee System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">☕ Hofu Coffee</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a href="/members" class="nav-link">Member</a></li>
        <li class="nav-item"><a href="/logout" class="nav-link"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>

<div class="container mt-4">
    @yield('content')
</div>

<footer class="text-center text-muted mt-5 mb-3">
    <small>© {{ date('Y') }} Hofu Coffee - Sistem Membership</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
