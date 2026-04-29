@php
    $isMultipla = $record->flow_type === 'Múltipla';
    $flows = $record->approvalFlows()->where('status', 'Aprovado')->get();
    $isDupla = $record->flow_type === 'Dupla';
    $step1 = $flows->first();
    $step2 = $isDupla ? $flows->skip(1)->first() : null;
    $isRejeitado = $record->status === 'Rejeitado';
@endphp

@if(!$isMultipla)
<div class="flex items-center justify-between py-4">
    <div class="flex items-center w-full max-w-2xl mx-auto space-x-4">
        <!-- Step 1 -->
        <div class="flex flex-col items-center">
            <div class="flex items-center justify-center w-10 h-10 border-2 {{ $step1 ? 'border-primary-600 bg-primary-50' : ($isRejeitado ? 'border-danger-600 bg-danger-50' : 'border-gray-300') }} rounded-full transition-all duration-300">
                @if($step1)
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                @elseif($isRejeitado && $record->approvalFlows()->where('status', 'Rejeitado')->exists())
                     <svg class="w-6 h-6 text-danger-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                @else
                    <span class="text-sm text-gray-400 font-bold">1</span>
                @endif
            </div>
            <span class="mt-2 text-xs font-semibold {{ $step1 ? 'text-primary-700' : 'text-gray-500' }}">
                {{ $step1 ? $step1->assignedUser->name : ($isDupla ? 'Diretor' : 'Assinatura') }}
            </span>
            @if($step1)
                <span class="text-[10px] text-gray-400 font-medium">{{ $step1->approved_at?->format('d/m H:i') }}</span>
            @endif
        </div>

        <div class="flex-1 h-1 self-center mt-[-20px] {{ $step1 ? 'bg-primary-500' : 'bg-gray-200' }} rounded-full"></div>

        @if($isDupla)
            <!-- Step 2 -->
            <div class="flex flex-col items-center">
                <div class="flex items-center justify-center w-10 h-10 border-2 {{ $step2 ? 'border-primary-600 bg-primary-50' : 'border-gray-300' }} rounded-full transition-all duration-300">
                    @if($step2)
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    @else
                        <span class="text-sm text-gray-400 font-bold">2</span>
                    @endif
                </div>
                <span class="mt-2 text-xs font-semibold {{ $step2 ? 'text-primary-700' : 'text-gray-500' }}">
                    {{ $step2 ? $step2->assignedUser->name : 'Advogado(a)' }}
                </span>
                @if($step2)
                    <span class="text-[10px] text-gray-400 font-medium">{{ $step2->approved_at?->format('d/m H:i') }}</span>
                @endif
            </div>

            <div class="flex-1 h-1 self-center mt-[-20px] {{ $step2 ? 'bg-primary-500' : 'bg-gray-200' }} rounded-full"></div>
        @endif

        <!-- Final -->
        <div class="flex flex-col items-center">
            <div class="flex items-center justify-center w-10 h-10 border-2 {{ $record->status === 'Aprovado' ? 'border-green-600 bg-green-50' : 'border-gray-300' }} rounded-full transition-all duration-300">
                <svg class="w-6 h-6 {{ $record->status === 'Aprovado' ? 'text-green-600' : 'text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <span class="mt-2 text-xs font-bold {{ $record->status === 'Aprovado' ? 'text-green-700' : 'text-gray-500' }}">
                {{ $record->status === 'Aprovado' ? 'Certificado' : ($isRejeitado ? 'Bloqueado' : 'Finalizar') }}
            </span>
        </div>
    </div>
</div>
@endif

