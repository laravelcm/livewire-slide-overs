<?php

declare(strict_types=1);

namespace Laravelcm\LivewireSlideOvers;

use InvalidArgumentException;
use Livewire\Component;
use Laravelcm\LivewireSlideOvers\Contracts\PanelContract;

abstract class SlideOverComponent extends Component implements PanelContract
{
    public bool $forceClose = false;

    public int $skipPanels = 0;

    public bool $destroySkipped = false;

    protected static array $maxWidths = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        '5xl' => 'max-w-5xl',
        '6xl' => 'max-w-6xl',
        '7xl' => 'max-w-7xl',
    ];

    public function destroySkippedPanels(): self
    {
        $this->destroySkipped = true;

        return $this;
    }

    public function skipPreviousPanels($count = 1, $destroy = false): self
    {
        $this->skipPreviousPanel($count, $destroy);

        return $this;
    }

    public function skipPreviousPanel($count = 1, $destroy = false): self
    {
        $this->skipPanels = $count;
        $this->destroySkipped = $destroy;

        return $this;
    }

    public function forceClose(): self
    {
        $this->forceClose = true;

        return $this;
    }

    public function closePanel(): void
    {
        $this->dispatch(
            'closePanel',
            force: $this->forceClose,
            skipPreviousPanels: $this->skipPanels,
            destroySkipped: $this->destroySkipped
        );
    }

    public function closePanelWithEvents(array $events): void
    {
        $this->emitPanelEvents($events);
        $this->closePanel();
    }

    public static function panelMaxWidth(): string
    {
        if (static::panelPosition() === Position::Bottom) {
            return '7xl';
        }

        return config('livewire-slide-over.slide_over_max_width', 'xl');
    }

    public static function panelMaxWidthClass(): string
    {
        if (! array_key_exists(SlideOverComponent::panelMaxWidth(), static::$maxWidths)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Panel max width [%s] is invalid. The width must be one of the following [%s].',
                    SlideOverComponent::panelMaxWidth(),
                    implode(', ', array_keys(static::$maxWidths))
                ),
            );
        }

        if (static::panelPosition() === Position::Bottom) {
            dump('Hi');
            return static::$maxWidths['7xl'];
        }

        return static::$maxWidths[SlideOverComponent::panelMaxWidth()];
    }

    public static function panelPosition(): Position
    {
        return config('livewire-slide-over.default_position', Position::Right);
    }

    public static function closePanelOnClickAway(): bool
    {
        return config('livewire-slide-over.close_slide_over_on_click_away', true);
    }

    public static function closePanelOnEscape(): bool
    {
        return config('livewire-slide-over.close_slide_over_on_escape', true);
    }

    public static function closePanelOnEscapeIsForceful(): bool
    {
        return config('livewire-slide-over.close_slide_over_on_escape_is_forceful', true);
    }

    public static function dispatchCloseEvent(): bool
    {
        return config('livewire-slide-over.dispatch_close_event', false);
    }

    public static function destroyOnClose(): bool
    {
        return config('livewire-slide-over.destroy_on_close', false);
    }

    private function emitPanelEvents(array $events): void
    {
        foreach ($events as $component => $event) {
            if (is_array($event)) {
                [$event, $params] = $event;
            }

            if (is_numeric($component)) {
                $this->dispatch($event, ...$params ?? []);
            } else {
                $this->dispatch($event, ...$params ?? [])->to($component);
            }
        }
    }
}
