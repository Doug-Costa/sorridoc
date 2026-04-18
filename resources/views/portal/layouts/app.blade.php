<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal SorriMed - @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    @auth
    <nav class="navbar">
        <div class="brand">SorriDoc <span style="font-weight: 300;">Portal</span></div>
        <div style="display: flex; align-items: center; gap: 20px;">
            <span style="font-size: 14px; color: var(--text-muted);">
                {{ Auth::user()->name }} 
                <span class="badge" style="background: #eef2ff; color: #4f46e5; margin-left:8px;">{{ Auth::user()->role }}</span>
            </span>
            <form action="{{ route('portal.logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-link" style="border: none; background: none; cursor: pointer;">Sair</button>
            </form>
        </div>
    </nav>
    @endauth

    <div class="@auth main-content @else auth-container @endauth">
        @yield('content')
    </div>
</body>
</html>
