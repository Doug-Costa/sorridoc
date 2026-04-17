@extends('portals.layout')

@section('title', 'Login - Upload de Documentos')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <div class="bg-teal-100 p-4 rounded-full">
                    <svg class="h-12 w-12 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                </div>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Upload de Documentos</h1>
            <p class="text-gray-600 mt-2">{{ $company->corporate_name }}</p>
        </div>

        <form method="POST" action="{{ route('upload.auth', $token) }}">
            @csrf
            
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
            <p>Acesso restrito a usuários autorizados.</p>
            <p class="mt-2">Solicite credenciais à SorriDoc se necessário.</p>
        </div>
    </div>
</div>
@endsection
