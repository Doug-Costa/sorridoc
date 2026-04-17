@extends('portals.worker.layout')

@section('title', 'Verificar Token - Portal do Trabalhador')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <div class="bg-teal-100 p-4 rounded-full">
                    <svg class="h-12 w-12 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Verificação de Acesso</h1>
            <p class="text-gray-600 mt-2">CPF: {{ session('worker_cpf') }}</p>
        </div>

        <form method="POST" action="{{ route('worker.verify-token') }}">
            @csrf
            
            <div class="mb-6">
                <label for="access_token" class="block text-gray-700 text-sm font-bold mb-2">Token de Acesso</label>
                <input type="text" name="access_token" id="access_token"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500"
                    placeholder="Cole aqui o token enviado pela SorriDoc" required autofocus>
                <p class="text-xs text-gray-500 mt-1">O token foi enviado para o e-mail cadastrado pela sua empresa.</p>
            </div>

            <button type="submit"
                class="w-full bg-teal-600 text-white font-bold py-2 px-4 rounded hover:bg-teal-700 transition">
                Verificar e Acessar
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('worker.login') }}" class="text-sm text-teal-600 hover:text-teal-800">
                ← Voltar e usar outro CPF
            </a>
        </div>
    </div>
</div>
@endsection
