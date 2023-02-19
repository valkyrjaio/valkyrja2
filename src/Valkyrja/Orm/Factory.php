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
 * Interface Factory.
 *
 * @author Melech Mizrachi
 */
interface Factory
{
    /**
     * Create an adapter.
     *
     * @param class-string<Adapter> $name   The adapter class name
     * @param array                 $config The config
     */
    public function createAdapter(string $name, array $config): Adapter;

    /**
     * Create a driver.
     *
     * @param Adapter              $adapter The adapter
     * @param class-string<Driver> $name    The driver class name
     * @param array                $config  The config
     */
    public function createDriver(Adapter $adapter, string $name, array $config): Driver;

    /**
     * Create a repository.
     *
     * @param Driver                   $driver The driver
     * @param class-string<Repository> $name   The repository class name
     * @param class-string<Entity>     $entity The entity class name
     *
     * @return Repository<Entity>
     */
    public function createRepository(Driver $driver, string $name, string $entity): Repository;

    /**
     * Create a query builder.
     *
     * @param Adapter                    $adapter The adapter
     * @param class-string<QueryBuilder> $name    The query builder class name
     */
    public function createQueryBuilder(Adapter $adapter, string $name): QueryBuilder;

    /**
     * Create a delete query builder.
     *
     * @param Adapter                          $adapter The adapter
     * @param class-string<DeleteQueryBuilder> $name    The delete query builder class name
     */
    public function createDeleteQueryBuilder(Adapter $adapter, string $name): DeleteQueryBuilder;

    /**
     * Create a insert query builder.
     *
     * @param Adapter                          $adapter The adapter
     * @param class-string<InsertQueryBuilder> $name    The insert query builder class name
     */
    public function createInsertQueryBuilder(Adapter $adapter, string $name): InsertQueryBuilder;

    /**
     * Create a select query builder.
     *
     * @param Adapter                          $adapter The adapter
     * @param class-string<SelectQueryBuilder> $name    The select query builder class name
     */
    public function createSelectQueryBuilder(Adapter $adapter, string $name): SelectQueryBuilder;

    /**
     * Create a update query builder.
     *
     * @param Adapter                          $adapter The adapter
     * @param class-string<UpdateQueryBuilder> $name    The update query builder class name
     */
    public function createUpdateQueryBuilder(Adapter $adapter, string $name): UpdateQueryBuilder;

    /**
     * Create a query.
     *
     * @param Adapter             $adapter The adapter
     * @param class-string<Query> $name    The query class name
     */
    public function createQuery(Adapter $adapter, string $name): Query;

    /**
     * Create a persister.
     *
     * @param Adapter                 $adapter The adapter
     * @param class-string<Persister> $name    The persister class name
     */
    public function createPersister(Adapter $adapter, string $name): Persister;

    /**
     * Create a retriever.
     *
     * @param Adapter                 $adapter The adapter
     * @param class-string<Retriever> $name    The retriever class name
     */
    public function createRetriever(Adapter $adapter, string $name): Retriever;

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
}
