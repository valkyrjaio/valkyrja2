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

use Valkyrja\Orm\Exceptions\EntityNotFoundException;

/**
 * Interface Retriever.
 *
 * @author   Melech Mizrachi
 *
 * @template Entity
 */
interface Retriever extends WhereQueryBuilder
{
    /**
     * Find by given criteria.
     *
     * <code>
     *      $retriever->bind(Entity::class, true | false)
     * </code>
     *
     * @param class-string<Entity> $entity
     */
    public function find(string $entity): static;

    /**
     * Find a single entity given its id.
     *
     * <code>
     *      $retriever->findOne(Entity::class, 1, true | false)
     * </code>
     *
     * @param class-string<Entity> $entity
     */
    public function findOne(string $entity, int|string $id): static;

    /**
     * Count all the results of given criteria.
     *
     * <code>
     *      $retriever->count(Entity::class)
     * </code>
     *
     * @param class-string<Entity> $entity
     */
    public function count(string $entity): static;

    /**
     * Set columns.
     */
    public function columns(array $columns): static;

    /**
     * Join with another table.
     *
     * @param string      $table    The table to join on
     * @param string      $column1  The column to join on
     * @param string      $column2  The secondary column to join on
     * @param string|null $operator [optional] The operator
     * @param string|null $type     [optional] The type of join
     * @param bool|null   $isWhere  [optional] Whether this is a where join
     */
    public function join(
        string $table,
        string $column1,
        string $column2,
        string $operator = null,
        string $type = null,
        bool $isWhere = null
    ): static;

    /**
     * Set group by.
     */
    public function groupBy(string $column): static;

    /**
     * Set an order by.
     */
    public function orderBy(string $column, string $type = null): static;

    /**
     * Set limit.
     */
    public function limit(int $limit): static;

    /**
     * Set offset.
     */
    public function offset(int $offset): static;

    /**
     * Get results.
     *
     * @return Entity[]
     */
    public function getResult(): array;

    /**
     * Get one or null.
     */
    public function getOneOrNull(): ?Entity;

    /**
     * Get one or fail.
     *
     * @throws EntityNotFoundException
     */
    public function getOneOrFail(): Entity;

    /**
     * Get count results.
     */
    public function getCount(): int;

    /**
     * Get the query builder.
     */
    public function getQueryBuilder(): QueryBuilder;

    /**
     * Get the query.
     */
    public function getQuery(): Query;
}
