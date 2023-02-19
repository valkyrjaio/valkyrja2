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

namespace Valkyrja\Orm\Providers;

use RuntimeException;
use Valkyrja\Cache\Cache;
use Valkyrja\Config\Config\Config;
use Valkyrja\Container\Container;
use Valkyrja\Container\Support\Provider;
use Valkyrja\Orm\Adapter;
use Valkyrja\Orm\CacheRepository;
use Valkyrja\Orm\DeleteQueryBuilder;
use Valkyrja\Orm\Driver;
use Valkyrja\Orm\Factories\ContainerFactory;
use Valkyrja\Orm\Factory;
use Valkyrja\Orm\InsertQueryBuilder;
use Valkyrja\Orm\Migrations\Migration;
use Valkyrja\Orm\Orm;
use Valkyrja\Orm\PdoAdapter;
use Valkyrja\Orm\Pdos\Pdo;
use Valkyrja\Orm\Persister;
use Valkyrja\Orm\Query;
use Valkyrja\Orm\QueryBuilder;
use Valkyrja\Orm\Repository;
use Valkyrja\Orm\Retriever;
use Valkyrja\Orm\Retrievers\LocalCacheRetriever;
use Valkyrja\Orm\SelectQueryBuilder;
use Valkyrja\Orm\Statement;
use Valkyrja\Orm\UpdateQueryBuilder;

/**
 * Class ServiceProvider.
 *
 * @author Melech Mizrachi
 */
class ServiceProvider extends Provider
{
    /**
     * @inheritDoc
     */
    public static function publishers(): array
    {
        return [
            Orm::class                 => 'publishORM',
            Factory::class             => 'publishFactory',
            Driver::class              => 'publishDriver',
            Adapter::class             => 'publishAdapter',
            PdoAdapter::class          => 'publishPdoAdapter',
            Repository::class          => 'publishRepository',
            CacheRepository::class     => 'publishCacheRepository',
            Persister::class           => 'publishPersister',
            Retriever::class           => 'publishRetriever',
            LocalCacheRetriever::class => 'publishLocalCacheRetriever',
            Query::class               => 'publishQuery',
            QueryBuilder::class        => 'publishQueryBuilder',
            DeleteQueryBuilder::class  => 'publishDeleteQueryBuilder',
            InsertQueryBuilder::class  => 'publishInsertQueryBuilder',
            SelectQueryBuilder::class  => 'publishSelectQueryBuilder',
            UpdateQueryBuilder::class  => 'publishUpdateQueryBuilder',
            Statement::class           => 'publishStatement',
            Pdo::class                 => 'publishPDO',
            Migration::class           => 'publishMigration',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provides(): array
    {
        return [
            Orm::class,
            Factory::class,
            Driver::class,
            Adapter::class,
            PdoAdapter::class,
            Repository::class,
            CacheRepository::class,
            Persister::class,
            Retriever::class,
            Query::class,
            QueryBuilder::class,
            DeleteQueryBuilder::class,
            InsertQueryBuilder::class,
            SelectQueryBuilder::class,
            UpdateQueryBuilder::class,
            Statement::class,
            Pdo::class,
            Migration::class,
        ];
    }

    /**
     * Publish the ORM service.
     *
     * @param Container $container The container
     */
    public static function publishORM(Container $container): void
    {
        $config = $container->getSingleton(Config::class);

        $container->setSingleton(
            Orm::class,
            new \Valkyrja\Orm\Managers\Orm(
                $container->getSingleton(Factory::class),
                $config['orm']
            )
        );
    }

    /**
     * Publish the factory.
     *
     * @param Container $container The container
     */
    public static function publishFactory(Container $container): void
    {
        $container->setSingleton(
            Factory::class,
            new ContainerFactory($container)
        );
    }

    /**
     * Publish a driver service.
     *
     * @param Container $container The container
     */
    public static function publishDriver(Container $container): void
    {
        $container->setClosure(
            Driver::class,
            /**
             * @param class-string<Driver> $name
             */
            static function (string $name, Adapter $adapter, array $config): Driver {
                return new $name(
                    $adapter,
                    $config
                );
            }
        );
    }

    /**
     * Publish a adapter service.
     *
     * @param Container $container The container
     */
    public static function publishAdapter(Container $container): void
    {
        $orm = $container->getSingleton(Orm::class);

        $container->setClosure(
            Adapter::class,
            /**
             * @param class-string<Adapter> $name
             */
            static function (string $name, array $config) use ($orm): Adapter {
                return new $name(
                    $orm,
                    $config
                );
            }
        );
    }

    /**
     * Publish a PDO adapter service.
     *
     * @param Container $container The container
     */
    public static function publishPdoAdapter(Container $container): void
    {
        $orm = $container->getSingleton(Orm::class);

        $container->setClosure(
            PdoAdapter::class,
            /**
             * @param class-string<PdoAdapter> $name
             */
            static function (string $name, array $config) use ($container, $orm): PdoAdapter {
                $pdoConfig = $config['config'];
                $pdoClass  = $pdoConfig['pdo'] ?? \PDO::class;

                if ($container->has($pdoClass)) {
                    $pdo = $container->get($pdoClass, [$pdoConfig]);
                } else {
                    $pdo = $container->get(Pdo::class, [$pdoClass, $pdoConfig]);
                }

                return new $name(
                    $orm,
                    $pdo,
                    $config
                );
            }
        );
    }

    /**
     * Publish a PDO service.
     *
     * @param Container $container The container
     */
    public static function publishPDO(Container $container): void
    {
        $container->setClosure(
            Pdo::class,
            /**
             * @param class-string<Pdo> $name
             */
            static function (string $name, array $config): Pdo {
                if ($name === \PDO::class) {
                    // If we got here then that means the developer has opted to use the default PDO
                    // but has not defined a PDO in the service container. The reason for this requirement
                    // is that the Valkyrja PDO constructors take in a config array, whereas the default
                    // PDO takes in a DSN as the first param.
                    throw new RuntimeException('Default PDO service not found in container.');
                }

                return new $name(
                    $config
                );
            }
        );
    }

    /**
     * Publish a repository service.
     *
     * @param Container $container The container
     */
    public static function publishRepository(Container $container): void
    {
        $orm = $container->getSingleton(Orm::class);

        $container->setClosure(
            Repository::class,
            /**
             * @param class-string<Repository> $name
             */
            static function (string $name, Driver $driver, string $entity) use ($orm): Repository {
                return new $name(
                    $orm,
                    $driver,
                    $entity
                );
            }
        );
    }

    /**
     * Publish a cache repository service.
     *
     * @param Container $container The container
     */
    public static function publishCacheRepository(Container $container): void
    {
        $orm   = $container->getSingleton(Orm::class);
        $cache = $container->getSingleton(Cache::class);

        $container->setClosure(
            CacheRepository::class,
            /**
             * @param class-string<CacheRepository> $name
             */
            static function (string $name, Driver $driver, string $entity) use ($orm, $cache): CacheRepository {
                return new $name(
                    $orm,
                    $driver,
                    $cache,
                    $entity
                );
            }
        );
    }

    /**
     * Publish a persister service.
     *
     * @param Container $container The container
     */
    public static function publishPersister(Container $container): void
    {
        $container->setClosure(
            Persister::class,
            /**
             * @param class-string<Persister> $name
             */
            static function (string $name, Adapter $adapter): Persister {
                return new $name(
                    $adapter
                );
            }
        );
    }

    /**
     * Publish a retriever service.
     *
     * @param Container $container The container
     */
    public static function publishRetriever(Container $container): void
    {
        $container->setClosure(
            Retriever::class,
            /**
             * @param class-string<Retriever> $name
             */
            static function (string $name, Adapter $adapter): Retriever {
                return new $name(
                    $adapter
                );
            }
        );
    }

    /**
     * Publish a query service.
     *
     * @param Container $container The container
     */
    public static function publishQuery(Container $container): void
    {
        $container->setClosure(
            Query::class,
            /**
             * @param class-string<Query> $name
             */
            static function (string $name, Adapter $adapter): Query {
                return new $name(
                    $adapter
                );
            }
        );
    }

    /**
     * Publish a query builder service.
     *
     * @param Container $container The container
     */
    public static function publishQueryBuilder(Container $container): void
    {
        $container->setClosure(
            QueryBuilder::class,
            /**
             * @param class-string<QueryBuilder> $name
             */
            static function (string $name, Adapter $adapter): QueryBuilder {
                return new $name(
                    $adapter
                );
            }
        );
    }

    /**
     * Publish a delete query builder service.
     *
     * @param Container $container The container
     */
    public static function publishDeleteQueryBuilder(Container $container): void
    {
        $container->setClosure(
            DeleteQueryBuilder::class,
            /**
             * @param class-string<DeleteQueryBuilder> $name
             */
            static function (string $name, Adapter $adapter): DeleteQueryBuilder {
                return new $name(
                    $adapter
                );
            }
        );
    }

    /**
     * Publish a insert query builder service.
     *
     * @param Container $container The container
     */
    public static function publishInsertQueryBuilder(Container $container): void
    {
        $container->setClosure(
            InsertQueryBuilder::class,
            /**
             * @param class-string<InsertQueryBuilder> $name
             */
            static function (string $name, Adapter $adapter): InsertQueryBuilder {
                return new $name(
                    $adapter
                );
            }
        );
    }

    /**
     * Publish a select query builder service.
     *
     * @param Container $container The container
     */
    public static function publishSelectQueryBuilder(Container $container): void
    {
        $container->setClosure(
            SelectQueryBuilder::class,
            /**
             * @param class-string<SelectQueryBuilder> $name
             */
            static function (string $name, Adapter $adapter): SelectQueryBuilder {
                return new $name(
                    $adapter
                );
            }
        );
    }

    /**
     * Publish a update query builder service.
     *
     * @param Container $container The container
     */
    public static function publishUpdateQueryBuilder(Container $container): void
    {
        $container->setClosure(
            UpdateQueryBuilder::class,
            /**
             * @param class-string<UpdateQueryBuilder> $name
             */
            static function (string $name, Adapter $adapter): UpdateQueryBuilder {
                return new $name(
                    $adapter
                );
            }
        );
    }

    /**
     * Publish a statement service.
     *
     * @param Container $container The container
     */
    public static function publishStatement(Container $container): void
    {
        $container->setClosure(
            Statement::class,
            /**
             * @param class-string<Statement> $name
             */
            static function (string $name, Adapter $adapter, array $data = []): Statement {
                return new $name(...$data);
            }
        );
    }

    /**
     * Publish a migration service.
     *
     * @param Container $container The container
     */
    public static function publishMigration(Container $container): void
    {
        $container->setClosure(
            Migration::class,
            /**
             * @param class-string<Migration> $name
             */
            static function (string $name, Orm $orm): Migration {
                return new $name($orm);
            }
        );
    }
}
