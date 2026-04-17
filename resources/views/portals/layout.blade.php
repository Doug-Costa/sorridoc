<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SorriDoc')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-sorridoc-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span class="ml-2 font-bold text-xl">SorriDoc</span>
                    </div>
                </div>
                <div class="flex items-center">
                    @auth
                        <span class="mr-4 text-sm">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm hover:text-gray-200">Sair</button>
                        </form>
                    @endauth
                    @guest
                        <a href="{{ route('login') }}" class="text-sm hover:text-gray-200">Entrar</a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <main class="py-10">
        @yield('content')
    </main>

    <footer class="bg-gray-800 text-white py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center text-sm">
            <p>&copy; {{ date('Y') }} SorriDoc - DentalPress. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>
