<!DOCTYPE html>
<html lang="pt_BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificação de Documento | SorriDoc</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-2xl w-full bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
        <div class="bg-indigo-600 p-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Documento Autêntico</h1>
            <p class="text-indigo-100 mt-1">Este processo de aprovação foi verificado com sucesso.</p>
        </div>

        <div class="p-8 space-y-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500 uppercase tracking-wider font-semibold">Título</p>
                    <p class="text-lg font-bold text-gray-900">{{ $approval->title }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 uppercase tracking-wider font-semibold">Status</p>
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-bold">{{ $approval->status }}</span>
                </div>
                <div>
                    <p class="text-sm text-gray-500 uppercase tracking-wider font-semibold">Solicitante</p>
                    <p class="text-gray-900 font-medium">{{ $approval->owner->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 uppercase tracking-wider font-semibold">Data</p>
                    <p class="text-gray-900 font-medium">{{ $approval->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                <p class="text-sm text-gray-500 uppercase tracking-wider font-semibold mb-2">Hash de Integridade</p>
                <p class="text-xs font-mono text-gray-600 break-all">{{ $approval->hash_sha256 }}</p>
            </div>

            <div class="border-t border-gray-100 pt-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Histórico de Assinaturas</h3>
                @foreach($approval->approvalFlows as $flow)
                    <div class="flex items-start space-x-4 mb-4">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs ring-4 ring-white">
                            {{ $loop->iteration }}
                        </div>
                        <div class="flex-grow">
                            <p class="text-sm font-bold text-gray-900">{{ $flow->step_name }}</p>
                            <p class="text-xs text-gray-500">Por: {{ $flow->assignedUser->name ?? 'Sistema' }} em {{ $flow->approved_at?->format('d/m/Y H:i') }}</p>
                            @if($flow->comment)
                                <p class="mt-1 text-sm text-gray-600 italic">"{{ $flow->comment }}"</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-gray-100 p-6 text-center text-gray-500 text-xs italic">
            Esta verificação foi gerada em {{ now()->format('d/m/Y H:i:s') }}. SorriDoc Platform.
        </div>
    </div>
</body>
</html>
