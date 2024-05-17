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

```bash
composer require laravelcm/livewire-slide-overs
```

### Usage
Add the Livewire directive @livewire('slide-over-panel') directive to your master layout.

```bladehtml
<html>
<body>
    <!-- content -->

    @livewire('slide-over-panel')
</body>
</html>
```

### Test

wip..

```bash
composer test
```

## Credits
- [Philo Hermans](https://github.com/philoNL)
- [All Contributors](../../contributors)

## License
Livewire Slide Over is open-sourced software licensed under the [MIT license](LICENSE.md).
