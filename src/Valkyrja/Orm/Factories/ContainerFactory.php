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

namespace Valkyrja\Orm\Factories;

use Valkyrja\Container\Container;
use Valkyrja\Orm\Adapter;
use Valkyrja\Orm\CacheRepository;
use Valkyrja\Orm\DeleteQueryBuilder;
use Valkyrja\Orm\Driver;
use Valkyrja\Orm\Factory as Contract;
use Valkyrja\Orm\InsertQueryBuilder;
use Valkyrja\Orm\Migration;
use Valkyrja\Orm\PdoAdapter;
use Valkyrja\Orm\Persister;
use Valkyrja\Orm\Query;
use Valkyrja\Orm\QueryBuilder;
use Valkyrja\Orm\Repository;
use Valkyrja\Orm\Retriever;
use Valkyrja\Orm\SelectQueryBuilder;
use Valkyrja\Orm\Statement;
use Valkyrja\Orm\UpdateQueryBuilder;
use Valkyrja\Type\Support\Cls;

/**
 * Class ContainerFactory.
 *
 * @author Melech Mizrachi
 */
class ContainerFactory implements Contract
{
    /**
     * The container service.
     */
    protected Container $container;

    /**
     * ContainerFactory constructor.
     *
     * @param Container $container The container service
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function createAdapter(string $name, array $config): Adapter
    {
        return Cls::getDefaultableService(
            $this->container,
            $name,
            is_a($name, PdoAdapter::class, true) ? PdoAdapter::class : Adapter::class,
            [
                $config,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function createDriver(Adapter $adapter, string $name, array $config): Driver
    {
        return Cls::getDefaultableService(
            $this->container,
            $name,
            Driver::class,
            [
                $adapter,
                $config,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function createRepository(Driver $driver, string $name, string $entity): Repository
    {
        return Cls::getDefaultableService(
            $this->container,
            $name,
            is_a($name, CacheRepository::class, true) ? CacheRepository::class : Repository::class,
            [
                $driver,
                $entity,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function createQueryBuilder(Adapter $adapter, string $name): QueryBuilder
    {
        return Cls::getDefaultableService(
            $this->container,
            $name,
            QueryBuilder::class,
            [$adapter]
        );
    }

    /**
     * @inheritDoc
     */
    public function createDeleteQueryBuilder(Adapter $adapter, string $name): DeleteQueryBuilder
    {
        return Cls::getDefaultableService(
            $this->container,
            $name,
            DeleteQueryBuilder::class,
            [$adapter]
        );
    }

    /**
     * @inheritDoc
     */
    public function createInsertQueryBuilder(Adapter $adapter, string $name): InsertQueryBuilder
    {
        return Cls::getDefaultableService(
            $this->container,
            $name,
            InsertQueryBuilder::class,
            [$adapter]
        );
    }

    /**
     * @inheritDoc
     */
    public function createSelectQueryBuilder(Adapter $adapter, string $name): SelectQueryBuilder
    {
        return Cls::getDefaultableService(
            $this->container,
            $name,
            SelectQueryBuilder::class,
            [$adapter]
        );
    }

    /**
     * @inheritDoc
     */
    public function createUpdateQueryBuilder(Adapter $adapter, string $name): UpdateQueryBuilder
    {
        return Cls::getDefaultableService(
            $this->container,
            $name,
            UpdateQueryBuilder::class,
            [$adapter]
        );
    }

    /**
     * @inheritDoc
     */
    public function createQuery(Adapter $adapter, string $name): Query
    {
        return Cls::getDefaultableService(
            $this->container,
            $name,
            Query::class,
            [$adapter]
        );
    }

    /**
     * @inheritDoc
     */
    public function createPersister(Adapter $adapter, string $name): Persister
    {
        return Cls::getDefaultableService(
            $this->container,
            $name,
            Persister::class,
            [$adapter]
        );
    }

    /**
     * @inheritDoc
     */
    public function createRetriever(Adapter $adapter, string $name): Retriever
    {
        return Cls::getDefaultableService(
            $this->container,
            $name,
            Retriever::class,
            [$adapter]
        );
    }

    /**
     * @inheritDoc
     */
    public function createStatement(Adapter $adapter, string $name, array $data = []): Statement
    {
        return Cls::getDefaultableService(
            $this->container,
            $name,
            Statement::class,
            [$adapter, $data]
        );
    }

    /**
     * @inheritDoc
     */
    public function createMigration(string $name, array $data = []): Migration
    {
        return Cls::getDefaultableService(
            $this->container,
            $name,
            Migration::class,
            [$data]
        );
    }
}
