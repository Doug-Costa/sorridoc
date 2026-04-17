@extends('portals.layout')

@section('title', 'Login - Portal do Trabalhador')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <div class="bg-teal-100 p-4 rounded-full">
                    <svg class="h-12 w-12 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Portal do Trabalhador</h1>
            <p class="text-gray-600 mt-2">Acesse seus documentos de saúde ocupacional</p>
        </div>

        <form method="POST" action="{{ route('worker.auth') }}">
            @csrf
            
            <div class="mb-4">
                <label for="cpf" class="block text-gray-700 text-sm font-bold mb-2">CPF</label>
                <input type="text" name="cpf" id="cpf" value="{{ old('cpf') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500"
                    placeholder="000.000.000-00" required autofocus>
                <p class="text-xs text-gray-500 mt-1">Digite seu CPF para buscar seu acesso.</p>
            </div>

            <div class="mb-6">
                <label for="access_token" class="block text-gray-700 text-sm font-bold mb-2">Token de Acesso</label>
                <input type="text" name="access_token" id="access_token"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500"
                    placeholder="Cole aqui o token enviado pela sua empresa">
                <p class="text-xs text-gray-500 mt-1">O token é fornecido pela SorriDoc através de sua empresa.</p>
            </div>

            <button type="submit"
                class="w-full bg-teal-600 text-white font-bold py-2 px-4 rounded hover:bg-teal-700 transition">
                Acessar Meus Documentos
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-500">
            <p>Problemas de acesso? Entre em contato com o RH de sua empresa.</p>
            <p class="mt-2">Seus dados estão protegidos conforme a LGPD.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.8/dist/inputmask.min.js"></script>
<script>
    Inputmask("999.999.999-99").mask(document.getElementById("cpf"));
</script>
@endsection
