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

use BackedEnum;
use JsonException;
use UnitEnum;
use Valkyrja\Model\CastableModel as Contract;
use Valkyrja\Model\Enums\CastType;
use Valkyrja\Model\Model;
use Valkyrja\Type\Support\Arr;
use Valkyrja\Type\Support\Obj;
use Valkyrja\Type\Type;

use function is_array;
use function is_object;
use function is_string;

/**
 * Trait Castable.
 *
 * @author Melech Mizrachi
 */
trait Castable
{
    /**
     * Property castings used for mass property sets to avoid needing individual setters for simple type casting.
     *
     * <code>
     *      [
     *          // An property to be json_decoded to an array
     *          'property_name' => CastType::array,
     *          // An property to be unserialized to an object
     *          'property_name' => CastType::object,
     *          // An property to be json_decoded to an object
     *          'property_name' => CastType::json,
     *          // An property to be cast to an string
     *          'property_name' => CastType::string,
     *          // An property to be cast to an int
     *          'property_name' => CastType::int,
     *          // An property to be cast to an float
     *          'property_name' => CastType::float,
     *          // An property to be cast to an bool
     *          'property_name' => CastType::bool,
     *          // An property to be cast to an enum
     *          'property_name' => [CastType::enum, Enum::class],
     *          // An property to be cast to a model
     *          'property_name' => [CastType::model, Model::class],
     *          // An property to be cast to an array of models
     *          'property_name' => [CastType::model, [Model::class]],
     *          // An property to be cast to a Type
     *          'property_name' => [CastType::type, Type::class],
     *      ]
     * </code>
     *
     * @var array<string, CastType|array{CastType, class-string|class-string[]}>
     */
    protected static array $castings = [];

    /**
     * Allowed classes for serialization of object type properties.
     *
     * <code>
     *      [
     *          // An array of allowed classes for serialization for object types
     *          'property_name' => [ClassName::class],
     *      ]
     * </code>
     *
     * @var array<string, class-string[]>
     */
    protected static array $castingsAllowedClasses = [];

    /**
     * @inheritDoc
     *
     * @return array<string, CastType|array{CastType, class-string|class-string[]}>
     */
    public static function getCastings(): array
    {
        return static::$castings;
    }

    /**
     * @inheritDoc
     *
     * @return array<string, class-string[]>
     */
    public static function getCastingsAllowedClasses(): array
    {
        return static::$castingsAllowedClasses;
    }

    /**
     * Set properties from an array of properties.
     *
     * @param array $properties The properties to set
     *
     * @throws JsonException
     */
    protected function __setProperties(array $properties): void
    {
        $castings    = static::getCastings();
        $hasCastings = self::$cachedExistsValidations[static::class . 'hasCastings'] ??= ! empty($castings);

        // Iterate through the properties
        foreach ($properties as $property => $value) {
            if ($this->hasProperty($property)) {
                // Set the property
                $this->__set(
                    $property,
                    $hasCastings
                        ? $this->__getPropertyValueByType($castings, $property, $value)
                        : $value
                );
            }
        }
    }

    /**
     * Get a property's value by the type (if type is set).
     *
     * @param array  $castings The castings
     * @param string $property The property name
     * @param mixed  $value    The property value
     *
     * @throws JsonException
     */
    protected function __getPropertyValueByType(array $castings, string $property, mixed $value): mixed
    {
        // If there is no type specified or the value is null just return the value
        // Castings assignment is set in the if specifically to avoid an assignment
        // if the value is null, which would be an unneeded assigned variable
        if ($value === null || ($type = $castings[$property] ?? null) === null) {
            return $value;
        }

        return $this->__getPropertyValueByTypeMatch($type, $property, $value);
    }

    /**
     * Get a property's value by the type for a given type cast.
     *
     * @param CastType|array $type     The cast type
     * @param string         $property The property name
     * @param mixed          $value    The property value
     *
     * @throws JsonException
     */
    protected function __getPropertyValueByTypeMatch(CastType|array $type, string $property, mixed $value): mixed
    {
        return match ($this->__getTypeToCheck($type)) {
            CastType::string => $this->__getStringFromValueType($property, $value),
            CastType::int    => $this->__getIntFromValueType($property, $value),
            CastType::float  => $this->__getFloatFromValueType($property, $value),
            CastType::double => $this->__getDoubleFromValueType($property, $value),
            CastType::bool   => $this->__getBoolFromValueType($property, $value),
            CastType::model  => $this->__getModelFromValueType($property, $type[1], $value),
            CastType::enum   => $this->__getEnumFromValueType($property, $type[1], $value),
            CastType::type   => $this->__getTypeFromValueType($property, $type[1], $value),
            CastType::json   => $this->__getJsonFromValueType($property, $value),
            CastType::array  => $this->__getArrayFromValueType($property, $value),
            CastType::object => $this->__getObjectFromValueType($property, $value),
            CastType::true   => $this->__getTrueFromValueType($property, $value),
            CastType::false  => $this->__getFalseFromValueType($property, $value),
            CastType::null   => $this->__getNullFromValueType($property, $value),
        };
    }

    /**
     * Get the type to check. Could be an array for models or enums since the second index will be the enum/model name.
     *
     * @param CastType|array $type The type
     */
    protected function __getTypeToCheck(CastType|array $type): CastType
    {
        return $type instanceof CastType
            ? $type
            : $type[0];
    }

    /**
     * Get the value for a string type cast.
     *
     * @param string $property The property name
     * @param mixed  $value    The value
     */
    protected function __getStringFromValueType(string $property, mixed $value): string
    {
        return (string) $value;
    }

    /**
     * Get the value for a int type cast.
     *
     * @param string $property The property name
     * @param mixed  $value    The value
     */
    protected function __getIntFromValueType(string $property, mixed $value): int
    {
        return (int) $value;
    }

    /**
     * Get the value for a float type cast.
     *
     * @param string $property The property name
     * @param mixed  $value    The value
     */
    protected function __getFloatFromValueType(string $property, mixed $value): float
    {
        return (float) $value;
    }

    /**
     * Get the value for a double type cast.
     *
     * @param string $property The property name
     * @param mixed  $value    The value
     */
    protected function __getDoubleFromValueType(string $property, mixed $value): float
    {
        return (float) $value;
    }

    /**
     * Get the value for a bool type cast.
     *
     * @param string $property The property name
     * @param mixed  $value    The value
     */
    protected function __getBoolFromValueType(string $property, mixed $value): bool
    {
        return (bool) $value;
    }

    /**
     * Get the value for a true type cast.
     *
     * @param string $property The property name
     * @param mixed  $value    The value
     *
     * @return true
     */
    protected function __getTrueFromValueType(string $property, mixed $value): bool
    {
        return true;
    }

    /**
     * Get the value for a false type cast.
     *
     * @param string $property The property name
     * @param mixed  $value    The value
     *
     * @return false
     */
    protected function __getFalseFromValueType(string $property, mixed $value): bool
    {
        return false;
    }

    /**
     * Get the value for a null type cast.
     *
     * @param string $property The property name
     * @param mixed  $value    The value
     *
     * @return null
     */
    protected function __getNullFromValueType(string $property, mixed $value): mixed
    {
        return null;
    }

    /**
     * Get the value for a json type cast.
     *
     * @param string $property The property name
     * @param mixed  $value    The value
     *
     * @throws JsonException
     */
    protected function __getJsonFromValueType(string $property, mixed $value): object
    {
        return is_string($value)
            ? Obj::fromString($value)
            : (object) $value;
    }

    /**
     * Get the value for a array type cast.
     *
     * @param string $property The property name
     * @param mixed  $value    The value
     *
     * @throws JsonException
     */
    protected function __getArrayFromValueType(string $property, mixed $value): array
    {
        return is_string($value)
            ? Arr::fromString($value)
            : (array) $value;
    }

    /**
     * Get the value for a object type cast.
     *
     * @param string $property The property name
     * @param mixed  $value    The value
     */
    protected function __getObjectFromValueType(string $property, mixed $value): object
    {
        return is_string($value)
            ? unserialize(
                $value,
                [
                    'allowed_classes' => static::getCastingsAllowedClasses()[$property] ?? false,
                ]
            )
            : (object) $value;
    }

    /**
     * Get the value for a Type type cast.
     *
     * @param string             $property The property name
     * @param class-string<Type> $type     The type of the property
     * @param mixed              $value    The value
     */
    protected function __getTypeFromValueType(string $property, string $type, mixed $value): Type|null
    {
        return $value !== null && ! ($value instanceof Type)
            ? new $type($value)
            : $value;
    }

    /**
     * Get a model from value given a type not identified prior.
     *
     * @param string       $property The property name
     * @param array|string $type     The type of the property
     * @param mixed        $value    The value
     *
     * @throws JsonException
     *
     * @return Model|Model[]
     */
    protected function __getModelFromValueType(string $property, array|string $type, mixed $value): Model|array
    {
        // An array would indicate an array of models
        if (is_array($type)) {
            $type = $type[0];

            return array_map(
                function (array $data) use ($property, $type) {
                    return $this->__getModelFromValue($property, $type, $data);
                },
                $value
            );
        }

        return $this->__getModelFromValue($property, $type, $value);
    }

    /**
     * Get a model from value.
     *
     * @param string              $property The property name
     * @param class-string<Model> $type     The type of the property
     * @param mixed               $value    The value
     *
     * @throws JsonException
     */
    protected function __getModelFromValue(string $property, string $type, mixed $value): Model
    {
        if (is_string($value)) {
            $value = Arr::fromString($value);
        } elseif ($value instanceof Contract) {
            $value = $value->jsonSerialize();
        } elseif (is_object($value) || is_array($value)) {
            $value = (array) $value;
        }

        if (isset($this->$property) && $this->$property instanceof Contract) {
            $value = array_merge($this->$property->asArray(), $value);
        }

        /** @var Model $type */
        return $type::fromArray($value);
    }

    /**
     * Get an enum from value given a type not identified prior.
     *
     * @param string       $property The property name
     * @param array|string $type     The type of the property
     * @param mixed        $value    The value
     *
     * @return UnitEnum|UnitEnum[]
     */
    protected function __getEnumFromValueType(string $property, array|string $type, mixed $value): UnitEnum|array
    {
        // An array would indicate an array of enums
        if (is_array($type)) {
            $type = $type[0];

            return array_map(
                function (array $data) use ($property, $type) {
                    return $this->__getEnumFromValue($property, $type, $data);
                },
                $value
            );
        }

        return $this->__getEnumFromValue($property, $type, $value);
    }

    /**
     * Get an enum from value.
     *
     * @param string $property The property name
     * @param string $type     The type of the property
     * @param mixed  $value    The value
     */
    protected function __getEnumFromValue(string $property, string $type, mixed $value): UnitEnum
    {
        // If it's already an enum just send it along the way
        if ($value instanceof BackedEnum) {
            return $value;
        }

        /** @var BackedEnum $type */
        return $type::from($value);
    }
}
