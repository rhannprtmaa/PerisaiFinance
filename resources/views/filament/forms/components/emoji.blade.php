<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div x-data="{
        open: false,
        state: $wire.entangle('{{ $getStatePath() }}')
    }" class="relative">

        <button
            type="button"
            @click="open = !open"
            class="w-full flex items-center justify-between gap-3 px-3 py-2.5 text-left bg-white border border-gray-300 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
        >
            <div class="flex items-center gap-3">
                <span x-text="state || '😀'" class="text-2xl py-0.5"></span>
                <span x-text="state ? 'Emoticon Terpilih' : 'Klik untuk membuka papan emoji...'" class="text-sm text-gray-500 dark:text-gray-400"></span>
            </div>
            <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
            </svg>
        </button>

        <div
            x-show="open"
            @click.away="open = false"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute left-0 z-50 mt-2 bg-white border border-gray-200 rounded-xl shadow-2xl dark:bg-gray-900 dark:border-gray-800 p-1"
            style="display: none; min-width: 320px;"
        >
            <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@1/index.js"></script>

            <style>
                emoji-picker {
                    --border-color: transparent;
                    --background: transparent;
                    width: 100%;
                    max-width: 340px;
                    height: 320px;
                }
            </style>

            <emoji-picker
                @emoji-click="state = $event.detail.unicode; open = false"
                class="light dark:dark"
            ></emoji-picker>
        </div>
    </div>
</x-dynamic-component>
