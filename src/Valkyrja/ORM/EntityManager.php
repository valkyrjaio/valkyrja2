<?php

declare(strict_types=1);

/*
 * This file is part of the Valkyrja framework.
 *
 * (c) Melech Mizrachi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valkyrja\ORM;

/**
 * Interface EntityManager.
 *
 * @author Melech Mizrachi
 */
interface EntityManager
{
    /**
     * Get a pdo store by name.
     *
     * @param string|null $connection
     *
     * @return Connection
     */
    public function connection(string $connection = null): Connection;

    /**
     * Get a new query builder instance.
     *
     * @param string|null $entity
     * @param string|null $alias
     *
     * @return QueryBuilder
     */
    public function queryBuilder(string $entity = null, string $alias = null): QueryBuilder;

    /**
     * Start a query.
     *
     * @param string      $query
     * @param string|null $entity
     *
     * @return Query
     */
    public function query(string $query, string $entity = null): Query;

    /**
     * Get a repository instance.
     *
     * @param string $entity
     *
     * @return Repository
     */
    public function repository(string $entity): Repository;

    /**
     * Initiate a transaction.
     *
     * @return bool
     */
    public function beginTransaction(): bool;

    /**
     * In a transaction.
     *
     * @return bool
     */
    public function inTransaction(): bool;

    /**
     * Commit all items in the transaction.
     *
     * @return bool
     */
    public function commit(): bool;

    /**
     * Rollback the previous transaction.
     *
     * @return bool
     */
    public function rollback(): bool;

    /**
     * Get the last inserted id.
     *
     * @return string
     */
    public function lastInsertId(): string;

    /**
     * Find a single entity given its id.
     * <code>
     *      $repository
     *          ->find(
     *              Entity::class,
     *              true | false,
     *              1,
     *              true | false | null
     *          )
     * </code>.
     *
     * @param string     $entity
     * @param bool       $useRepository
     * @param string|int $id
     * @param bool|null  $getRelations
     *
     * @return Entity|null
     */
    public function find(string $entity, bool $useRepository, $id, bool $getRelations = null): ?Entity;

    /**
     * Find one entity by given criteria.
     * <code>
     *      $repository
     *          ->findOneBy(
     *              Entity::class,
     *              true | false,
     *              [
     *                  'column'  => 'value',
     *                  'column2' => 'value2',
     *              ],
     *              [
     *                  'column'
     *                  'column2' => OrderBy::ASC,
     *                  'column3' => OrderBy::DESC,
     *              ],
     *              1,
     *              1
     *          )
     * </code>.
     *
     * @param string     $entity
     * @param bool       $useRepository
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $offset
     * @param array|null $columns
     * @param bool|null  $getRelations
     *
     * @return Entity|null
     */
    public function findBy(
        string $entity,
        bool $useRepository,
        array $criteria,
        array $orderBy = null,
        int $offset = null,
        array $columns = null,
        bool $getRelations = null
    ): ?Entity;

    /**
     * Find entities by given criteria.
     * <code>
     *      $entityManager
     *          ->findBy(
     *              Entity::class,
     *              true | false,
     *              [
     *                  'column'
     *                  'column2' => OrderBy::ASC,
     *                  'column3' => OrderBy::DESC,
     *              ]
     *          )
     * </code>.
     *
     * @param string     $entity
     * @param bool       $useRepository
     * @param array      $orderBy
     * @param array|null $columns
     * @param bool|null  $getRelations
     *
     * @return Entity[]
     */
    public function findAll(
        string $entity,
        bool $useRepository,
        array $orderBy = null,
        array $columns = null,
        bool $getRelations = null
    ): array;

    /**
     * Find entities by given criteria.
     * <code>
     *      $entityManager
     *          ->findBy(
     *              Entity::class,
     *              true | false,
     *              [
     *                  'column'  => 'value',
     *                  'column2' => 'value2',
     *              ],
     *              [
     *                  'column'
     *                  'column2' => OrderBy::ASC,
     *                  'column3' => OrderBy::DESC,
     *              ],
     *              1,
     *              1
     *          )
     * </code>.
     *
     * @param string     $entity
     * @param bool       $useRepository
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     * @param array|null $columns
     * @param bool|null  $getRelations
     *
     * @return Entity[]
     */
    public function findAllBy(
        string $entity,
        bool $useRepository,
        array $criteria,
        array $orderBy = null,
        int $limit = null,
        int $offset = null,
        array $columns = null,
        bool $getRelations = null
    ): array;

    /**
     * Count all the results of given criteria.
     * <code>
     *      $entityManager
     *          ->count(
     *              Entity::class,
     *              true | false,
     *              [
     *                  'column'  => 'value',
     *                  'column2' => 'value2',
     *              ]
     *          )
     * </code>.
     *
     * @param string $entity
     * @param bool   $useRepository
     * @param array  $criteria
     *
     * @return int
     */
    public function count(string $entity, bool $useRepository, array $criteria): int;

    /**
     * Set a model for creation on transaction commit.
     * <code>
     *      $entityManager
     *          ->create(
     *              new Entity(),
     *              true | false
     *          )
     * </code>.
     *
     * @param Entity $entity
     * @param bool   $useRepository
     *
     * @return void
     */
    public function create(Entity $entity, bool $useRepository): void;

    /**
     * Set a model for saving on transaction commit.
     * <code>
     *      $entityManager
     *          ->save(
     *              new Entity(),
     *              true | false
     *          )
     * </code>.
     *
     * @param Entity $entity
     * @param bool   $useRepository
     *
     * @return void
     */
    public function save(Entity $entity, bool $useRepository): void;

    /**
     * Set a model for deletion on transaction commit.
     * <code>
     *      $entityManager
     *          ->delete(
     *              new Entity(),
     *              true | false
     *          )
     * </code>.
     *
     * @param Entity $entity
     * @param bool   $useRepository
     *
     * @return void
     */
    public function delete(Entity $entity, bool $useRepository): void;

    /**
     * Clear a model previously set for creation, save, or deletion.
     * <code>
     *      $entityManager
     *          ->clear(
     *              new Entity()
     *          )
     * </code>.
     *
     * @param Entity|null $entity The entity instance to remove.
     *
     * @return void
     */
    public function clear(Entity $entity = null): void;
}
