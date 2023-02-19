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

namespace Valkyrja\Container\Annotators;

use ReflectionException;
use Valkyrja\Annotation\Annotation;
use Valkyrja\Annotation\Filter;
use Valkyrja\Container\Annotator as Contract;
use Valkyrja\Container\Enums\AnnotationName;
use Valkyrja\Reflection\Reflector;

/**
 * Class ContainerAnnotator.
 *
 * @author Melech Mizrachi
 */
class Annotator implements Contract
{
    /**
     * The filter.
     */
    protected Filter $filter;

    /**
     * The reflector.
     */
    protected Reflector $reflector;

    /**
     * ContainerAnnotator constructor.
     */
    public function __construct(Filter $filter, Reflector $reflector)
    {
        $this->filter    = $filter;
        $this->reflector = $reflector;
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException
     */
    public function getServices(string ...$classes): array
    {
        return $this->getAllClassesAnnotationsByType(AnnotationName::SERVICE, ...$classes);
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException
     */
    public function getAliasServices(string ...$classes): array
    {
        return $this->getAllClassesAnnotationsByType(AnnotationName::SERVICE_ALIAS, ...$classes);
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException
     */
    public function getContextServices(string ...$classes): array
    {
        return $this->getAllClassesAnnotationsByType(AnnotationName::SERVICE_CONTEXT, ...$classes);
    }

    /**
     * Get all annotations for a class and its members by type.
     *
     * @param string       $type       The type
     * @param class-string ...$classes The classes
     *
     * @throws ReflectionException
     */
    protected function getAllClassesAnnotationsByType(string $type, string ...$classes): array
    {
        $annotations = [];

        // Iterate through all the classes
        foreach ($classes as $class) {
            // Get all the annotations for each class and iterate through them
            foreach ($this->filter->classAndMembersAnnotationsByType($type, $class) as $annotation) {
                $this->setServiceProperties($annotation);

                // Set the annotation in the annotations list
                $annotations[] = $annotation;
            }
        }

        return $annotations;
    }

    /**
     * Set the properties for a service annotation.
     *
     * @throws ReflectionException
     */
    protected function setServiceProperties(Annotation $annotation): void
    {
        $class    = $annotation->getClass();
        $property = $annotation->getProperty();

        if ($class && $property === null) {
            $reflection = $this->reflector->getMethodReflection(
                $class,
                $annotation->getMethod() ?? '__construct'
            );

            // Set the dependencies
            $annotation->setDependencies($this->reflector->getDependencies($reflection));
        }

        $annotation->setMatches();
    }
}
