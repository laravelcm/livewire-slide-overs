<?php

declare(strict_types=1);

namespace Laravelcm\LivewireSlideOvers;

use Exception;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Reflector;
use Laravelcm\LivewireSlideOvers\Contracts\PanelContract;
use Livewire\Component;
use Livewire\Mechanisms\ComponentRegistry;
use ReflectionClass;

class SlideOverPanel extends Component
{
    public ?string $activeComponent;

    public array $components = [];

    public function resetState(): void
    {
        $this->components = [];
        $this->activeComponent = null;
    }

    public function openPanel($component, $arguments = [], $panelAttributes = []): void
    {
        $requiredInterface = PanelContract::class;
        $componentClass = app(ComponentRegistry::class)->getClass($component);
        $reflect = new ReflectionClass($componentClass);

        if ($reflect->implementsInterface($requiredInterface) === false) {
            throw new Exception("[{$componentClass}] does not implement [{$requiredInterface}] interface.");
        }

        $id = md5($component . serialize($arguments));

        $arguments = collect($arguments)
            ->merge($this->resolveComponentProps($arguments, new $componentClass()))
            ->all();

        $this->components[$id] = [
            'name' => $component,
            'arguments' => $arguments,
            'panelAttributes' => array_merge([
                'closeOnClickAway' => $componentClass::closePanelOnClickAway(),
                'closeOnEscape' => $componentClass::closePanelOnEscape(),
                'closeOnEscapeIsForceful' => $componentClass::closePanelOnEscapeIsForceful(),
                'dispatchCloseEvent' => $componentClass::dispatchCloseEvent(),
                'destroyOnClose' => $componentClass::destroyOnClose(),
                'maxWidth' => $componentClass::panelMaxWidth(),
                'maxWidthClass' => $componentClass::panelMaxWidthClass(),
                'position' => $componentClass::panelPosition(),
            ], $panelAttributes),
        ];

        $this->activeComponent = $id;

        $this->dispatch('activePanelComponentChanged', id: $id);
    }

    public function resolveComponentProps(array $attributes, Component $component): Collection
    {
        return $this->getPublicPropertyTypes($component)
            ->intersectByKeys($attributes)
            ->map(function ($className, $propName) use ($attributes) {
                $resolved = $this->resolveParameter($attributes, $propName, $className);

                return $resolved;
            });
    }

    protected function resolveParameter($attributes, $parameterName, $parameterClassName)
    {
        $parameterValue = $attributes[$parameterName];

        if ($parameterValue instanceof UrlRoutable) {
            return $parameterValue;
        }

        if (enum_exists($parameterClassName)) {
            $enum = $parameterClassName::tryFrom($parameterValue);

            if ($enum !== null) {
                return $enum;
            }
        }

        $instance = app()->make($parameterClassName);

        if (! $model = $instance->resolveRouteBinding($parameterValue)) {
            throw (new ModelNotFoundException())->setModel(get_class($instance), [$parameterValue]);
        }

        return $model;
    }

    public function getPublicPropertyTypes($component): Collection
    {
        return collect($component->all())
            ->map(fn ($value, $name) => Reflector::getParameterClassName(new \ReflectionProperty($component, $name)))
            ->filter();
    }

    public function destroyComponent($id): void
    {
        unset($this->components[$id]);
    }

    public function getListeners(): array
    {
        return [
            'openPanel',
            'destroyComponent',
        ];
    }

    public function render(): View
    {
        if (config('livewire-slide-overs.include_js', true)) {
            $jsPath = __DIR__ . '/../public/slide-over.js';
        }

        if (config('livewire-slide-overs.include_css', true)) {
            $cssPath = __DIR__ . '/../public/slide-over.css';
        }

        return view('livewire-slide-over::slide-over', [
            'jsPath' => $jsPath ?? null,
            'cssPath' => $cssPath ?? null,
        ]);
    }
}
