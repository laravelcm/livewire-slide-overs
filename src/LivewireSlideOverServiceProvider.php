<?php

declare(strict_types=1);

namespace Laravelcm\LivewireSlideOvers;

use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class LivewireSlideOverServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('livewire-slide-over')
            ->hasConfigFile()
            ->hasViews();
    }

    public function bootingPackage(): void
    {
        Livewire::component('slide-over-panel', SlideOverPanel::class);
    }
}
