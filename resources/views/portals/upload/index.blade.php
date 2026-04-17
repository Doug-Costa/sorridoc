@extends('portals.upload.layout')

@section('title', 'Upload de Documentos')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Upload de Documentos</h1>
        <p class="text-gray-600">Envie documentos de saúde ocupacional para os trabalhadores</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Novo Documento</h2>
        
        <form method="POST" action="{{ route('upload.store', $token) }}" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="worker_id" class="block text-gray-700 text-sm font-bold mb-2">Trabalhador *</label>
                    <select name="worker_id" id="worker_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <option value="">Selecione um trabalhador</option>
                        @foreach($workers as $id => $name)
                            <option value="{{ $id }}" {{ old('worker_id') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="type" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Documento *</label>
                    <select name="type" id="type" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <option value="">Selecione o tipo</option>
                        @foreach(App\Models\WorkerDocument::TYPES as $key => $label)
                            <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Título *</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500"
                        placeholder="Ex: ASO - Admissional 2026">
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Descrição</label>
                    <textarea name="description" id="description" rows="2"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500"
                        placeholder="Observações sobre o documento (opcional)">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label for="file" class="block text-gray-700 text-sm font-bold mb-2">Arquivo PDF *</label>
                    <input type="file" name="file" id="file" accept=".pdf" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <p class="text-xs text-gray-500 mt-1">Apenas arquivos PDF. Máximo: 100MB</p>
                </div>

                <div>
                    <label for="issued_at" class="block text-gray-700 text-sm font-bold mb-2">Data de Emissão</label>
                    <input type="date" name="issued_at" id="issued_at" value="{{ old('issued_at') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>

                <div>
                    <label for="expires_at" class="block text-gray-700 text-sm font-bold mb-2">Data de Vencimento</label>
                    <input type="date" name="expires_at" id="expires_at" value="{{ old('expires_at') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <p class="text-xs text-gray-500 mt-1">Deixe em branco se não expirar</p>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit"
                    class="bg-teal-600 text-white font-bold py-2 px-6 rounded hover:bg-teal-700 transition">
                    Enviar Documento
                </button>
            </div>
        </form>
    </div>

    <div class="bg-blue-50 rounded-lg border border-blue-200 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Tipos de Documentos</h3>
                <div class="mt-1 text-sm text-blue-600">
                    <ul class="list-disc list-inside">
                        <li><strong>ASO</strong> - Atestado de Saúde Ocupacional</li>
                        <li><strong>PCMSO</strong> - Programa de PCMSO</li>
                        <li><strong>PPP</strong> - Perfil Profissiográfico</li>
                        <li><strong>CAT</strong> - Comunicação de Acidente de Trabalho</li>
                        <li><strong>LTCAT</strong> - Laudo Técnico</li>
                        <li><strong>Exames/Laudos/Receitas/Atestados</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
