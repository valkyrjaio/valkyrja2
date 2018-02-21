<?php

/*
 * This file is part of the Valkyrja framework.
 *
 * (c) Melech Mizrachi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valkyrja\Model;

use JsonSerializable;

/**
 * Class Model.
 *
 * @author Melech Mizrachi
 */
abstract class Model implements JsonSerializable
{
    /**
     * @var array
     */
    protected static $attributes = [];

    /**
     * @var array
     */
    protected static $attributeTypes = [];

    /**
     * Get a property.
     *
     * @param string $name The property to get
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        $methodName = str_replace('_', '', ucwords($name, '_'));
        $methodName = 'get' . $methodName;

        if (method_exists($this, $methodName)) {
            return $this->$methodName();
        }

        return $this->{$name};
    }

    /**
     * Set a property.
     *
     * @param string $name  The property to set
     * @param mixed  $value The value to set
     *
     * @return \Valkyrja\Model\Model
     */
    public function __set(string $name, $value): self
    {
        $methodName = str_replace('_', '', ucwords($name, '_'));
        $methodName = 'set' . $methodName;

        if (method_exists($this, $methodName)) {
            return $this->$methodName($value);
        }

        $this->{$name} = $value;

        return $this;
    }

    /**
     * Check if a property is set.
     *
     * @param string $name The property to check
     *
     * @return bool
     */
    public function __isset(string $name): bool
    {
        $methodName = str_replace('_', '', ucwords($name, '_'));
        $methodName = 'isset' . $methodName;

        if (method_exists($this, $methodName)) {
            return $this->$methodName();
        }

        return property_exists($this, $name);
    }

    /**
     * Set properties from an array of properties.
     *
     * @param array $properties
     *
     * @return void
     */
    public function fromArray(array $properties): void
    {
        // Iterate through the public/protected vars of this model
        foreach (get_object_vars($this) as $attrName => $attrValue) {
            // Get the attribute from the properties
            $property = $properties[$attrName] ?? null;

            // If no property exists
            if (null === $property) {
                // Continue onward
                continue;
            }

            // Check if a type was set for this attribute
            $type = static::$attributeTypes[$attrName] ?? null;

            // If the type is object and the property isn't already an object
            if ($type === 'object' && ! \is_object($property)) {
                // Unserialize the object
                $property = unserialize($property, true);
            } // If the type is array and the property isn't already an array
            elseif ($type === 'array' && ! \is_array($property)) {
                $property = json_decode($property);
            }

            $this->__set($attrName, $property);
        }
    }

    /**
     * Serialize properties for json_encode.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
