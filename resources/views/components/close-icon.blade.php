@php
    $closeOnEscape = config('livewire-slide-over.component_defaults.close_slide_over_on_escape', true);
@endphp

<div class="flex items-center gap-2 h-7">
    @if ($closeOnEscape)
        <span
            class="inline-flex items-center rounded-md bg-zinc-50 px-2 py-1 text-xs text-zinc-500 dark:bg-zinc-800 dark:text-zinc-500">
            esc
        </span>
    @endif

    <button
        type="button"
        class="rounded-md bg-white text-zinc-400 outline-none hover:text-zinc-500 dark:bg-zinc-900 dark:text-zinc-500 dark:hover:text-zinc-300"
        wire:click="$dispatch('closePanel')"
    >
        <span class="sr-only">Close panel</span>
        <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M17 7L7 17M7 7L17 17" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
</div>
