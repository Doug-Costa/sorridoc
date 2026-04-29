@php
    $isMultipla = $record->flow_type === 'Múltipla';
    $assignees = $isMultipla ? $record->assignees : collect();
    $flows = $record->approvalFlows()->get();
    $isRejeitado = $record->status === 'Rejeitado';
@endphp

@if($isMultipla)
<div class="flex items-center justify-between py-4">
    <div class="flex flex-wrap items-center w-full max-w-4xl mx-auto space-x-4">
        @foreach($assignees as $index => $assignee)
            @php
                $userFlow = $flows->where('user_id', $assignee->user_id)->first();
                $isAprovado = $userFlow && $userFlow->status === 'Aprovado';
                $isUserRejeitado = $userFlow && $userFlow->status === 'Rejeitado';
            @endphp
            
            <div class="flex flex-col items-center mb-4">
                <div class="flex items-center justify-center w-10 h-10 border-2 {{ $isAprovado ? 'border-primary-600 bg-primary-50' : ($isUserRejeitado ? 'border-danger-600 bg-danger-50' : 'border-gray-300') }} rounded-full transition-all duration-300">
                    @if($isAprovado)
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    @elseif($isUserRejeitado)
                         <svg class="w-6 h-6 text-danger-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    @else
                        <span class="text-sm text-gray-400 font-bold">{{ $index + 1 }}</span>
                    @endif
                </div>
                <span class="mt-2 text-xs font-semibold {{ $isAprovado ? 'text-primary-700' : 'text-gray-500' }}">
                    {{ $assignee->user->name ?? 'Aprovador' }}
                </span>
                @if($isAprovado)
                    <span class="text-[10px] text-gray-400 font-medium">{{ $userFlow->approved_at?->format('d/m H:i') }}</span>
                @endif
            </div>

            @if(!$loop->last)
                <div class="flex-1 h-1 self-center mt-[-35px] {{ $isAprovado ? 'bg-primary-500' : 'bg-gray-200' }} rounded-full min-w-[30px]"></div>
            @endif
        @endforeach

        @if($assignees->count() > 0)
        <!-- Final -->
        <div class="flex-1 h-1 self-center mt-[-35px] {{ $record->status === 'Aprovado' ? 'bg-green-500' : 'bg-gray-200' }} rounded-full min-w-[30px]"></div>
        
        <div class="flex flex-col items-center mb-4">
            <div class="flex items-center justify-center w-10 h-10 border-2 {{ $record->status === 'Aprovado' ? 'border-green-600 bg-green-50' : 'border-gray-300' }} rounded-full transition-all duration-300">
                <svg class="w-6 h-6 {{ $record->status === 'Aprovado' ? 'text-green-600' : 'text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <span class="mt-2 text-xs font-bold {{ $record->status === 'Aprovado' ? 'text-green-700' : 'text-gray-500' }}">
                {{ $record->status === 'Aprovado' ? 'Certificado' : ($isRejeitado ? 'Bloqueado' : 'Finalizar') }}
            </span>
        </div>
        @endif
    </div>
</div>
@endif
