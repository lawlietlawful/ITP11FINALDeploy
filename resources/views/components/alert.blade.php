@props([
    'type' => 'info',
    'title' => null,
    'dismissible' => true,
    'autohide' => null,
])

@php
    $styles = match ($type) {
        'success' => [
            'wrapper' => 'border-emerald-200/80 bg-gradient-to-r from-emerald-50/95 to-white text-emerald-900 shadow-emerald-100/70',
            'iconWrap' => 'bg-emerald-100 text-emerald-600 ring-1 ring-emerald-200/80',
            'title' => 'text-emerald-900',
            'body' => 'text-emerald-800/90',
            'close' => 'text-emerald-400 hover:bg-emerald-100 hover:text-emerald-600',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0"></path>',
        ],
        'warning' => [
            'wrapper' => 'border-amber-200/80 bg-gradient-to-r from-amber-50/95 to-white text-amber-900 shadow-amber-100/70',
            'iconWrap' => 'bg-amber-100 text-amber-600 ring-1 ring-amber-200/80',
            'title' => 'text-amber-900',
            'body' => 'text-amber-800/90',
            'close' => 'text-amber-400 hover:bg-amber-100 hover:text-amber-600',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-7.938 4h15.876c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L2.34 16c-.77 1.333.192 3 1.732 3z"></path>',
        ],
        'error', 'danger' => [
            'wrapper' => 'border-rose-200/80 bg-gradient-to-r from-rose-50/95 to-white text-rose-900 shadow-rose-100/70',
            'iconWrap' => 'bg-rose-100 text-rose-600 ring-1 ring-rose-200/80',
            'title' => 'text-rose-900',
            'body' => 'text-rose-800/90',
            'close' => 'text-rose-400 hover:bg-rose-100 hover:text-rose-600',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0"></path>',
        ],
        default => [
            'wrapper' => 'border-slate-200/80 bg-gradient-to-r from-slate-50/95 to-white text-slate-900 shadow-slate-100/70',
            'iconWrap' => 'bg-slate-100 text-slate-600 ring-1 ring-slate-200/80',
            'title' => 'text-slate-900',
            'body' => 'text-slate-700/90',
            'close' => 'text-slate-400 hover:bg-slate-100 hover:text-slate-600',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0"></path>',
        ],
    };

    $heading = $title ?? match ($type) {
        'success' => 'Success',
        'warning' => 'Heads Up',
        'error', 'danger' => 'Something Went Wrong',
        default => 'Notice',
    };
@endphp

<div
    x-data="{ visible: true }"
    x-init="@if($autohide) setTimeout(() => visible = false, {{ (int) $autohide }}); @endif"
    x-show="visible"
    x-transition:enter="transform ease-out duration-250"
    x-transition:enter-start="opacity-0 -translate-y-2 sm:translate-x-4 sm:translate-y-0 scale-[0.98]"
    x-transition:enter-end="opacity-100 translate-x-0 translate-y-0 scale-100"
    x-transition:leave="transform ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-x-0 translate-y-0 scale-100"
    x-transition:leave-end="opacity-0 -translate-y-1 sm:translate-x-3 sm:translate-y-0 scale-[0.98]"
    {{ $attributes->class([
        'overflow-hidden rounded-xl border shadow-sm backdrop-blur-sm pointer-events-auto',
        $styles['wrapper'],
    ]) }}
>
    <div class="flex items-start gap-3 px-3.5 py-3.5 sm:px-4">
        <div class="mt-0.5 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-xl {{ $styles['iconWrap'] }}">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $styles['icon'] !!}
            </svg>
        </div>

        <div class="min-w-0 flex-1">
            <p class="text-[10px] font-extrabold uppercase tracking-[0.2em] {{ $styles['title'] }}">{{ $heading }}</p>
            <div class="mt-1 text-[13px] font-medium leading-relaxed {{ $styles['body'] }}">
                {{ $slot }}
            </div>
        </div>

        @if($dismissible)
            <button
                type="button"
                @click="visible = false"
                class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-lg transition-colors {{ $styles['close'] }}"
                aria-label="Dismiss alert"
            >
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        @endif
    </div>
</div>
