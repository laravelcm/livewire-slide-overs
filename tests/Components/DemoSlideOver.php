<?php

declare(strict_types=1);

namespace Laravelcm\LivewireSlideOvers\Tests\Components;

use Laravelcm\LivewireSlideOvers\SlideOverComponent;

class DemoSlideOver extends SlideOverComponent
{
    public $user;

    public $number;

    public $message;

    public function render(): string
    {
        return <<<'blade'
            <div>
                {{ $user }} says:
                {{ $message }} + {{ $number }}
            </div>
        blade;
    }
}
