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

namespace Valkyrja\Routing\Matchers;

use Valkyrja\Container\Container;
use Valkyrja\Orm\Entity;
use Valkyrja\Orm\Orm;
use Valkyrja\Orm\RelationshipRepository;
use Valkyrja\Routing\Collection;
use Valkyrja\Routing\Enums\CastType;
use Valkyrja\Routing\Exceptions\InvalidRouteParameter;
use Valkyrja\Routing\Models\Parameter;
use Valkyrja\Routing\Route;
use Valkyrja\Routing\Support\Abort;

/**
 * Class EntityCapableMatcher.
 *
 * @author Melech Mizrachi
 */
class EntityCapableMatcher extends Matcher
{
    /**
     * EntityCapableMatcher constructor.
     *
     * @param Collection $collection The collection
     */
    public function __construct(
        protected Container $container,
        Collection $collection,
    ) {
        parent::__construct($collection);
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidRouteParameter
     */
    protected function getMatchValueForType(Route $route, Parameter $parameter, CastType $castType, int $index, mixed $match): mixed
    {
        // If this is an entity cast type
        if ($castType !== CastType::entity) {
            return parent::getMatchValueForType($route, $parameter, $castType, $index, $match);
        }

        if (! $entityName = $parameter->getEntity()) {
            throw new InvalidRouteParameter("Entity is missing for casted entity parameter {$parameter->getName()}");
        }

        // Try to get the entity
        $entity = $this->getEntity($parameter, $entityName, $match);

        // If no entity is found
        if ($entity === null) {
            // Handle entity not found
            $this->handleEntityNotFound($parameter, $match);
        }

        // Set the entity with the param name as the service id into the container
        $this->container->setSingleton($entityName . $index, $entity);

        return $entity;
    }

    /**
     * Get the entity.
     *
     * @param Parameter            $parameter  The parameter
     * @param class-string<Entity> $entityName The entity class name
     * @param mixed                $match      The match value
     */
    protected function getEntity(Parameter $parameter, string $entityName, mixed $match): ?Entity
    {
        $orm = $this->container->getSingleton(Orm::class)->getRepository($entityName);

        $relationships = $parameter->getEntityRelationships() ?? [];

        // If there is a field specified to use
        if ($field = $parameter->getEntityColumn()) {
            $find = $orm->find()->where($field, null, $match);

            if (is_a($find, RelationshipRepository::class)) {
                $find->withRelationships($relationships);
            }

            return $find->getOneOrNull();
        }

        $find = $orm->findOne($match);

        if (is_a($find, RelationshipRepository::class)) {
            $find->withRelationships($relationships);
        }

        return $find->getOneOrNull();
    }

    /**
     * Handle the entity not being found.
     *
     * @param Parameter $parameter The parameter
     * @param mixed     $match     The match
     */
    protected function handleEntityNotFound(Parameter $parameter, mixed $match): never
    {
        Abort::abort404();
    }
}
