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

namespace Valkyrja\Annotation\Annotators;

use ReflectionException;
use Valkyrja\Annotation\Annotation;
use Valkyrja\Annotation\Annotator as Contract;
use Valkyrja\Annotation\Constants\Property;
use Valkyrja\Annotation\Parser;
use Valkyrja\Reflection\Reflector;

use function array_merge;

/**
 * Class Annotator.
 *
 * @author Melech Mizrachi
 */
class Annotator implements Contract
{
    /**
     * Cache index constants.
     */
    protected const CLASS_CACHE             = 'class';
    protected const CLASS_MEMBERS_CACHE     = 'class.members';
    protected const CLASS_AND_MEMBERS_CACHE = 'class.and.members';
    protected const PROPERTY_CACHE          = 'property';
    protected const PROPERTIES_CACHE        = 'properties';
    protected const METHOD_CACHE            = 'method';
    protected const METHODS_CACHE           = 'methods';
    protected const FUNCTION_CACHE          = 'function';

    /**
     * Cached reflection classes.
     */
    protected static array $reflections = [];

    /**
     * Cached annotations.
     */
    protected static array $annotations = [];

    /**
     * The parser.
     */
    protected Parser $parser;

    /**
     * The reflector.
     */
    protected Reflector $reflector;

    /**
     * Annotations constructor.
     */
    public function __construct(Parser $parser, Reflector $reflector)
    {
        $this->reflector = $reflector;
        $this->parser    = $parser;
    }

    /**
     * @inheritDoc
     */
    public function getParser(): Parser
    {
        return $this->parser;
    }

    /**
     * @inheritDoc
     */
    public function setParser(Parser $parser): void
    {
        $this->parser = $parser;
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException
     */
    public function classAnnotations(string $class): array
    {
        $index = static::CLASS_CACHE . $class;

        return self::$annotations[$index]
            ??= $this->setAnnotationValues(
                [
                    Property::CLASS_NAME => $class,
                ],
                ...$this->parser->getAnnotations((string) $this->reflector->getClassReflection($class)->getDocComment())
            );
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException
     */
    public function classMembersAnnotations(string $class): array
    {
        $index = static::CLASS_MEMBERS_CACHE . $class;

        return self::$annotations[$index]
            ??= array_merge(
                $this->propertiesAnnotations($class),
                $this->methodsAnnotations($class)
            );
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException
     */
    public function classAndMembersAnnotations(string $class): array
    {
        $index = static::CLASS_AND_MEMBERS_CACHE . $class;

        return self::$annotations[$index]
            ??= array_merge(
                $this->classAnnotations($class),
                $this->classMembersAnnotations($class)
            );
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException
     */
    public function propertyAnnotations(string $class, string $property): array
    {
        $index      = static::PROPERTY_CACHE . $class . $property;
        $reflection = $this->reflector->getPropertyReflection($class, $property);

        return self::$annotations[$index]
            ??= $this->setAnnotationValues(
                [
                    Property::CLASS_NAME => $class,
                    Property::PROPERTY   => $property,
                    Property::STATIC     => $reflection->isStatic(),
                ],
                ...$this->parser->getAnnotations((string) $reflection->getDocComment())
            );
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException
     */
    public function propertiesAnnotations(string $class): array
    {
        $index = static::PROPERTIES_CACHE . $class;

        if (isset(self::$annotations[$index])) {
            return self::$annotations[$index];
        }

        $annotations = [];

        // Get the class's reflection
        // Iterate through the properties
        foreach ($this->reflector->getClassReflection($class)->getProperties() as $property) {
            $index = static::METHOD_CACHE . $class . $property->getName();
            // Set the property's reflection class in the cache
            self::$reflections[$index] = $property;
            // Set the annotations in the list
            $annotations[] = $this->propertyAnnotations($class, $property->getName());
        }

        return self::$annotations[$index] = array_merge(...$annotations);
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException
     */
    public function methodAnnotations(string $class, string $method): array
    {
        $index      = static::METHOD_CACHE . $class . $method;
        $reflection = $this->reflector->getMethodReflection($class, $method);

        return self::$annotations[$index]
            ??= $this->setAnnotationValues(
                [
                    Property::CLASS_NAME => $class,
                    Property::METHOD     => $method,
                    Property::STATIC     => $reflection->isStatic(),
                ],
                ...$this->parser->getAnnotations((string) $reflection->getDocComment())
            );
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException
     */
    public function methodsAnnotations(string $class): array
    {
        $index = static::METHODS_CACHE . $class;

        if (isset(self::$annotations[$index])) {
            return self::$annotations[$index];
        }

        $annotations = [];

        // Get the class's reflection
        // Iterate through the methods
        foreach ($this->reflector->getClassReflection($class)->getMethods() as $method) {
            $index = static::METHOD_CACHE . $class . $method->getName();
            // Set the method's reflection class in the cache
            self::$reflections[$index] = $method;
            // Set the annotations in the list
            $annotations[] = $this->methodAnnotations($class, $method->getName());
        }

        return self::$annotations[$index] = array_merge(...$annotations);
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException
     */
    public function functionAnnotations(string $function): array
    {
        $index = static::FUNCTION_CACHE . $function;

        return self::$annotations[$index]
            ??= $this->setAnnotationValues(
                [
                    Property::FUNCTION => $function,
                ],
                ...$this->parser->getAnnotations(
                    (string) $this->reflector->getFunctionReflection($function)->getDocComment()
                )
            );
    }

    /**
     * Set the base annotation model values.
     *
     * @param array      $properties     The properties
     * @param Annotation ...$annotations The annotations
     *
     * @return Annotation[]
     */
    protected function setAnnotationValues(array $properties, Annotation ...$annotations): array
    {
        foreach ($annotations as $annotation) {
            $annotation->setClass($properties[Property::CLASS_NAME] ?? null);
            $annotation->setProperty($properties[Property::PROPERTY] ?? null);
            $annotation->setMethod($properties[Property::METHOD] ?? null);
            $annotation->setFunction($properties[Property::FUNCTION] ?? null);
            $annotation->setStatic($properties[Property::STATIC] ?? false);
        }

        return $annotations;
    }
}
