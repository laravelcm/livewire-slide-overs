<?php

declare(strict_types=1);

use Laravelcm\LivewireSlideOvers\SlideOverPanel;
use Laravelcm\LivewireSlideOvers\Tests\Components\DemoSlideOver;
use Laravelcm\LivewireSlideOvers\Tests\Components\InvalidSlideOver;
use Livewire\Livewire;

beforeEach(function (): void {
    Livewire::component('demo-slide-over', DemoSlideOver::class);
    Livewire::component('invalid-slide-over', InvalidSlideOver::class);
});

it('opens a panel via openPanel event', function (): void {
    $component = 'demo-slide-over';
    $arguments = ['user' => 1, 'number' => 42, 'message' => 'Hello World'];
    $panelAttributes = [
        'closeOnEscape' => true,
        'maxWidth' => '2xl',
        'maxWidthClass' => 'max-w-2xl',
        'closeOnClickAway' => true,
        'closeOnEscapeIsForceful' => true,
        'dispatchCloseEvent' => false,
        'destroyOnClose' => false,
        'position' => 'right',
    ];

    $id = md5($component.json_encode($arguments));

    Livewire::test(SlideOverPanel::class)
        ->dispatch('openPanel', component: $component, arguments: $arguments, panelAttributes: $panelAttributes)
        ->assertSet('components', [
            $id => [
                'name' => $component,
                'arguments' => $arguments,
                'panelAttributes' => $panelAttributes,
            ],
        ])
        ->assertSet('activeComponent', $id)
        ->assertDispatched('activePanelComponentChanged', id: $id)
        ->assertSee(['Hello World', 1, '42']);
});

it('destroys a component via destroyComponent event', function (): void {
    $component = 'demo-slide-over';
    $arguments = ['message' => 'Foobar'];
    $panelAttributes = [
        'closeOnEscape' => true,
        'maxWidth' => '2xl',
        'maxWidthClass' => 'max-w-2xl',
        'closeOnClickAway' => true,
        'closeOnEscapeIsForceful' => true,
        'dispatchCloseEvent' => false,
        'destroyOnClose' => false,
        'position' => 'right',
    ];

    $id = md5($component.json_encode($arguments));

    Livewire::test(SlideOverPanel::class)
        ->dispatch('openPanel', component: $component, arguments: $arguments, panelAttributes: $panelAttributes)
        ->assertSet('components', [
            $id => [
                'name' => $component,
                'arguments' => $arguments,
                'panelAttributes' => $panelAttributes,
            ],
        ])
        ->dispatch('destroyComponent', id: $id)
        ->assertSet('components', []);
});

it('resets state correctly', function (): void {
    Livewire::test(SlideOverPanel::class)
        ->dispatch('openPanel', component: 'demo-slide-over', arguments: ['message' => 'Test'])
        ->assertNotSet('activeComponent', null)
        ->assertNotSet('components', [])
        ->call('resetState')
        ->assertSet('activeComponent', null)
        ->assertSet('components', []);
});

it('throws exception when component does not implement PanelContract', function (): void {
    $component = InvalidSlideOver::class;

    $this->expectException(Exception::class);
    $this->expectExceptionMessage("[{$component}] does not implement [Laravelcm\LivewireSlideOvers\Contracts\PanelContract] interface.");

    Livewire::test(SlideOverPanel::class)
        ->dispatch('openPanel', component: 'invalid-slide-over');
});

it('opens panel with default attributes when no panelAttributes provided', function (): void {
    $component = 'demo-slide-over';
    $arguments = ['message' => 'Default'];

    $id = md5($component.json_encode($arguments));

    Livewire::test(SlideOverPanel::class)
        ->dispatch('openPanel', component: $component, arguments: $arguments)
        ->assertSet('activeComponent', $id)
        ->assertDispatched('activePanelComponentChanged', id: $id);
});

it('includes position in panel attributes', function (): void {
    $component = 'demo-slide-over';
    $arguments = ['message' => 'Position test'];

    $id = md5($component.json_encode($arguments));

    $testable = Livewire::test(SlideOverPanel::class)
        ->dispatch('openPanel', component: $component, arguments: $arguments);

    $components = $testable->get('components');

    expect($components[$id]['panelAttributes'])->toHaveKey('position', 'right');
});
