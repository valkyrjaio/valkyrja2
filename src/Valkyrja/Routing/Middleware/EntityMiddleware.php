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

namespace Valkyrja\Routing\Middleware;

use Valkyrja\Http\Request;
use Valkyrja\Http\Response;
use Valkyrja\Orm\Entity;
use Valkyrja\Orm\Orm;
use Valkyrja\Orm\RelationshipRepository;
use Valkyrja\Orm\Repository;
use Valkyrja\Routing\Models\Parameter;
use Valkyrja\Routing\Route;
use Valkyrja\Routing\Support\Abort;

/**
 * Class EntityMiddleware.
 *
 * @author Melech Mizrachi
 */
class EntityMiddleware extends Middleware
{
    /**
     * The orm.
     */
    protected static Orm $orm;

    /**
     * @inheritDoc
     */
    public static function before(Request $request): Request|Response
    {
        if (($route = self::$route ?? null) && $matches = $route->getMatches()) {
            static::checkParamsForEntities($route, $matches);
        }

        return $request;
    }

    /**
     * Get the ORM repository for a given entity.
     *
     * @param class-string<Entity> $entity The entity class name
     */
    protected static function getOrmRepository(string $entity): Repository
    {
        return static::getOrm()->getRepository($entity);
    }

    /**
     * Get the ORM service.
     */
    protected static function getOrm(): Orm
    {
        return self::$orm ?? self::getContainer()->getSingleton(Orm::class);
    }

    /**
     * Check route params for entities.
     *
     * @param Route $route   The route
     * @param array $matches The matches
     */
    protected static function checkParamsForEntities(Route $route, array $matches): void
    {
        $parameters = $route->getParameters();

        if (! empty($parameters)) {
            $dependencies = $route->getDependencies() ?? [];

            // Iterate through the params
            foreach ($parameters as $index => $parameter) {
                static::checkParameterForEntity($index, $parameter, $dependencies, $matches);
            }

            $route->setMatches($matches);
            $route->setDependencies($dependencies);
        }
    }

    /**
     * Check a route's parameters for an entity.
     *
     * @param int       $index        The index
     * @param Parameter $parameter    The parameter
     * @param array     $dependencies The route dependencies
     * @param array     $matches      The matches
     */
    protected static function checkParameterForEntity(int $index, Parameter $parameter, array &$dependencies, array &$matches): void
    {
        if (($entityName = $parameter->getEntity()) === null) {
            return;
        }

        static::findAndSetEntityFromParameter($index, $parameter, $entityName, $dependencies, $matches[$index]);
    }

    /**
     * Try to find and set a route's entity dependency.
     *
     * @param int                  $index        The index
     * @param Parameter            $parameter    The parameter
     * @param class-string<Entity> $entityName   The entity class name
     * @param array                $dependencies The dependencies
     * @param mixed                $value        The value
     */
    protected static function findAndSetEntityFromParameter(int $index, Parameter $parameter, string $entityName, array &$dependencies, mixed &$value): void
    {
        // Attempt to get the entity from the ORM repository
        $entity = static::findEntityFromParameter($parameter, $entityName, $value);

        if (! $entity) {
            static::entityNotFound($entityName, $value);
        }

        // Set the entity with the param name as the service id into the container
        self::getContainer()->setSingleton($entityName . $index, $entity);

        // Replace the route match with this entity
        $value = $entity;

        $updatedDependencies = [];

        foreach ($dependencies as $dependency) {
            if ($dependency !== $entityName) {
                $updatedDependencies[] = $dependency;
            }
        }

        $dependencies = $updatedDependencies;
    }

    /**
     * Try to find a route's entity dependency.
     *
     * @param Parameter            $parameter  The parameter
     * @param class-string<Entity> $entityName The entity class name
     * @param mixed                $value      The value
     */
    protected static function findEntityFromParameter(Parameter $parameter, string $entityName, mixed $value): ?Entity
    {
        $orm           = static::getOrmRepository($entityName);
        $relationships = $parameter->getEntityRelationships() ?? [];

        // If there is a field specified to use
        if ($field = $parameter->getEntityColumn()) {
            $find = $orm->find()->where($field, null, $value);

            if (is_a($find, RelationshipRepository::class)) {
                $find->withRelationships($relationships);
            }

            return $find->getOneOrNull();
        }

        $find = $orm->findOne($value);

        if (is_a($find, RelationshipRepository::class)) {
            $find->withRelationships($relationships);
        }

        return $find->getOneOrNull();
    }

    /**
     * Do when an entity was not found with the given value.
     *
     * @param string $entity The entity not found
     * @param mixed  $value  [optional] The value used to check for the entity
     */
    protected static function entityNotFound(string $entity, mixed $value): void
    {
        Abort::abort404();
    }
}
