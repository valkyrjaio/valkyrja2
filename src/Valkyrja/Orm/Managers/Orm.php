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

namespace Valkyrja\Orm\Managers;

use Valkyrja\Orm\Adapter;
use Valkyrja\Orm\Config\Config;
use Valkyrja\Orm\Driver;
use Valkyrja\Orm\Entity;
use Valkyrja\Orm\Factory;
use Valkyrja\Orm\Migration;
use Valkyrja\Orm\Orm as Contract;
use Valkyrja\Orm\Persister;
use Valkyrja\Orm\Query;
use Valkyrja\Orm\QueryBuilder;
use Valkyrja\Orm\Repository;
use Valkyrja\Orm\Retriever;
use Valkyrja\Orm\SoftDeleteEntity;
use Valkyrja\Orm\Statement;

use function assert;
use function get_class;

/**
 * Class ORM.
 *
 * @author Melech Mizrachi
 */
class Orm implements Contract
{
    /**
     * The drivers.
     *
     * @var Driver[]
     */
    protected static array $drivers = [];

    /**
     * Repositories.
     *
     * @var Repository[]
     */
    protected static array $repositories = [];

    /**
     * The connections.
     */
    protected array $connections;

    /**
     * The default connection.
     */
    protected string $defaultConnection;

    /**
     * The default adapter.
     *
     * @var class-string<Adapter>
     */
    protected string $defaultAdapter;

    /**
     * The default driver.
     *
     * @var class-string<Driver>
     */
    protected string $defaultDriver;

    /**
     * The default repository.
     *
     * @var class-string<Repository>
     */
    protected string $defaultRepository;

    /**
     * The default query.
     *
     * @var class-string<Query>
     */
    protected string $defaultQuery;

    /**
     * The default query builder.
     *
     * @var class-string<QueryBuilder>
     */
    protected string $defaultQueryBuilder;

    /**
     * The default persister.
     *
     * @var class-string<Persister>
     */
    protected string $defaultPersister;

    /**
     * The default retriever.
     *
     * @var class-string<Retriever>
     */
    protected string $defaultRetriever;

    /**
     * ORM constructor.
     *
     * @param Factory      $factory The factory
     * @param Config|array $config  The config
     */
    public function __construct(
        protected Factory $factory,
        protected Config|array $config
    ) {
        $this->connections         = $config['connections'];
        $this->defaultConnection   = $config['default'];
        $this->defaultAdapter      = $config['adapter'];
        $this->defaultDriver       = $config['driver'];
        $this->defaultRepository   = $config['repository'];
        $this->defaultQuery        = $config['query'];
        $this->defaultQueryBuilder = $config['queryBuilder'];
        $this->defaultPersister    = $config['persister'];
        $this->defaultRetriever    = $config['retriever'];
    }

    /**
     * @inheritDoc
     */
    public function useConnection(string $name = null, string $adapter = null): Driver
    {
        // The connection to use
        $name ??= $this->defaultConnection;
        /** @var array{driver?: class-string<Driver>, adapter?: class-string<Adapter>} $config */
        // The connection config to use
        $config = $this->connections[$name];
        // The driver
        $driver = $config['driver'] ?? $this->defaultDriver;

        assert(is_a($name, Driver::class, true));

        // The adapter to use
        $adapter ??= $config['adapter'] ?? $this->defaultAdapter;

        assert(is_a($adapter, Adapter::class, true));

        // The cache key to use
        $cacheKey = $name . $driver . $adapter;

        return self::$drivers[$cacheKey]
            ??= $this->factory->createDriver($this->createAdapter($adapter, $config), $driver, $config);
    }

    /**
     * @inheritDoc
     */
    public function createAdapter(string $name, array $config): Adapter
    {
        assert(is_a($name, Adapter::class, true));

        // Set the query
        $config['query'] ??= $this->defaultQuery;
        // Set the query builder
        $config['queryBuilder'] ??= $this->defaultQueryBuilder;
        // Set the persister
        $config['persister'] ??= $this->defaultPersister;
        // Set the retriever
        $config['retriever'] ??= $this->defaultRetriever;

        return $this->factory->createAdapter($name, $config);
    }

    /**
     * @inheritDoc
     */
    public function createQueryBuilder(Adapter $adapter, string $name): QueryBuilder
    {
        assert(is_a($name, QueryBuilder::class, true));

        return $this->factory->createQueryBuilder($adapter, $name);
    }

    /**
     * @inheritDoc
     */
    public function createQuery(Adapter $adapter, string $name): Query
    {
        assert(is_a($name, Query::class, true));

        return $this->factory->createQuery($adapter, $name);
    }

    /**
     * @inheritDoc
     */
    public function createRetriever(Adapter $adapter, string $name): Retriever
    {
        assert(is_a($name, Retriever::class, true));

        return $this->factory->createRetriever($adapter, $name);
    }

    /**
     * @inheritDoc
     */
    public function createPersister(Adapter $adapter, string $name): Persister
    {
        assert(is_a($name, Persister::class, true));

        return $this->factory->createPersister($adapter, $name);
    }

    /**
     * @inheritDoc
     */
    public function getRepository(string $entity): Repository
    {
        assert(is_a($entity, Entity::class, true));

        $entityClass = $entity;

        $name     = $entityClass::getRepository() ?? $this->defaultRepository;
        $cacheKey = $name . $entity;

        return static::$repositories[$cacheKey]
            ?? static::$repositories[$cacheKey] = $this->factory->createRepository(
                $this->useConnection($entityClass::getConnection()),
                $name,
                $entity
            );
    }

    /**
     * @inheritDoc
     */
    public function getRepositoryFromClass(Entity $entity): Repository
    {
        return $this->getRepository(get_class($entity));
    }

    /**
     * @inheritDoc
     */
    public function createStatement(Adapter $adapter, string $name, array $data = []): Statement
    {
        return $this->factory->createStatement($adapter, $name, $data);
    }

    /**
     * @inheritDoc
     */
    public function createMigration(string $name, array $data = []): Migration
    {
        return $this->factory->createMigration($name, $data);
    }

    /**
     * @inheritDoc
     */
    public function beginTransaction(): bool
    {
        return $this->useConnection()->beginTransaction();
    }

    /**
     * @inheritDoc
     */
    public function inTransaction(): bool
    {
        return $this->useConnection()->inTransaction();
    }

    /**
     * @inheritDoc
     */
    public function ensureTransaction(): void
    {
        $this->useConnection()->ensureTransaction();
    }

    /**
     * @inheritDoc
     */
    public function persist(): bool
    {
        return $this->useConnection()->getPersister()->persist();
    }

    /**
     * @inheritDoc
     */
    public function rollback(): bool
    {
        return $this->useConnection()->rollback();
    }

    /**
     * @inheritDoc
     */
    public function lastInsertId(string $table = null, string $idField = null): string
    {
        return $this->useConnection()->lastInsertId($table, $idField);
    }

    /**
     * @inheritDoc
     */
    public function find(string $entity): Retriever
    {
        return $this->useConnection()->createRetriever()->find($entity);
    }

    /**
     * @inheritDoc
     */
    public function findOne(string $entity, int|string $id): Retriever
    {
        return $this->useConnection()->createRetriever()->findOne($entity, $id);
    }

    /**
     * @inheritDoc
     */
    public function count(string $entity): Retriever
    {
        return $this->useConnection()->createRetriever()->count($entity);
    }

    /**
     * @inheritDoc
     */
    public function create(Entity $entity, bool $defer = true): void
    {
        $this->getRepositoryFromClass($entity)->create($entity, $defer);
    }

    /**
     * @inheritDoc
     */
    public function save(Entity $entity, bool $defer = true): void
    {
        $this->getRepositoryFromClass($entity)->save($entity, $defer);
    }

    /**
     * @inheritDoc
     */
    public function delete(Entity $entity, bool $defer = true): void
    {
        $this->getRepositoryFromClass($entity)->delete($entity, $defer);
    }

    /**
     * @inheritDoc
     */
    public function softDelete(SoftDeleteEntity $entity, bool $defer = true): void
    {
        $this->getRepositoryFromClass($entity)->softDelete($entity, $defer);
    }

    /**
     * @inheritDoc
     */
    public function clear(Entity $entity = null): void
    {
        if ($entity !== null) {
            $this->getRepositoryFromClass($entity)->clear($entity);

            return;
        }

        $this->useConnection()->getPersister()->clear($entity);
    }
}
