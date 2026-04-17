@extends('portals.rh.layout')

@section('title', $worker->name . ' - Portal RH')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('rh.workers', $token) }}" class="text-teal-600 hover:text-teal-800 text-sm">
            ← Voltar para lista
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h1 class="text-2xl font-bold text-gray-900">{{ $worker->name }}</h1>
            <p class="text-gray-600">{{ $company->corporate_name }}</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div>
                    <p class="text-sm text-gray-500">CPF</p>
                    <p class="font-medium text-gray-900">{{ $worker->cpf }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Cargo</p>
                    <p class="font-medium text-gray-900">{{ $worker->role ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Departamento</p>
                    <p class="font-medium text-gray-900">{{ $worker->department ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $worker->status === 'Ativo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $worker->status }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">E-mail</p>
                    <p class="font-medium text-gray-900">{{ $worker->email ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Telefone</p>
                    <p class="font-medium text-gray-900">{{ $worker->phone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Nascimento</p>
                    <p class="font-medium text-gray-900">{{ $worker->birth_date ? $worker->birth_date->format('d/m/Y') : '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Último Acesso</p>
                    <p class="font-medium text-gray-900">{{ $worker->last_access_at ? $worker->last_access_at->format('d/m/Y H:i') : 'Nunca' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Documentos ({{ $documents->flatten()->count() }})</h2>
        </div>
        
        @forelse($documents as $type => $docs)
        <div class="border-b border-gray-200 last:border-b-0">
            <div class="px-6 py-3 bg-gray-50">
                <h3 class="font-semibold text-gray-700">
                    {{ App\Models\WorkerDocument::TYPES[$type] ?? $type }}
                    <span class="text-gray-400 font-normal">({{ $docs->count() }})</span>
                </h3>
            </div>
            <table class="min-w-full">
                <tbody class="divide-y divide-gray-100">
                    @foreach($docs as $doc)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $doc->title }}</div>
                            @if($doc->description)
                                <div class="text-sm text-gray-500">{{ Str::limit($doc->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Emissão: {{ $doc->issued_at ? $doc->issued_at->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($doc->expires_at)
                                <span class="{{ $doc->is_expired ? 'text-red-600' : ($doc->expires_at->isBefore(now()->addDays(30)) ? 'text-amber-600' : 'text-green-600') }}">
                                    Vencimento: {{ $doc->expires_at->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="text-gray-400">Sem vencimento</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('rh.document.download', [$token, $doc]) }}" 
                               class="text-teal-600 hover:text-teal-900 text-sm font-medium">
                                Baixar PDF
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @empty
        <div class="p-6 text-center text-gray-500">
            Nenhum documento encontrado para este trabajador.
        </div>
        @endforelse
    </div>
</div>
@endsection
