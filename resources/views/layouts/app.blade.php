<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SIHOCO Owner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color:#f6f8fa;">

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

</body>
</html>
