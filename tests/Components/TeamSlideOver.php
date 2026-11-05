<?php

declare(strict_types=1);

namespace Laravelcm\LivewireSlideOvers\Tests\Components;

use Laravelcm\LivewireSlideOvers\SlideOverComponent;

class TeamSlideOver extends SlideOverComponent
{
    public bool $lazy = false;

    public string $teamName = '';

    public function mount(int $ownerId): void
    {
        abort_unless($ownerId === 1, 403);
    }

    public function render(): string
    {
        return <<<'blade'
            <div>{{ $teamName }}</div>
        blade;
    }
}
