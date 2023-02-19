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

namespace Valkyrja\Attribute\Managers;

use Closure;
use ReflectionAttribute;
use ReflectionClassConstant;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use Valkyrja\Attribute\Attributes as Contract;
use Valkyrja\Dispatcher\Constants\Property;
use Valkyrja\Reflection\Reflector;

/**
 * Class Attributes.
 *
 * @author Melech Mizrachi
 */
class Attributes implements Contract
{
    /**
     * Default flags for the getAttributes() method.
     */
    protected static int $defaultFlags = ReflectionAttribute::IS_INSTANCEOF;

    /**
     * Attributes constructor.
     *
     * @param Reflector $reflector [optional] The reflector service
     */
    public function __construct(
        protected Reflector $reflector = new \Valkyrja\Reflection\Reflectors\Reflector(),
    ) {
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException
     */
    public function forClass(string $class, string $attribute = null, int $flags = null): array
    {
        return $this->getInstances(
            [
                Property::CLASS_NAME => $class,
            ],
            ...$this->reflector->getClassReflection($class)->getAttributes($attribute, $flags ?? static::$defaultFlags)
        );
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException
     */
    public function forClassMembers(string $class, string $attribute = null, int $flags = null): array
    {
        return [
            ...$this->forConstants($class, $attribute, $flags),
            ...$this->forProperties($class, $attribute, $flags),
            ...$this->forMethods($class, $attribute, $flags),
        ];
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException
     */
    public function forClassAndMembers(string $class, string $attribute = null, int $flags = null): array
    {
        return [
            ...$this->forClass($class, $attribute, $flags),
            ...$this->forClassMembers($class, $attribute, $flags),
        ];
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException
     */
    public function forConstant(string $class, string $constant, string $attribute = null, int $flags = null): array
    {
        return $this->forClassMember($attribute, $flags, $this->reflector->getClassConstReflection($class, $constant));
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException
     */
    public function forConstants(string $class, string $attribute = null, int $flags = null): array
    {
        return $this->forClassMember(
            $attribute,
            $flags,
            ...$this->reflector->getClassReflection($class)->getReflectionConstants()
        );
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException
     */
    public function forProperty(string $class, string $property, string $attribute = null, int $flags = null): array
    {
        return $this->forClassMember($attribute, $flags, $this->reflector->getPropertyReflection($class, $property));
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException
     */
    public function forProperties(string $class, string $attribute = null, int $flags = null): array
    {
        return $this->forClassMember(
            $attribute,
            $flags,
            ...$this->reflector->getClassReflection($class)->getProperties()
        );
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException
     */
    public function forMethod(string $class, string $method, string $attribute = null, int $flags = null): array
    {
        return $this->forClassMember($attribute, $flags, $this->reflector->getMethodReflection($class, $method));
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException
     */
    public function forMethods(string $class, string $attribute = null, int $flags = null): array
    {
        return $this->forClassMember(
            $attribute,
            $flags,
            ...$this->reflector->getClassReflection($class)->getMethods()
        );
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException
     */
    public function forFunction(string $function, string $attribute = null, int $flags = null): array
    {
        return $this->getInstances(
            [
                Property::FUNCTION => $function,
            ],
            ...$this->reflector->getFunctionReflection($function)
                   ->getAttributes($attribute, $flags ?? static::$defaultFlags)
        );
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException
     */
    public function forClosure(Closure $closure, string $attribute = null, int $flags = null): array
    {
        return $this->getInstances(
            [
                Property::CLOSURE => $closure,
            ],
            ...$this->reflector->getClosureReflection($closure)
                   ->getAttributes($attribute, $flags ?? static::$defaultFlags)
        );
    }

    /**
     * Get a class' members' attributes.
     *
     * @param class-string|null                                           $attribute  [optional] The attribute to return
     * @param ReflectionClassConstant|ReflectionMethod|ReflectionProperty ...$members [optional] The members
     *
     * @return object[]
     */
    protected function forClassMember(
        string $attribute = null,
        int $flags = null,
        ReflectionClassConstant|ReflectionMethod|ReflectionProperty ...$members
    ): array {
        $instances = [];

        foreach ($members as $member) {
            $instances = [
                ...$instances,
                ...$this->getInstances(
                    [
                        Property::CLASS_NAME => $member->getDeclaringClass()->getName(),
                        Property::CONSTANT   => $member instanceof ReflectionClassConstant
                            ? $member->getName()
                            : null,
                        Property::PROPERTY   => $member instanceof ReflectionProperty
                            ? $member->getName()
                            : null,
                        Property::METHOD     => $member instanceof ReflectionMethod
                            ? $member->getName()
                            : null,
                        Property::STATIC     => method_exists($member, 'isStatic')
                            ? $member->isStatic()
                            : null,
                    ],
                    ...$member->getAttributes($attribute, $flags ?? static::$defaultFlags)
                ),
            ];
        }

        return $instances;
    }

    /**
     * Set the base annotation model values.
     *
     * @param array               $properties              The properties
     * @param ReflectionAttribute ...$reflectionAttributes The reflection attributes
     *
     * @return object[]
     */
    protected function getInstances(array $properties, ReflectionAttribute ...$reflectionAttributes): array
    {
        $instances = [];

        foreach ($reflectionAttributes as $reflectionAttribute) {
            $instances[] = $instance = $reflectionAttribute->newInstance();

            if (isset($properties[Property::CLASS_NAME]) && method_exists($instance, 'setClass')) {
                $instance->setClass($properties[Property::CLASS_NAME]);
            }
            if (isset($properties[Property::CONSTANT]) && method_exists($instance, 'setConstant')) {
                $instance->setConstant($properties[Property::CONSTANT]);
            }
            if (isset($properties[Property::PROPERTY]) && method_exists($instance, 'setProperty')) {
                $instance->setProperty($properties[Property::PROPERTY]);
            }
            if (isset($properties[Property::METHOD]) && method_exists($instance, 'setMethod')) {
                $instance->setMethod($properties[Property::METHOD]);
            }
            if (isset($properties[Property::STATIC]) && method_exists($instance, 'setStatic')) {
                $instance->setStatic($properties[Property::STATIC]);
            }
        }

        return $instances;
    }
}
