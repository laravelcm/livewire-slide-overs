# Changelog

All notable changes to `livewire-slide-overs` will be documented in this file.

## v2.0.1 - 2026-03-17

- fix: support both Livewire 3.x and 4.x component resolution (#13)

## v2.0.0 - 2026-03-16

### Breaking Changes

- `PanelContract` now defines method signatures (was an empty marker interface)
- Component ID hashing changed from `serialize()` to `json_encode()`
- `getListeners()` replaced by `#[On]` attributes

### New Features

- Stacked panels support (`'stack' => true` in config)
- Per-component position in panelAttributes

### Improvements

- Bug fix: `this.show` → `this.open` in closePanel() JS
- Escape key closes only active panel in stacked mode
- Modern Livewire: `#[On]` attributes
- Cleaner type resolution: `Reflector::getParameterClassName()`
- Reactive close-icon: Alpine `x-if="closeOnEscape"` per-component
- Build workflow: path filter on `resources/`
- 2 new tests
