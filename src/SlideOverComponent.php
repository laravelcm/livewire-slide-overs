<?php

declare(strict_types=1);

namespace Laravelcm\LivewireSlideOvers;

use InvalidArgumentException;
use Laravelcm\LivewireSlideOvers\Contracts\PanelContract;
use Livewire\Component;

abstract class SlideOverComponent extends Component implements PanelContract
{
    public bool $forceClose = false;

    public int $skipPanels = 0;

    public bool $destroySkipped = false;

    /** @var array<string, string> */
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

    public static function panelMaxWidth(): string
    {
        /** @var string $width */
        $width = config('livewire-slide-over.component_defaults.slide_over_max_width', 'xl');

        return $width;
    }

    public static function panelMaxWidthClass(): string
    {
        if (! array_key_exists(static::panelMaxWidth(), static::$maxWidths)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Panel max width [%s] is invalid. The width must be one of the following [%s].',
                    static::panelMaxWidth(),
                    implode(', ', array_keys(static::$maxWidths))
                ),
            );
        }

        return static::$maxWidths[static::panelMaxWidth()];
    }

    public static function panelPosition(): Position
    {
        /** @var Position $position */
        $position = config('livewire-slide-over.position', Position::Right);

        return $position;
    }

    public static function closePanelOnClickAway(): bool
    {
        /** @var bool $value */
        $value = config('livewire-slide-over.component_defaults.close_slide_over_on_click_away', true);

        return $value;
    }

    public static function closePanelOnEscape(): bool
    {
        /** @var bool $value */
        $value = config('livewire-slide-over.component_defaults.close_slide_over_on_escape', true);

        return $value;
    }

    public static function closePanelOnEscapeIsForceful(): bool
    {
        /** @var bool $value */
        $value = config('livewire-slide-over.component_defaults.close_slide_over_on_escape_is_forceful', true);

        return $value;
    }

    public static function dispatchCloseEvent(): bool
    {
        /** @var bool $value */
        $value = config('livewire-slide-over.component_defaults.dispatch_close_event', false);

        return $value;
    }

    public static function destroyOnClose(): bool
    {
        /** @var bool $value */
        $value = config('livewire-slide-over.component_defaults.destroy_on_close', false);

        return $value;
    }

    public function destroySkippedPanels(): self
    {
        $this->destroySkipped = true;

        return $this;
    }

    public function skipPreviousPanels(int $count = 1, bool $destroy = false): self
    {
        $this->skipPreviousPanel($count, $destroy);

        return $this;
    }

    public function skipPreviousPanel(int $count = 1, bool $destroy = false): self
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

    /**
     * @param  array<int|string, mixed>  $events
     */
    public function closePanelWithEvents(array $events): void
    {
        $this->emitPanelEvents($events);
        $this->closePanel();
    }

    /**
     * @param  array<int|string, mixed>  $events
     */
    private function emitPanelEvents(array $events): void
    {
        foreach ($events as $component => $event) {
            /** @var array<int, mixed> $params */
            $params = [];

            if (is_array($event)) {
                [$eventName, $eventParams] = $event;
                $event = $eventName;
                /** @var array<int, mixed> $params */
                $params = is_array($eventParams) ? $eventParams : [];
            }

            /** @var string $eventName */
            $eventName = $event;

            if (is_numeric($component)) {
                $this->dispatch($eventName, ...$params);
            } else {
                $dispatcher = $this->dispatch($eventName, ...$params);
                $dispatcher->to((string) $component);
            }
        }
    }
}
