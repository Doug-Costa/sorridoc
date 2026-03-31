<div class="flex items-center justify-between py-4">
    <div class="flex items-center w-full max-w-2xl mx-auto space-x-4">
        <!-- Step 1 -->
        <div class="flex flex-col items-center">
            <div class="flex items-center justify-center w-10 h-10 border-2 {{ $status === 'Pendente' || $status === 'Em Aprovação' || $status === 'Aprovado' ? 'border-primary-600' : 'border-gray-300' }} rounded-full">
                <span class="text-sm {{ $status === 'Pendente' || $status === 'Em Aprovação' || $status === 'Aprovado' ? 'text-primary-600 font-bold' : 'text-gray-400' }}">1</span>
            </div>
            <span class="mt-2 text-xs font-medium text-gray-500">João ✓</span>
        </div>

        <div class="flex-1 h-px bg-gray-300"></div>

        <!-- Step 2 -->
        <div class="flex flex-col items-center">
            <div class="flex items-center justify-center w-10 h-10 border-2 {{ $status === 'Aprovado' ? 'border-primary-600' : 'border-gray-300' }} rounded-full">
                <span class="text-sm {{ $status === 'Aprovado' ? 'text-primary-600 font-bold' : 'text-gray-400' }}">2</span>
            </div>
            <span class="mt-2 text-xs font-medium text-gray-500">Regina...</span>
        </div>

        <div class="flex-1 h-px bg-gray-300"></div>

        <!-- Final -->
        <div class="flex flex-col items-center">
            <div class="flex items-center justify-center w-10 h-10 border-2 {{ $status === 'Aprovado' ? 'border-green-600' : 'border-gray-300' }} rounded-full">
                <svg class="w-6 h-6 {{ $status === 'Aprovado' ? 'text-green-600' : 'text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <span class="mt-2 text-xs font-medium text-gray-500">Publicar</span>
        </div>
    </div>
</div>
