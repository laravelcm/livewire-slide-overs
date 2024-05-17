<p align="center">
    <a href="https://laravel.com">
        <img alt="Laravel v10.x" src="https://img.shields.io/badge/Laravel-v10.x-FF2D20">
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

# Livewire Slide over Panel

Slide Over Panel is a Livewire component that provides drawers (slide overs) that support multiple children while maintaining state.
This package is inspired by [wire-elements/modal](https://github.com/wire-elements/modal), a livewire component that renders modals with state management on livewire.
If you've already used it, then the behavior is the same, except that instead of a vil modal, it will open a drawer.

### Installation

To get started, require the package via Composer

```shell
composer require laravelcm/livewire-slide-overs
```

### Usage
Add the Livewire directive @livewire('slide-over-panel') directive to your master layout.

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

## Opening a Slide over
To open a slide over you will need to dispatch an event. To open the `ShoppingCart` slide over for example:

```html
<!-- Outside of any Livewire component -->
<button onclick="Livewire.dispatch('openPanel', { component: 'shopper-cart' })">View cart</button>

<!-- Inside existing Livewire component -->
<button wire:click="$dispatch('openPanel', { component: 'shopping-cart' })">View cart</button>

<!-- Taking namespace into account for component Shop/Actions/ShoppingCart -->
<button wire:click="$dispatch('openPanel', { component: 'shop.actions.shopping-cart' })">View cart</button>
```

### Configuration
wip...

### Test
wip..

```shell
composer test
```

## Credits
- [Philo Hermans](https://github.com/philoNL)
- [All Contributors](../../contributors)

## License
Livewire Slide Over is open-sourced software licensed under the [MIT license](LICENSE.md).
