<div x-data="{
    pin: ['', '', '', ''],
    updatePin() {
        $wire.set('{{ $getStatePath() }}', this.pin.join(''))
    },
    handleInput(index, event) {
        let val = event.target.value.replace(/\D/g, '')
        this.pin[index] = val.substring(0, 1)
        if (this.pin[index] && index < 3) {
            this.$nextTick(() => this.$refs['input' + (index + 1)].focus())
        }
        this.updatePin()
    },
    handleKeyDown(index, event) {
        if (event.key === 'Backspace' && !this.pin[index] && index > 0) {
            this.$refs['input' + (index - 1)].focus()
        }
    }
}" class="flex justify-center w-full py-8">
    <div class="inline-flex gap-3 px-4 py-2 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-inner">
        <template x-for="(v, index) in 4" :key="index">
            <input
                :x-ref="'input' + index"
                type="text"
                inputmode="numeric"
                maxlength="1"
                autocomplete="off"
                placeholder="·"
                style="width: 56px !important; height: 56px !important; -webkit-text-security: disc;"
                class="!p-0 text-center text-3xl font-bold rounded-xl border-2 border-gray-300 shadow-sm focus:!border-indigo-600 focus:!ring-indigo-600 dark:bg-gray-800 dark:border-gray-700 bg-white text-gray-900 flex-none transition-all duration-200 hover:border-indigo-400 placeholder-gray-300"
                x-model="pin[index]"
                @input="handleInput(index, $event)"
                @keydown="handleKeyDown(index, $event)"
            />
        </template>
    </div>
</div>
