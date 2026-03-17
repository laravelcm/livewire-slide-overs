@php
    use Laravelcm\LivewireSlideOvers\Position;

    $position = config('livewire-slide-over.position', Position::Right);
    $isLeft = $position === Position::Left;
    $isStacked = config('livewire-slide-over.stack', false);
@endphp

<div>
    @isset($jsPath)
        <script>
            {!! file_get_contents($jsPath) !!}
        </script>
    @endisset

    @isset($cssPath)
        <style>
            {!! file_get_contents($cssPath) !!}
        </style>
    @endisset

    <section
        x-data="SlideOver()"
        data-stacked="{{ $isStacked ? 'true' : 'false' }}"
        x-on:close.stop="setShowPropertyTo(false)"
        x-on:keydown.escape.window="closePanelOnEscape()"
        x-show="open"
        class="relative z-50"
        x-ref="dialog"
        aria-modal="true"
        x-cloak
    >
        <div
            x-cloak
            x-show="open"
            x-on:click="closePanelOnClickAway()"
            x-transition:enter="duration-500 ease-in-out"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="duration-500 ease-in-out"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-zinc-950/50 dark:bg-zinc-950/75"
        ></div>

        <div class="fixed inset-0">
            @if ($isStacked)
                <div
                    @class([
                        'pointer-events-none fixed inset-y-0 grid max-w-full py-2',
                        'left-0 pl-2 pr-10' => $isLeft,
                        'right-0 pr-2 pl-10' => ! $isLeft,
                    ])
                    style="grid-template-areas: 'stack'"
                >
                    @forelse ($components as $id => $component)
                        <div
                            x-show="open && isComponentVisible('{{ $id }}')"
                            x-transition:enter="transform transition duration-500 ease-in-out"
                            x-transition:enter-start="{{ $isLeft ? '-translate-x-full' : 'translate-x-full' }}"
                            x-transition:enter-end="translate-x-0"
                            x-transition:leave="transform transition duration-500 ease-in-out"
                            x-transition:leave-start="translate-x-0"
                            x-transition:leave-end="{{ $isLeft ? '-translate-x-full' : 'translate-x-full' }}"
                            class="pointer-events-auto min-h-0 w-screen"
                            x-bind:class="getComponentPanelAttribute('{{ $id }}', 'maxWidthClass') ?? panelWidth"
                            style="grid-area: stack"
                            wire:key="{{ $id }}"
                        >
                            <div
                                class="h-full transition-[transform,opacity] duration-300 ease-in-out"
                                x-bind:style="getStackStyle('{{ $id }}')"
                                x-bind:inert="activeComponent !== '{{ $id }}'"
                                x-trap="activeComponent === '{{ $id }}'"
                                x-ref="{{ $id }}"
                            >
                                @livewire($component['name'], $component['arguments'], key($id))
                            </div>
                        </div>
                    @empty

                    @endforelse
                </div>
            @else
                <div
                    @class([
                        'pointer-events-none fixed inset-y-0 flex max-w-full py-2',
                        'left-0 pl-2 pr-10' => $isLeft,
                        'right-0 pr-2 pl-10' => ! $isLeft,
                    ])
                >
                    <div
                        x-cloak
                        x-show="open && showActiveComponent"
                        x-transition:enter="transform transition duration-500 ease-in-out"
                        x-transition:enter-start="{{ $isLeft ? '-translate-x-full' : 'translate-x-full' }}"
                        x-transition:enter-end="translate-x-0"
                        x-transition:leave="transform transition duration-500 ease-in-out"
                        x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="{{ $isLeft ? '-translate-x-full' : 'translate-x-full' }}"
                        class="pointer-events-auto w-screen"
                        x-bind:class="panelWidth"
                        x-trap.noscroll.inert="open && showActiveComponent"
                        @click.away="closePanelOnClickAway()"
                        aria-modal="true"
                    >
                        <div
                            class="h-full overflow-hidden rounded-xl bg-zinc-50 shadow-lg ring-1 ring-zinc-950/20 p-1.5 dark:bg-zinc-950 dark:ring-white/10"
                        >
                            @forelse ($components as $id => $component)
                                <div
                                    class="size-full min-w-0 overflow-hidden rounded-md bg-white shadow-lg ring-1 ring-zinc-950/20 dark:bg-zinc-900 dark:ring-white/10"
                                    x-show.immediate="activeComponent == '{{ $id }}'"
                                    x-ref="{{ $id }}"
                                    wire:key="{{ $id }}"
                                >
                                    @livewire($component['name'], $component['arguments'], key($id))
                                </div>
                            @empty

                            @endforelse
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>
