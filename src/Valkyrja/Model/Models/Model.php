<?php

declare(strict_types=1);

/*
 * This file is part of the Valkyrja Framework package.
 *
 * (c) Melech Mizrachi <melechmizrachi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valkyrja\Model\Models;

use Closure;
use JsonException;
use Valkyrja\Model\Model as Contract;
use Valkyrja\Type\Support\Arr;
use Valkyrja\Type\Support\StrCase;

use function is_string;

/**
 * Class Model.
 *
 * @author Melech Mizrachi
 */
abstract class Model implements Contract
{
    /**
     * Cached list of validation logic for models.
     *
     * @var array<string, string>
     */
    protected static array $cachedValidations = [];

    /**
     * Cached list of property/method exists validation logic for models.
     *
     * @var array<string, bool>
     */
    protected static array $cachedExistsValidations = [];

    /**
     * Whether to set the original properties on creation via static::fromArray().
     *
     * @var bool
     */
    protected static bool $shouldSetOriginalProperties = true;

    /**
     * The original properties.
     *
     * @var array<string, mixed>
     */
    private array $internalOriginalProperties = [];

    /**
     * Whether the original properties have been set.
     *
     * @var bool
     */
    private bool $internalOriginalPropertiesSet = false;

    /**
     * @inheritDoc
     */
    public static function fromArray(array $properties): static
    {
        $model = new static();

        $model->internalSetProperties($properties);

        return $model;
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public static function fromValue(mixed $value): static
    {
        if (is_string($value)) {
            $value = Arr::fromString($value);
        } elseif ($value instanceof Contract) {
            $value = $value->asArray();
        } else {
            $value = (array) $value;
        }

        /** @var array<string, mixed> $value */
        return static::fromArray($value);
    }

    /**
     * Whether to set the original properties array.
     *
     * @return bool
     */
    protected static function shouldSetOriginalProperties(): bool
    {
        return static::$shouldSetOriginalProperties;
    }

    /**
     * @inheritDoc
     */
    public function __get(string $name)
    {
        $methodName = $this->internalGetPropertyTypeMethodName($name, 'get');

        if ($this->internalDoesPropertyTypeMethodExist($methodName)) {
            return $this->$methodName();
        }

        return $this->{$name} ?? null;
    }

    /**
     * @inheritDoc
     */
    public function __set(string $name, mixed $value): void
    {
        $methodName = $this->internalGetPropertyTypeMethodName($name, 'set');

        $this->internalSetOriginalProperty($name, $value);

        if ($this->internalDoesPropertyTypeMethodExist($methodName)) {
            $this->$methodName($value);

            return;
        }

        $this->{$name} = $value;
    }

    /**
     * @inheritDoc
     */
    public function __isset(string $name): bool
    {
        $methodName = $this->internalGetPropertyTypeMethodName($name, 'isset');

        if ($this->internalDoesPropertyTypeMethodExist($methodName)) {
            return $this->$methodName();
        }

        return isset($this->$name);
    }

    /**
     * Determine whether the model has a property.
     *
     * @param string $property The property
     *
     * @return bool
     */
    public function hasProperty(string $property): bool
    {
        return self::$cachedExistsValidations[static::class . $property] ??= property_exists($this, $property);
    }

    /**
     * @inheritDoc
     */
    public function updateProperties(array $properties): void
    {
        $this->internalSetProperties($properties);
    }

    /**
     * @inheritDoc
     */
    public function withProperties(array $properties): static
    {
        $model = clone $this;

        $model->internalSetProperties($properties);

        return $model;
    }

    /**
     * @inheritDoc
     */
    public function modify(Closure $closure): static
    {
        $new = clone $this;

        return $closure($new);
    }

    /**
     * @inheritDoc
     */
    public function asValue(): static
    {
        return $this;
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function asFlatValue(): string
    {
        return $this->__toString();
    }

    /**
     * @inheritDoc
     */
    public function asArray(string ...$properties): array
    {
        // Get the public properties
        $allProperties = $this->internalGetAllProperties();

        $this->internalRemoveInternalProperties($allProperties);

        $allProperties = $this->internalCheckOnlyProperties($allProperties, $properties);

        $this->internalSetPropertyValues($allProperties, [$this, '__get']);

        return $allProperties;
    }

    /**
     * @inheritDoc
     */
    public function asChangedArray(): array
    {
        return $this->internalGetChangedProperties($this->asArray());
    }

    /**
     * @inheritDoc
     */
    public function getOriginalPropertyValue(string $name): mixed
    {
        return $this->internalOriginalProperties[$name] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function asOriginalArray(): array
    {
        return $this->internalOriginalProperties;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        $allProperties = $this->internalGetAllProperties();

        $this->internalRemoveInternalProperties($allProperties);
        $this->internalSetPropertyValues($allProperties, [$this, 'internalGetJsonPropertyValue']);

        return $allProperties;
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function __toString(): string
    {
        return Arr::toString($this->jsonSerialize());
    }

    /**
     * Clone model.
     */
    public function __clone()
    {
        $this->internalOriginalPropertiesSet();
    }

    /**
     * Set properties from an array of properties.
     *
     * @param array<string, mixed> $properties The properties to set
     *
     * @return void
     */
    protected function internalSetProperties(array $properties): void
    {
        // Iterate through the properties
        foreach ($properties as $property => $value) {
            if ($this->hasProperty($property)) {
                // Set the property
                $this->__set($property, $value);
            }
        }

        $this->internalOriginalPropertiesSet();
    }

    /**
     * Set that original properties have been set.
     *
     * @return void
     */
    protected function internalOriginalPropertiesSet(): void
    {
        $this->internalOriginalPropertiesSet = true;
    }

    /**
     * Get a property's isset method name.
     *
     * @param string $property The property
     * @param string $type     The type (get|set|isset)
     *
     * @return string
     */
    protected function internalGetPropertyTypeMethodName(string $property, string $type): string
    {
        return self::$cachedValidations[static::class . "$type$property"]
            ??= $type . StrCase::toStudlyCase($property);
    }

    /**
     * Determine if a property type method exists.
     *
     * @param string $methodName The method name
     *
     * @return bool
     */
    protected function internalDoesPropertyTypeMethodExist(string $methodName): bool
    {
        return self::$cachedExistsValidations[static::class . "exists$methodName"]
            ??= method_exists($this, $methodName);
    }

    /**
     * Set an original property.
     *
     * @param string $name  The property name
     * @param mixed  $value The value
     *
     * @return void
     */
    protected function internalSetOriginalProperty(string $name, mixed $value): void
    {
        if (! $this->internalOriginalPropertiesSet && static::shouldSetOriginalProperties()) {
            $this->internalOriginalProperties[$name] ??= $value;
        }
    }

    /**
     * Get all properties.
     *
     * @return array<string, mixed>
     */
    protected function internalGetAllProperties(): array
    {
        /** @var array<string, mixed> */
        return get_object_vars($this);
    }

    /**
     * Remove internal model properties from an array of properties.
     *
     * @param array<string, mixed> $properties The properties
     *
     * @return void
     */
    protected function internalRemoveInternalProperties(array &$properties): void
    {
        unset($properties['internalOriginalProperties'], $properties['internalOriginalPropertiesSet']);
    }

    /**
     * Check if an array of all properties should be filtered by another list of properties.
     *
     * @param array<string, mixed> $properties     The properties
     * @param string[]             $onlyProperties A list of properties to return
     *
     * @return array<string, mixed>
     */
    protected function internalCheckOnlyProperties(array $properties, array $onlyProperties): array
    {
        if (! empty($onlyProperties)) {
            return $this->internalOnlyProperties($properties, $onlyProperties);
        }

        return $properties;
    }

    /**
     * Get an array subset of properties to return from a given list out of the returnable properties.
     *
     * @param array<string, mixed> $allProperties All the properties returnable
     * @param string[]             $properties    The properties we wish to return
     *
     * @return array<string, mixed>
     */
    protected function internalOnlyProperties(array $allProperties, array $properties): array
    {
        $onlyProperties = [];

        // Iterate through the list and set only those properties if the property exists in the allProperties array
        // NOTE: The allProperties array will already have gone through logic to get exposable properties, so only
        //       if the property exists in this array should we return it in the onlyProperties array.
        foreach ($properties as $onlyProperty) {
            if (isset($allProperties[$onlyProperty])) {
                $onlyProperties[$onlyProperty] = true;
            }
        }

        // Return the properties requested
        return $onlyProperties;
    }

    /**
     * Get the changed properties given an array of properties.
     *
     * @param array<string, mixed> $properties The properties to check the original properties against
     *
     * @return array<string, mixed>
     */
    protected function internalGetChangedProperties(array $properties): array
    {
        // The original properties set on the model
        $originalProperties = $this->internalOriginalProperties;
        // The changed properties
        /** @var array<string, mixed> $changed */
        $changed = [];

        // Iterate through the model's properties
        foreach ($properties as $property => $value) {
            $originalProperty = $originalProperties[$property] ?? null;

            // Determine if the property changed
            if ($originalProperty !== $value) {
                $changed[$property] = $value;
            }
        }

        return $changed;
    }

    /**
     * Set property values.
     *
     * @param array<string, mixed> $properties The properties
     * @param callable             $callable   The callable
     *
     * @return void
     */
    protected function internalSetPropertyValues(array &$properties, callable $callable): void
    {
        foreach ($properties as $property => $value) {
            $properties[$property] = $callable($property);
        }
    }

    /**
     * Get a property's value for jsonSerialize.
     *
     * @param string $property The property
     *
     * @return mixed
     */
    protected function internalGetJsonPropertyValue(string $property): mixed
    {
        return $this->__get($property);
    }
}
