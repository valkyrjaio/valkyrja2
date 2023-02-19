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

namespace Valkyrja\Orm;

/**
 * Interface BaseQueryBuilder.
 *
 * @author Melech Mizrachi
 */
interface BaseQueryBuilder
{
    /**
     * Set the table on which to perform the query statement.
     *
     * <code>
     *      $queryBuilder
     *          ->table('table');
     *      $queryBuilder
     *          ->table('table', 't');
     * </code>
     */
    public function table(string $table, string $alias = null): static;

    /**
     * Set the entity to query with.
     *
     * <code>
     *      $queryBuilder->entity(Entity::class);
     * </code>
     *
     * @param class-string<Entity> $entity
     */
    public function entity(string $entity, string $alias = null): static;

    /**
     * Get the built query string.
     */
    public function getQueryString(): string;

    /**
     * Create a new query.
     */
    public function createQuery(): Query;
}
