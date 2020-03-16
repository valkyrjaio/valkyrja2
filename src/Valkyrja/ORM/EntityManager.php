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
     * Get an adapter.
     *
     * @param string|null $name
     *
     * @return Adapter
     */
    public function getAdapter(string $name = null): Adapter;

    /**
     * Get a connection.
     *
     * @param string|null $connection
     *
     * @return Connection
     */
    public function getConnection(string $connection = null): Connection;

    /**
     * Create a new query builder instance.
     *
     * @param string|null $entity
     * @param string|null $alias
     *
     * @return QueryBuilder
     */
    public function createQueryBuilder(string $entity = null, string $alias = null): QueryBuilder;

    /**
     * Create a new query instance.
     *
     * @param string|null $query
     * @param string|null $entity
     *
     * @return Query
     */
    public function createQuery(string $query = null, string $entity = null): Query;

    /**
     * Create a new retriever instance.
     *
     * @return Retriever
     */
    public function createRetriever(): Retriever;

    /**
     * Get the persister.
     *
     * @return Persister
     */
    public function getPersister(): Persister;

    /**
     * Get a repository by entity name.
     *
     * @param string $entity
     *
     * @return Repository
     */
    public function getRepository(string $entity): Repository;

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
     * Ensure a transaction is in progress.
     *
     * @return void
     */
    public function ensureTransaction(): void;

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
     * Find by given criteria.
     * <code>
     *      $entityManager
     *          ->findBy(
     *              Entity::class,
     *              1
     *          )
     * </code>.
     *
     * @param string    $entity
     * @param bool|null $getRelations
     *
     * @return Retriever
     */
    public function find(string $entity, bool $getRelations = false): Retriever;

    /**
     * Find a single entity given its id.
     * <code>
     *      $entityManager
     *          ->find(
     *              Entity::class,
     *              1,
     *              true | false | null
     *          )
     * </code>.
     *
     * @param string     $entity
     * @param string|int $id
     * @param bool|null  $getRelations
     *
     * @return Retriever
     */
    public function findOne(string $entity, $id, bool $getRelations = false): Retriever;

    /**
     * Count all the results of given criteria.
     * <code>
     *      $entityManager
     *          ->count(
     *              Entity::class
     *          )
     * </code>.
     *
     * @param string $entity
     *
     * @return Retriever
     */
    public function count(string $entity): Retriever;

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
     * @param bool   $defer [optional]
     *
     * @return void
     */
    public function create(Entity $entity, bool $defer = true): void;

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
     * @param bool   $defer [optional]
     *
     * @return void
     */
    public function save(Entity $entity, bool $defer = true): void;

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
     * @param bool   $defer [optional]
     *
     * @return void
     */
    public function delete(Entity $entity, bool $defer = true): void;

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
