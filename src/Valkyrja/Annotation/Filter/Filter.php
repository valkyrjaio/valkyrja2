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

namespace Valkyrja\Annotation\Filter;

use Valkyrja\Annotation\Contract\Annotations;
use Valkyrja\Annotation\Filter\Contract\Filter as Contract;
use Valkyrja\Annotation\Model\Contract\Annotation;

use function in_array;

/**
 * Class Filter.
 *
 * @author Melech Mizrachi
 */
class Filter implements Contract
{
    /**
     * The annotations.
     *
     * @var Annotations
     */
    protected Annotations $annotator;

    /**
     * Filter constructor.
     *
     * @param Annotations $annotator
     */
    public function __construct(Annotations $annotator)
    {
        $this->annotator = $annotator;
    }

    /**
     * @inheritDoc
     */
    public function classAnnotationsByType(string $type, string $class): array
    {
        return $this->filterAnnotationsByType($type, ...$this->annotator->forClass($class));
    }

    /**
     * @inheritDoc
     */
    public function classMembersAnnotationsByType(string $type, string $class): array
    {
        return $this->filterAnnotationsByType($type, ...$this->annotator->forClassMembers($class));
    }

    /**
     * @inheritDoc
     */
    public function classAndMembersAnnotationsByType(string $type, string $class): array
    {
        return $this->filterAnnotationsByType($type, ...$this->annotator->forClassAndMembers($class));
    }

    /**
     * @inheritDoc
     */
    public function propertyAnnotationsByType(string $type, string $class, string $property): array
    {
        return $this->filterAnnotationsByType($type, ...$this->annotator->forClassProperty($class, $property));
    }

    /**
     * @inheritDoc
     */
    public function propertiesAnnotationsByType(string $type, string $class): array
    {
        return $this->filterAnnotationsByType($type, ...$this->annotator->forClassProperties($class));
    }

    /**
     * @inheritDoc
     */
    public function methodAnnotationsByType(string $type, string $class, string $method): array
    {
        return $this->filterAnnotationsByType($type, ...$this->annotator->forClassMethod($class, $method));
    }

    /**
     * @inheritDoc
     */
    public function methodsAnnotationsByType(string $type, string $class): array
    {
        return $this->filterAnnotationsByType($type, ...$this->annotator->forClassMethods($class));
    }

    /**
     * @inheritDoc
     */
    public function functionAnnotationsByType(string $type, string $function): array
    {
        return $this->filterAnnotationsByType($type, ...$this->annotator->forFunction($function));
    }

    /**
     * @inheritDoc
     */
    public function filterAnnotationsByType(string $type, Annotation ...$annotations): array
    {
        return $this->filterAnnotationsByTypes([$type], ...$annotations);
    }

    /**
     * @inheritDoc
     */
    public function filterAnnotationsByTypes(array $types, Annotation ...$annotations): array
    {
        // Set a list of annotations to return
        $annotationsList = [];

        // Iterate through the annotation
        foreach ($annotations as $annotation) {
            // If the annotation's type matches the types requested
            if (in_array($annotation->getType(), $types, true)) {
                // Set the annotation in the list
                $annotationsList[] = $annotation;
            }
        }

        return $annotationsList;
    }
}
