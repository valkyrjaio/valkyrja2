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
 * Interface ORM.
 *
 * @author Melech Mizrachi
 */
interface Orm
{
    /**
     * Use a connection by name.
     *
     * @param string|null                $name    The connection name
     * @param class-string<Adapter>|null $adapter The adapter
     */
    public function useConnection(string $name = null, string $adapter = null): Driver;

    /**
     * Create an adapter.
     *
     * @param class-string<Adapter> $name   The adapter class name
     * @param array                 $config The config
     */
    public function createAdapter(string $name, array $config): Adapter;

    /**
     * Create a query builder.
     *
     * @param Adapter                    $adapter The adapter
     * @param class-string<QueryBuilder> $name    The query builder class name
     */
    public function createQueryBuilder(Adapter $adapter, string $name): QueryBuilder;

    /**
     * Create a query.
     *
     * @param Adapter             $adapter The adapter
     * @param class-string<Query> $name    The query class name
     */
    public function createQuery(Adapter $adapter, string $name): Query;

    /**
     * Create a retriever.
     *
     * @param Adapter                 $adapter The adapter
     * @param class-string<Retriever> $name    The retriever class name
     */
    public function createRetriever(Adapter $adapter, string $name): Retriever;

    /**
     * Create a persister.
     *
     * @param Adapter                 $adapter The adapter
     * @param class-string<Persister> $name    The persister class name
     */
    public function createPersister(Adapter $adapter, string $name): Persister;

    /**
     * Get a repository by entity name.
     *
     * @param class-string<Entity> $entity
     *
     * @return Repository<Entity>
     */
    public function getRepository(string $entity): Repository;

    /**
     * Get a repository from an entity class.
     *
     * @return Repository<Entity>
     */
    public function getRepositoryFromClass(Entity $entity): Repository;

    /**
     * Create a statement.
     *
     * @param Adapter                 $adapter The adapter
     * @param class-string<Statement> $name    The statement class name
     * @param array                   $data    [optional] Additional data required for the statement
     */
    public function createStatement(Adapter $adapter, string $name, array $data = []): Statement;

    /**
     * Create a migration.
     *
     * @param class-string<Migration> $name The migration class name
     * @param array                   $data [optional] Additional data required for the migration
     */
    public function createMigration(string $name, array $data = []): Migration;

    /**
     * Initiate a transaction.
     */
    public function beginTransaction(): bool;

    /**
     * In a transaction.
     */
    public function inTransaction(): bool;

    /**
     * Ensure a transaction is in progress.
     */
    public function ensureTransaction(): void;

    /**
     * Persist all entities.
     */
    public function persist(): bool;

    /**
     * Rollback the previous transaction.
     */
    public function rollback(): bool;

    /**
     * Get the last inserted id.
     *
     * @param string|null $table   [optional] The table last inserted into
     * @param string|null $idField [optional] The id field of the table last inserted into
     */
    public function lastInsertId(string $table = null, string $idField = null): string;

    /**
     * Find by given criteria.
     *
     * <code>
     *      $entityManager->find(Entity::class, true | false)
     * </code>
     *
     * @param class-string<Entity> $entity
     */
    public function find(string $entity): Retriever;

    /**
     * Find a single entity given its id.
     *
     * <code>
     *      $entityManager->findOne(Entity::class, 1, true | false)
     * </code>
     *
     * @param class-string<Entity> $entity
     */
    public function findOne(string $entity, int|string $id): Retriever;

    /**
     * Count all the results of given criteria.
     *
     * <code>
     *      $entityManager
     *          ->count(
     *              Entity::class
     *          )
     * </code>
     *
     * @param class-string<Entity> $entity
     */
    public function count(string $entity): Retriever;

    /**
     * Create a new entity.
     *
     * <code>
     *      $entityManager->create(new Entity(), true | false)
     * </code>
     *
     * @param bool $defer [optional]
     */
    public function create(Entity $entity, bool $defer = true): void;

    /**
     * Update an existing entity.
     *
     * <code>
     *      $entityManager->save(new Entity(), true | false)
     * </code>
     *
     * @param bool $defer [optional]
     */
    public function save(Entity $entity, bool $defer = true): void;

    /**
     * Delete an existing entity.
     *
     * <code>
     *      $entityManager->delete(new Entity(), true | false)
     * </code>
     *
     * @param bool $defer [optional]
     */
    public function delete(Entity $entity, bool $defer = true): void;

    /**
     * Soft delete an existing entity.
     *
     * <code>
     *      $entityManager->softDelete(new SoftDeleteEntity(), true | false)
     * </code>
     *
     * @param bool $defer [optional]
     */
    public function softDelete(SoftDeleteEntity $entity, bool $defer = true): void;

    /**
     * Clear all, or a single, deferred entity.
     *
     * <code>
     *      $entityManager->clear(new Entity())
     * </code>
     *
     * @param Entity|null $entity [optional] The entity instance to remove.
     */
    public function clear(Entity $entity = null): void;
}
