@php
    // Konfigurasi tipe toast dan warna
    $toastTypes = [
        'success' => [
            'bg' => 'bg-emerald-50',
            'border' => 'border-emerald-200',
            'text' => 'text-emerald-800',
            'icon' => 'text-emerald-500',
            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />'
        ],
        'error' => [
            'bg' => 'bg-red-50',
            'border' => 'border-red-200',
            'text' => 'text-red-800',
            'icon' => 'text-red-500',
            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />'
        ],
        'info' => [
            'bg' => 'bg-blue-50',
            'border' => 'border-blue-200',
            'text' => 'text-blue-800',
            'icon' => 'text-blue-500',
            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 20c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8z" />'
        ],
    ];
@endphp

@foreach (['success', 'error', 'info'] as $type)
    @if (session($type))
        <div 
            x-data="{ show: true }" 
            x-show="show" 
            x-init="setTimeout(() => show = false, 3000)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed top-5 right-5 z-50 max-w-xs w-full {{ $toastTypes[$type]['bg'] }} {{ $toastTypes[$type]['border'] }} {{ $toastTypes[$type]['text'] }} text-sm rounded-xl shadow-lg px-5 py-4 flex items-start space-x-3 mb-2"
            role="alert"
        >
            <svg class="w-5 h-5 {{ $toastTypes[$type]['icon'] }} mt-0.5" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                {!! $toastTypes[$type]['svg'] !!}
            </svg>
            <span class="flex-1">{{ session($type) }}</span>
            <button @click="show = false" class="{{ $toastTypes[$type]['text'] }} hover:opacity-80 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif
@endforeach
