<?php

declare(strict_types=1);

use Laravelcm\LivewireSlideOvers\Tests\Components\DemoSlideOver;
use Livewire\Livewire;

beforeEach(function (): void {
    Livewire::component('demo-slide-over', DemoSlideOver::class);
});

it('closes the panel', function (): void {
    Livewire::test(DemoSlideOver::class)
        ->call('closePanel')
        ->assertDispatched('closePanel', force: false, skipPreviousPanels: 0, destroySkipped: false);
});

it('force closes the panel', function (): void {
    Livewire::test(DemoSlideOver::class)
        ->call('forceClose')
        ->call('closePanel')
        ->assertDispatched('closePanel', force: true, skipPreviousPanels: 0, destroySkipped: false);
});

it('skips previous panels', function (): void {
    Livewire::test(DemoSlideOver::class)
        ->call('skipPreviousPanels', 5)
        ->call('closePanel')
        ->assertDispatched('closePanel', force: false, skipPreviousPanels: 5, destroySkipped: false);
});

it('skips previous panel (singular)', function (): void {
    Livewire::test(DemoSlideOver::class)
        ->call('skipPreviousPanel')
        ->call('closePanel')
        ->assertDispatched('closePanel', force: false, skipPreviousPanels: 1, destroySkipped: false);
});

it('destroys skipped panels', function (): void {
    Livewire::test(DemoSlideOver::class)
        ->call('skipPreviousPanel')
        ->call('destroySkippedPanels')
        ->call('closePanel')
        ->assertDispatched('closePanel', force: false, skipPreviousPanels: 1, destroySkipped: true);
});

it('emits events when closing panel with events', function (): void {
    Livewire::test(DemoSlideOver::class)
        ->call('closePanelWithEvents', [
            'someEvent',
        ])
        ->assertDispatched('someEvent');
});

it('emits events to specific component when closing panel', function (): void {
    Livewire::test(DemoSlideOver::class)
        ->call('closePanelWithEvents', [
            'demo-slide-over' => 'someEvent',
        ])
        ->assertDispatched('someEvent');
});

it('emits events with parameters when closing panel', function (): void {
    Livewire::test(DemoSlideOver::class)
        ->call('closePanelWithEvents', [
            ['someEventWithParams', ['param1', 'param2']],
        ])
        ->assertDispatched('someEventWithParams', 'param1', 'param2');
});

it('emits events with parameters to specific component when closing panel', function (): void {
    Livewire::test(DemoSlideOver::class)
        ->call('closePanelWithEvents', [
            'demo-slide-over' => ['someEventWithParams', ['param1', 'param2']],
        ])
        ->assertDispatched('someEventWithParams', 'param1', 'param2');
});
