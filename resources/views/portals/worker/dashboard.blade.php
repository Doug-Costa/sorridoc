@extends('portals.worker.layout')

@section('title', 'Meus Documentos')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h1 class="text-2xl font-bold text-gray-900">Meus Documentos</h1>
            <p class="text-gray-600">{{ $worker->company->corporate_name ?? 'Empresa' }}</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Nome</p>
                    <p class="font-medium text-gray-900">{{ $worker->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">CPF</p>
                    <p class="font-medium text-gray-900">{{ $worker->cpf }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Cargo</p>
                    <p class="font-medium text-gray-900">{{ $worker->role ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Último Acesso</p>
                    <p class="font-medium text-gray-900">{{ $worker->last_access_at ? $worker->last_access_at->format('d/m/Y H:i') : 'Primeiro acesso' }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($documents->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum documento encontrado</h3>
            <p class="mt-1 text-sm text-gray-500">Seus documentos de saúde ocupacional aparecerão aqui quando forem disponibilizados.</p>
        </div>
    @else
        @foreach($documents as $type => $docs)
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">
                    {{ App\Models\WorkerDocument::TYPES[$type] ?? $type }}
                    <span class="text-gray-400 font-normal">({{ $docs->count() }})</span>
                </h2>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($docs as $doc)
                <div class="p-4 flex items-center justify-between hover:bg-gray-50">
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">{{ $doc->title }}</p>
                        @if($doc->description)
                            <p class="text-sm text-gray-500">{{ Str::limit($doc->description, 60) }}</p>
                        @endif
                        <div class="mt-1 flex items-center text-xs text-gray-400 space-x-4">
                            @if($doc->issued_at)
                                <span>Emitido: {{ $doc->issued_at->format('d/m/Y') }}</span>
                            @endif
                            @if($doc->expires_at)
                                <span class="{{ $doc->is_expired ? 'text-red-600' : ($doc->expires_at->isBefore(now()->addDays(30)) ? 'text-amber-600' : '') }}">
                                    Vence: {{ $doc->expires_at->format('d/m/Y') }}
                                </span>
                            @endif
                            @if($doc->is_expired)
                                <span class="px-2 py-0.5 bg-red-100 text-red-800 rounded">Vencido</span>
                            @endif
                        </div>
                    </div>
                    <div class="ml-4">
                        <a href="{{ route('worker.document.download', $doc) }}" 
                           class="inline-flex items-center px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-md hover:bg-teal-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Baixar
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    @endif

    <div class="mt-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Proteção de Dados</h3>
                <p class="mt-1 text-sm text-blue-600">
                    Seus documentos estão protegidos conforme a Lei Geral de Proteção de Dados (LGPD). 
                    O download é pessoal e intransferível. Todo acesso é registrado em log de auditoria.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
