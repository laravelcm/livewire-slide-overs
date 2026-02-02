<?php

declare(strict_types=1);

namespace Laravelcm\LivewireSlideOvers;

use BackedEnum;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Laravelcm\LivewireSlideOvers\Contracts\PanelContract;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\Finder\Finder;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;

class SlideOverPanel extends Component
{
    #[Locked]
    public ?string $activeComponent = null;

    /** @var array<string, array<string, mixed>> */
    #[Locked]
    public array $components = [];

    /**
     * @param  array<string, mixed>  $attributes
     * @param  class-string  $parameterClassName
     *
     * @throws BindingResolutionException
     */
    protected function resolveParameter(array $attributes, string $parameterName, string $parameterClassName): mixed
    {
        $parameterValue = $attributes[$parameterName];

        if ($parameterValue instanceof UrlRoutable) {
            return $parameterValue;
        }

        if (enum_exists($parameterClassName) && is_subclass_of($parameterClassName, BackedEnum::class)) {
            /** @var int|string $enumValue */
            $enumValue = $parameterValue;
            /** @var class-string<BackedEnum> $enumClass */
            $enumClass = $parameterClassName;
            $enum = $enumClass::tryFrom($enumValue);

            if ($enum !== null) {
                return $enum;
            }
        }

        /** @var Model $instance */
        $instance = app()->make($parameterClassName);

        /** @var int|string $bindingValue */
        $bindingValue = $parameterValue;
        $model = $instance->resolveRouteBinding($bindingValue);

        if (! $model) {
            /** @var class-string<Model> $modelClass */
            $modelClass = $parameterClassName;

            throw (new ModelNotFoundException)->setModel($modelClass, [$bindingValue]);
        }

        return $model;
    }

    /**
     * @return class-string
     */
    protected function resolveComponentClass(string $component): string
    {
        if (class_exists(\Livewire\Mechanisms\ComponentRegistry::class)) {
            /** @var class-string $class */
            $class = app(\Livewire\Mechanisms\ComponentRegistry::class)->getClass($component);

            return $class;
        }

        /** @var Finder $finder */
        $finder = app('livewire.finder');

        /** @var class-string $class */
        $class = $finder->resolveClassComponentClassName($component);

        return $class;
    }

    public function resetState(): void
    {
        $this->components = [];
        $this->activeComponent = null;
    }

    /**
     * @param  array<string, mixed>  $arguments
     * @param  array<string, mixed>  $panelAttributes
     *
     * @throws ReflectionException
     */
    public function openPanel(string $component, array $arguments = [], array $panelAttributes = []): void
    {
        $requiredInterface = PanelContract::class;
        /** @var class-string<SlideOverComponent> $componentClass */
        $componentClass = $this->resolveComponentClass($component);
        $reflect = new ReflectionClass($componentClass);

        if ($reflect->implementsInterface($requiredInterface) === false) {
            throw new Exception("[{$componentClass}] does not implement [{$requiredInterface}] interface.");
        }

        $id = md5($component.serialize($arguments));

        $arguments = collect($arguments)
            ->merge($this->resolveComponentProps($arguments, new $componentClass))
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
            ], $panelAttributes),
        ];

        $this->activeComponent = $id;

        $this->dispatch('activePanelComponentChanged', id: $id);
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return Collection<string, mixed>
     */
    public function resolveComponentProps(array $attributes, Component $component): Collection
    {
        return $this->getPublicPropertyTypes($component)
            ->intersectByKeys($attributes)
            ->map(
                /** @param class-string $className */
                fn (string $className, string $propName): mixed => $this->resolveParameter($attributes, $propName, $className)
            );
    }

    /**
     * @return Collection<string, class-string>
     */
    public function getPublicPropertyTypes(Component $component): Collection
    {
        /** @var array<string, mixed> $properties */
        $properties = $component->all();

        /** @var Collection<string, class-string> $result */
        $result = collect($properties)
            ->map(function (mixed $value, string $name) use ($component): ?string {
                $property = new ReflectionProperty($component, $name);
                $type = $property->getType();

                if ($type instanceof ReflectionNamedType && ! $type->isBuiltin()) {
                    return $type->getName();
                }

                return null;
            })
            ->filter();

        return $result;
    }

    public function destroyComponent(string $id): void
    {
        unset($this->components[$id]);
    }

    /**
     * @return array<int, string>
     */
    public function getListeners(): array
    {
        return [
            'openPanel',
            'destroyComponent',
        ];
    }

    public function render(): View
    {
        $jsPath = null;
        $cssPath = null;

        if (config('livewire-slide-over.include_js', true)) {
            $jsPath = __DIR__.'/../public/slide-over.js';
        }

        if (config('livewire-slide-over.include_css', true)) {
            $cssPath = __DIR__.'/../public/slide-over.css';
        }

        return view('livewire-slide-over::slide-over', [
            'jsPath' => $jsPath,
            'cssPath' => $cssPath,
        ]);
    }
}
