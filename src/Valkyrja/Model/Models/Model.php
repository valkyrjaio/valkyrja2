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

use JsonException;
use Valkyrja\Model\Model as Contract;
use Valkyrja\Type\Support\Arr;
use Valkyrja\Type\Support\StrCase;

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
     */
    protected static bool $shouldSetOriginalProperties = true;

    /**
     * The original properties.
     */
    private array $__originalProperties = [];

    /**
     * Whether the original properties have been set.
     */
    private bool $__originalPropertiesSet = false;

    /**
     * @inheritDoc
     */
    public static function fromArray(array $properties): static
    {
        $model = static::__getNew($properties);

        $model->__setProperties($properties);

        return $model;
    }

    /**
     * Whether to set the original properties array.
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
        $methodName = $this->__getPropertyTypeMethodName($name, 'get');

        if ($this->__doesPropertyTypeMethodExist($methodName)) {
            return $this->$methodName();
        }

        return $this->{$name} ?? null;
    }

    /**
     * @inheritDoc
     */
    public function __set(string $name, mixed $value): void
    {
        $methodName = $this->__getPropertyTypeMethodName($name, 'set');

        $this->__setOriginalProperty($name, $value);

        if ($this->__doesPropertyTypeMethodExist($methodName)) {
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
        $methodName = $this->__getPropertyTypeMethodName($name, 'isset');

        if ($this->__doesPropertyTypeMethodExist($methodName)) {
            return $this->$methodName();
        }

        return isset($this->$name);
    }

    /**
     * Determine whether the model has a property.
     *
     * @param string $property The property
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
        $this->__setProperties($properties);
    }

    /**
     * @inheritDoc
     */
    public function withProperties(array $properties): static
    {
        $model = clone $this;

        $model->__setProperties($properties);

        return $model;
    }

    /**
     * @inheritDoc
     */
    public function asArray(string ...$properties): array
    {
        // Get the public properties
        $allProperties = $this->__allProperties();

        $this->__removeInternalProperties($allProperties);

        $allProperties = $this->__checkOnlyProperties($allProperties, $properties);

        $this->__setPropertyValues($allProperties, '__get');

        return $allProperties;
    }

    /**
     * @inheritDoc
     */
    public function asChangedArray(): array
    {
        return $this->__getChangedProperties($this->asArray());
    }

    /**
     * @inheritDoc
     */
    public function getOriginalPropertyValue(string $name): mixed
    {
        return $this->__originalProperties[$name] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function asOriginalArray(): array
    {
        return $this->__originalProperties;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        $allProperties = $this->__allProperties();

        $this->__removeInternalProperties($allProperties);
        $this->__setPropertyValues($allProperties, '__getJsonPropertyValue');

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
     * Get a new instance.
     *
     * @param array $properties The properties
     *
     * @return $this
     */
    protected static function __getNew(array $properties): static
    {
        return new static();
    }

    /**
     * Set properties from an array of properties.
     *
     * @param array $properties The properties to set
     */
    protected function __setProperties(array $properties): void
    {
        // Iterate through the properties
        foreach ($properties as $property => $value) {
            if ($this->hasProperty($property)) {
                // Set the property
                $this->__set($property, $value);
            }
        }

        $this->__originalPropertiesSet();
    }

    /**
     * Set that original properties have been set.
     */
    protected function __originalPropertiesSet(): void
    {
        $this->__originalPropertiesSet = true;
    }

    /**
     * Get a property's isset method name.
     *
     * @param string $property The property
     * @param string $type     The type (get|set|isset)
     */
    protected function __getPropertyTypeMethodName(string $property, string $type): string
    {
        return self::$cachedValidations[static::class . "$type$property"]
            ??= $type . StrCase::toStudlyCase($property);
    }

    /**
     * Determine if a property type method exists.
     *
     * @param string $methodName The method name
     */
    protected function __doesPropertyTypeMethodExist(string $methodName): bool
    {
        return self::$cachedExistsValidations[static::class . "exists$methodName"]
            ??= method_exists($this, $methodName);
    }

    /**
     * Set an original property.
     *
     * @param string $name  The property name
     * @param mixed  $value The value
     */
    protected function __setOriginalProperty(string $name, mixed $value): void
    {
        if (! $this->__originalPropertiesSet && static::shouldSetOriginalProperties()) {
            $this->__originalProperties[$name] ??= $value;
        }
    }

    /**
     * Get all properties.
     */
    protected function __allProperties(): array
    {
        return get_object_vars($this);
    }

    /**
     * Remove internal model properties from an array of properties.
     *
     * @param array $properties The properties
     */
    protected function __removeInternalProperties(array &$properties): void
    {
        unset($properties['__originalProperties'], $properties['__originalPropertiesSet']);
    }

    /**
     * Check if an array of all properties should be filtered by another list of properties.
     *
     * @param array $properties     The properties
     * @param array $onlyProperties A list of properties to return
     */
    protected function __checkOnlyProperties(array $properties, array $onlyProperties): array
    {
        if (! empty($onlyProperties)) {
            return $this->__onlyProperties($properties, $onlyProperties);
        }

        return $properties;
    }

    /**
     * Get an array subset of properties to return from a given list out of the returnable properties.
     *
     * @param array $allProperties All the properties returnable
     * @param array $properties    The properties we wish to return
     */
    protected function __onlyProperties(array $allProperties, array $properties): array
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
     * @param array $properties The properties to check the original properties against
     */
    protected function __getChangedProperties(array $properties): array
    {
        // The original properties set on the model
        $originalProperties = $this->__originalProperties;
        // The changed properties
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
     * @param array  $properties The properties
     * @param string $method     The method name
     */
    protected function __setPropertyValues(array &$properties, string $method): void
    {
        foreach ($properties as $property => $value) {
            $properties[$property] = $this->$method($property);
        }
    }

    /**
     * Get a property's value for jsonSerialize.
     *
     * @param string $property The property
     */
    protected function __getJsonPropertyValue(string $property): mixed
    {
        return $this->__get($property);
    }
}
