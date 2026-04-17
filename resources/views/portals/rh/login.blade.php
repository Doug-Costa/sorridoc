@extends('portals.layout')

@section('title', 'Login - Portal RH')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <div class="bg-teal-100 p-4 rounded-full">
                    <svg class="h-12 w-12 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Portal RH</h1>
            <p class="text-gray-600 mt-2">{{ $company->corporate_name }}</p>
        </div>

        <form method="POST" action="{{ route('rh.auth', $token) }}">
            @csrf
            
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">E-mail</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500"
                    required autofocus>
            </div>

            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Senha</label>
                <input type="password" name="password" id="password"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500"
                    required>
            </div>

            <button type="submit"
                class="w-full bg-teal-600 text-white font-bold py-2 px-4 rounded hover:bg-teal-700 transition">
                Entrar
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-500">
            <p>Acesso restrito a gestores de RH autorizados.</p>
            <p class="mt-2">Problemas de acesso? Solicite ajuda à SorriDoc.</p>
        </div>
    </div>
</div>
@endsection
