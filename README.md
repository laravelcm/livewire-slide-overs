<p align="center">
    <a href="https://github.com/laravelcm/livewire-slide-overs/actions">
        <img src="https://github.com/laravelcm/livewire-slide-overs/workflows/Tests/badge.svg" alt="Build Status">
    </a>
    <a href="https://packagist.org/packages/laravelcm/livewire-slide-overs">
        <img src="https://img.shields.io/packagist/dt/laravelcm/livewire-slide-overs" alt="Total Downloads">
    </a>
    <a href="https://packagist.org/packages/laravelcm/livewire-slide-overs">
        <img src="https://img.shields.io/packagist/v/laravelcm/livewire-slide-overs" alt="Latest Stable Version">
    </a>
    <a href="https://packagist.org/packages/laravelcm/livewire-slide-overs">
        <img src="https://img.shields.io/packagist/l/laravelcm/livewire-slide-overs" alt="License">
    </a>
</p>

# Livewire Slide Over Panel

Livewire Slide Over Panel is a Livewire component that provides drawers (slide overs) that support multiple children while maintaining state.

This package is inspired by [wire-elements/modal](https://github.com/wire-elements/modal). If you've already used it, then the behavior is the same, except that instead of a modal, it will open a drawer (slide over).

## Requirements

- PHP 8.3 or higher
- Laravel 11.x or 12.x
- Livewire 3.4 or 4.x

## Installation

To get started, require the package via Composer:

```shell
composer require laravelcm/livewire-slide-overs
```

## Usage

Add the Livewire directive `@livewire('slide-over-panel')` to your template layout:

```blade
<html>
<body>
    <!-- content -->

    @livewire('slide-over-panel')
</body>
</html>
```

## Creating a Slide Over

You can run `php artisan make:livewire ShoppingCart` to make the initial Livewire component. Open your component class and make sure it extends the `SlideOverComponent` class:

```php
<?php

namespace App\Livewire;

use Laravelcm\LivewireSlideOvers\SlideOverComponent;

class ShoppingCart extends SlideOverComponent
{
    public function render()
    {
        return view('livewire.shopping-cart');
    }
}
```

## Opening a Slide Over

To open a slide over you will need to dispatch an event. To open the `ShoppingCart` slide over for example:

```blade
<!-- Outside of any Livewire component -->
<button onclick="Livewire.dispatch('openPanel', { component: 'shopping-cart' })">View cart</button>

<!-- Inside an existing Livewire component -->
<button wire:click="$dispatch('openPanel', { component: 'shopping-cart' })">View cart</button>

<!-- Taking namespace into account for component Shop/Actions/ShoppingCart -->
<button wire:click="$dispatch('openPanel', { component: 'shop.actions.shopping-cart' })">View cart</button>
```

### Instant Open (Optimistic UI)

Dispatching `openPanel` waits for a full Livewire round-trip before the drawer animates. The `window.$slideOver` helper opens the drawer chrome immediately while the Livewire content hydrates inside it during the slide transition:

```blade
<button onclick="$slideOver.open('shopping-cart')">View cart</button>

<!-- With arguments -->
<button onclick="$slideOver.open('edit-user', { user: {{ $user->id }} })">Edit user</button>

<!-- With panel attribute overrides -->
<button onclick="$slideOver.open('shopping-cart', {}, { maxWidthClass: 'max-w-2xl' })">View cart</button>
```

**How the cache works.** No registration or config is required. The first time a given component is opened in a session, the helper falls back to a standard Livewire round-trip (the drawer can't animate without knowing its width and position). Once the server response returns, the resolved panel attributes are stored in `sessionStorage`, and every subsequent open of the same component during that session is instant.

If you want the very first open to be instant too, pass the relevant attributes explicitly as the third argument — they bypass the cache and let the helper animate immediately.

## Passing Data

You can pass data to the slide over component by adding an `arguments` object to the dispatch event:

```blade
<button wire:click="$dispatch('openPanel', { component: 'edit-user', arguments: { user: {{ $user->id }} }})">
    Edit User
</button>
```

The arguments will be passed to the `mount()` method of your slide over component:

```php
<?php

namespace App\Livewire;

use App\Models\User;
use Laravelcm\LivewireSlideOvers\SlideOverComponent;

class EditUser extends SlideOverComponent
{
    public User $user;

    public function mount(User $user)
    {
        $this->user = $user;
    }

    public function render()
    {
        return view('livewire.edit-user');
    }
}
```

Model binding is automatic - just type-hint the model and pass the ID as the argument.

## Resizing a Slide Over Dynamically

A slide over's width is normally set once via `panelMaxWidth()` on the component class. To change the width while the panel is open (for example, expanding when the user reveals additional content), call `resizePanel()` from your component:

```php
public function loadDetails(): void
{
    // ... fetch data ...

    // Expand the panel to give the new content some room.
    $this->resizePanel('max-w-6xl');
}

public function clearDetails(): void
{
    // ... reset ...

    $this->resizePanel('max-w-2xl');
}
```

The argument is a Tailwind max-width utility class (`max-w-sm` through `max-w-7xl`) or any arbitrary class string — including JIT values such as `max-w-[800px]`. The change is applied client-side via a smooth CSS transition; no full re-render of the panel is required.

To target a specific panel in a stacked configuration, pass its id as the second argument:

```php
$this->resizePanel('max-w-4xl', $panelId);
```

When omitted, the currently active panel is resized.

## Closing the Slide Over

From within your slide over component, you can call `$this->closePanel()`:

```php
public function save()
{
    // Save logic...

    $this->closePanel();
}
```

### Force Close

If you have multiple slide overs open and want to close them all at once, use `forceClose()`:

```php
public function save()
{
    $this->forceClose()->closePanel();
}
```

### Skip Previous Panels

You can skip one or more previous panels in the stack:

```php
// Skip the previous panel
$this->skipPreviousPanel()->closePanel();

// Skip multiple panels
$this->skipPreviousPanels(2)->closePanel();

// Skip and destroy the skipped panels
$this->skipPreviousPanels(2)->destroySkippedPanels()->closePanel();
```

### Close with Events

Close the panel and emit events to other components:

```php
// Emit a simple event
$this->closePanelWithEvents(['refreshList']);

// Emit event to a specific component
$this->closePanelWithEvents([
    'user-table' => 'refreshList',
]);

// Emit event with parameters
$this->closePanelWithEvents([
    ['refreshList', ['user' => $this->user->id]],
]);
```

## Component Properties

You can customize the slide over behavior by overriding static methods in your component:

```php
<?php

namespace App\Livewire;

use Laravelcm\LivewireSlideOvers\SlideOverComponent;

class EditUser extends SlideOverComponent
{
    // Set the max width (sm, md, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl, 7xl)
    public static function panelMaxWidth(): string
    {
        return '2xl';
    }

    // Disable closing by clicking away
    public static function closePanelOnClickAway(): bool
    {
        return false;
    }

    // Disable closing on escape key
    public static function closePanelOnEscape(): bool
    {
        return false;
    }

    // Make escape key close only the current panel (not force close all)
    public static function closePanelOnEscapeIsForceful(): bool
    {
        return false;
    }

    // Dispatch a close event when the panel is closed
    public static function dispatchCloseEvent(): bool
    {
        return true;
    }

    // Destroy the component state when closed
    public static function destroyOnClose(): bool
    {
        return true;
    }

    public function render()
    {
        return view('livewire.edit-user');
    }
}
```

## Configuration

You can publish the configuration file:

```shell
php artisan vendor:publish --tag=livewire-slide-over-config
```

Available configuration options:

```php
return [
    // Include the package CSS (set to true if not using TailwindCSS)
    'include_css' => false,

    // Include the package JavaScript
    'include_js' => true,

    // Slide over position: Position::Right or Position::Left
    'position' => Position::Right,

    // Default component settings
    'component_defaults' => [
        'slide_over_max_width' => 'xl',
        'close_slide_over_on_click_away' => true,
        'close_slide_over_on_escape' => true,
        'close_slide_over_on_escape_is_forceful' => true,
        'dispatch_close_event' => false,
        'destroy_on_close' => false,
    ],
];
```

## TailwindCSS

The slide over uses TailwindCSS classes. If you use TailwindCSS in your project, you need to configure it to scan the package views.

### Tailwind CSS v4

Add the package views using the `@source` directive in your CSS file:

```css
@import "tailwindcss";
@source "../vendor/laravelcm/livewire-slide-overs/resources/views";
```

### Tailwind CSS v3

Add the package views to your `tailwind.config.js` content array:

```js
module.exports = {
    content: [
        // ...
        './vendor/laravelcm/livewire-slide-overs/resources/views/**/*.blade.php',
    ],
    // ...
}
```

### Without TailwindCSS

If you don't use TailwindCSS, set `include_css` to `true` in your configuration file to include the precompiled styles.

## Testing

```shell
composer test
```

## Credits

- [Arthur Monney](https://github.com/mckenziearts)
- [Philo Hermans](https://github.com/philoNL)
- [All Contributors](../../contributors)

## License

Livewire Slide Over is open-sourced software licensed under the [MIT license](LICENSE.md).
