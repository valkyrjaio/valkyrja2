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

namespace Valkyrja\Type\Support;

use Valkyrja\Container\Container;
use Valkyrja\Type\Exceptions\InvalidClassPropertyProvidedException;
use Valkyrja\Type\Exceptions\InvalidClassProvidedException;

use function explode;
use function is_a;

/**
 * Class Cls.
 *
 * @author Melech Mizrachi
 */
class Cls
{
    /**
     * Validate that a class::name inherits from another class::name.
     *
     * @param class-string $object   The object name to check
     * @param class-string $inherits The inherits class name
     *
     * @throws InvalidClassProvidedException
     */
    public static function validateInherits(string $object, string $inherits): void
    {
        if (! static::inherits($object, $inherits)) {
            throw new InvalidClassProvidedException("Expected $inherits got $object");
        }
    }

    /**
     * Check if a class::name inherits from another class::name.
     *
     * @param class-string $object   The object name to check
     * @param class-string $inherits The inherits class name
     */
    public static function inherits(string $object, string $inherits): bool
    {
        return is_a($object, $inherits, true);
    }

    /**
     * Validate that a class::name has a property.
     *
     * @param class-string|string $object   The object name to validate
     * @param string              $property The property name
     *
     * @throws InvalidClassProvidedException
     */
    public static function validateHasProperty(string $object, string $property): void
    {
        if (! static::hasProperty($object, $property)) {
            throw new InvalidClassPropertyProvidedException("$property does not exist in $object");
        }
    }

    /**
     * Check if a class::name has a property.
     *
     * @param class-string|string $object   The object name to validate
     * @param string              $property The property name
     */
    public static function hasProperty(string $object, string $property): bool
    {
        return property_exists($object, $property);
    }

    /**
     * Get a class nice name.
     *
     * @param class-string $name The class object name
     */
    public static function getNiceName(string $name): string
    {
        return Str::replace($name, '\\', '');
    }

    /**
     * Get a class name without namespace.
     *
     * @param class-string $name The class object name
     */
    public static function getName(string $name): string
    {
        $parts = explode('\\', $name);

        return end($parts);
    }

    /**
     * Get a service that can be defaulted whereby the default class exists in the container and has the same
     *  constructor parameters as the service to return.
     *
     * @template T
     * @template D
     *
     * @param Container              $container    The container
     * @param class-string<T>|string $class        The class to get
     * @param class-string<D>|string $defaultClass The default class to fallback to
     * @param array                  $arguments    [optional] The arguments
     *
     * @return T|D
     */
    public static function getDefaultableService(Container $container, string $class, string $defaultClass, array $arguments = []): object
    {
        if ($container->has($class)) {
            return $container->get(
                $class,
                $arguments
            );
        }

        array_unshift($arguments, $class);

        return $container->get(
            $defaultClass,
            $arguments
        );
    }
}
