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

it('uses the client-provided id when given', function (): void {
    $component = 'demo-slide-over';
    $arguments = ['message' => 'Optimistic'];
    $clientId = 'so-abc123';

    Livewire::test(SlideOverPanel::class)
        ->dispatch('openPanel', component: $component, arguments: $arguments, id: $clientId)
        ->assertSet('activeComponent', $clientId)
        ->assertDispatched('activePanelComponentChanged', id: $clientId);
});

it('falls back to md5 id when no id is provided', function (): void {
    $component = 'demo-slide-over';
    $arguments = ['message' => 'Legacy path'];
    $expectedId = md5($component.json_encode($arguments));

    Livewire::test(SlideOverPanel::class)
        ->dispatch('openPanel', component: $component, arguments: $arguments)
        ->assertSet('activeComponent', $expectedId);
});

it('rejects ids with unsafe characters to prevent expression injection', function (string $unsafeId): void {
    expect(
        fn () => Livewire::test(SlideOverPanel::class)
            ->dispatch('openPanel', component: 'demo-slide-over', id: $unsafeId)
    )->toThrow(InvalidArgumentException::class);
})->with([
    'with single quote' => "foo'; alert(1); //",
    'with double quote' => 'foo"; alert(1); //',
    'with angle brackets' => '<script>alert(1)</script>',
    'with spaces' => 'foo bar',
    'with dots' => 'foo.bar',
    'with slashes' => '../etc/passwd',
    'with backticks' => 'foo`bar',
]);

it('rejects ids that exceed the maximum length', function (): void {
    $tooLong = str_repeat('a', 65);

    expect(
        fn () => Livewire::test(SlideOverPanel::class)
            ->dispatch('openPanel', component: 'demo-slide-over', id: $tooLong)
    )->toThrow(InvalidArgumentException::class);
});

it('rejects an empty id when explicitly provided', function (): void {
    expect(
        fn () => Livewire::test(SlideOverPanel::class)
            ->dispatch('openPanel', component: 'demo-slide-over', id: '')
    )->toThrow(InvalidArgumentException::class);
});

it('accepts safe id patterns', function (string $safeId): void {
    Livewire::test(SlideOverPanel::class)
        ->dispatch('openPanel', component: 'demo-slide-over', id: $safeId)
        ->assertSet('activeComponent', $safeId);
})->with([
    'alphanumeric' => 'abc123',
    'with hyphen' => 'so-abc-def',
    'with underscore' => 'panel_id_42',
    'prefixed hash' => 'so-1y2u3w-2k9v',
    'single char' => 'a',
    'max length' => str_repeat('x', 64),
]);
