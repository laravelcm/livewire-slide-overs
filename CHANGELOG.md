# Changelog

All notable changes to `livewire-slide-overs` will be documented in this file.

## v2.1.1 - 2026-11-05

### Security

`openPanel` passed every argument sent by the browser to `@livewire()`. A client could add `lazy` or `defer` and open any registered slide-over as a lazy placeholder, which skips its `mount()` (and the authorization placed there) while its public actions stay callable. All versions up to v2.1.0 are affected. Details in [GHSA-hg7g-pvvc-mggp](https://github.com/laravelcm/livewire-slide-overs/security/advisories/GHSA-hg7g-pvvc-mggp).

`openPanel` now keeps only the arguments that match a public property or a `mount()` parameter of the slide-over, and always drops `lazy` and `defer`. Numeric keys and undeclared keys are no longer passed; kebab-case keys are mapped to camelCase.

Laravel 11 is no longer tested: Composer refuses to install any `laravel/framework` 11.x release because of open security advisories.

### What's Changed

* chore(deps): bump actions/setup-node from 6 to 7 by @dependabot[bot] in https://github.com/laravelcm/livewire-slide-overs/pull/23
* chore(deps): bump actions/cache from 5 to 6 by @dependabot[bot] in https://github.com/laravelcm/livewire-slide-overs/pull/22
* chore(deps): bump actions/checkout from 6 to 7 by @dependabot[bot] in https://github.com/laravelcm/livewire-slide-overs/pull/21
* chore(deps-dev): bump the js-dependencies group with 2 updates by @dependabot[bot] in https://github.com/laravelcm/livewire-slide-overs/pull/20
* fix: pass only declared arguments from openPanel to the slide-over by @mckenziearts in https://github.com/laravelcm/livewire-slide-overs/pull/24

**Full Changelog**: https://github.com/laravelcm/livewire-slide-overs/compare/v2.1.0...v2.1.1

## v2.1.0 - 2026-05-13

### What's Changed

* feat: instant open, dynamic resize, native dialog mode by @mckenziearts in https://github.com/laravelcm/livewire-slide-overs/pull/19

**Full Changelog**: https://github.com/laravelcm/livewire-slide-overs/compare/v2.0.4...v2.1.0

## v2.0.4 - 2026-05-08

### What's Changed

* chore(deps-dev): bump the js-dependencies group with 2 updates by @dependabot[bot] in https://github.com/laravelcm/livewire-slide-overs/pull/16
* chore(deps-dev): bump the js-dependencies group with 2 updates by @dependabot[bot] in https://github.com/laravelcm/livewire-slide-overs/pull/17
* fix: stacked slide-over UX (ESC pops single panel, responsive width) by @mckenziearts in https://github.com/laravelcm/livewire-slide-overs/pull/18

**Full Changelog**: https://github.com/laravelcm/livewire-slide-overs/compare/v2.0.3...v2.0.4

## v2.0.3 - 2026-03-17

### What's Changed

* fix: safelist max-width classes for Tailwind v4 by @mckenziearts in https://github.com/laravelcm/livewire-slide-overs/pull/15

**Full Changelog**: https://github.com/laravelcm/livewire-slide-overs/compare/v2.0.2...v2.0.3

## v2.0.2 - 2026-03-17

### What's Changed

* fix: stacked panel overflow and initialize changelog by @mckenziearts in https://github.com/laravelcm/livewire-slide-overs/pull/14

**Full Changelog**: https://github.com/laravelcm/livewire-slide-overs/compare/v2.0.1...v2.0.2

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
