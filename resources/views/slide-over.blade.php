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

    @if ($isStacked)
        <section
            x-data="SlideOver('stack')"
            data-stacked="true"
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
                            class="pointer-events-auto min-h-0 w-[calc(100vw-3rem)] sm:w-screen ease-in-out"
                            :class="[getComponentPanelAttribute('{{ $id }}', 'maxWidthClass') ?? panelWidth, inSwitch ? '' : 'transition-[max-width] duration-300']"
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
            </div>
        </section>
    @else
        <style wire:ignore>
            dialog[data-livewire-slide-over] {
                position: fixed;
                top: 0.5rem;
                bottom: 0.5rem;
                {{ $isLeft ? 'left: 0.5rem; right: auto;' : 'right: 0.5rem; left: auto;' }}
                margin: 0;
                padding: 0;
                border: 0;
                background: transparent;
                color: inherit;
                width: calc(100vw - 3rem);
                height: calc(100vh - 1rem);
                max-height: calc(100vh - 1rem);
                overflow: visible;
                transform: translateX({{ $isLeft ? 'calc(-100% - 0.5rem)' : 'calc(100% + 0.5rem)' }});
                transition: transform 0.35s cubic-bezier(0.32, 0.72, 0, 1), max-width 0.3s ease-in-out, display 0.35s allow-discrete;
            }

            dialog[data-livewire-slide-over]:not([open]) {
                display: none;
            }

            dialog[data-livewire-slide-over][open] {
                transform: translateX(0);
            }

            @starting-style {
                dialog[data-livewire-slide-over][open] {
                    transform: translateX({{ $isLeft ? 'calc(-100% - 0.5rem)' : 'calc(100% + 0.5rem)' }});
                }
            }

            dialog[data-livewire-slide-over]::backdrop {
                background-color: rgba(9, 9, 11, 0.5);
                opacity: 0;
                transition: opacity 0.35s ease-in-out, display 0.35s allow-discrete;
            }

            dialog[data-livewire-slide-over][open]::backdrop {
                opacity: 1;
            }

            @starting-style {
                dialog[data-livewire-slide-over][open]::backdrop {
                    opacity: 0;
                }
            }

            dialog[data-livewire-slide-over] .slide-over-panel-item {
                position: absolute;
                inset: 0;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.2s ease-in-out;
            }

            dialog[data-livewire-slide-over] .slide-over-panel-item.is-active {
                opacity: 1;
                pointer-events: auto;
            }
        </style>

        <dialog
            wire:ignore.self
            x-data="SlideOver('dialog')"
            data-livewire-slide-over
            data-stacked="false"
            x-ref="dialog"
            x-on:keydown.escape.prevent.stop="closePanelOnEscape()"
            x-on:cancel.prevent
            @click.self="closePanelOnClickAway()"
            :class="panelWidth"
        >
            <div class="relative h-full w-full overflow-hidden">
                @forelse ($components as $id => $component)
                    <div
                        class="slide-over-panel-item size-full overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-zinc-950/20 dark:bg-zinc-900 dark:ring-white/10"
                        :class="activeComponent === '{{ $id }}' ? 'is-active' : ''"
                        x-ref="{{ $id }}"
                        wire:key="{{ $id }}"
                    >
                        @livewire($component['name'], $component['arguments'], key($id))
                    </div>
                @empty

                @endforelse
            </div>
        </dialog>
    @endif
</div>
