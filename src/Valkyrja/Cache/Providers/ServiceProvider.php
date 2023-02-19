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

namespace Valkyrja\Cache\Providers;

use Predis\Client;
use Valkyrja\Cache\Adapter;
use Valkyrja\Cache\Cache;
use Valkyrja\Cache\Driver;
use Valkyrja\Cache\Factories\ContainerFactory;
use Valkyrja\Cache\Factory;
use Valkyrja\Cache\LogAdapter;
use Valkyrja\Cache\RedisAdapter;
use Valkyrja\Config\Config\Config;
use Valkyrja\Container\Container;
use Valkyrja\Container\Support\Provider;
use Valkyrja\Log\Logger;

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
            Cache::class        => 'publishCache',
            Factory::class      => 'publishFactory',
            Driver::class       => 'publishDriver',
            Adapter::class      => 'publishAdapter',
            LogAdapter::class   => 'publishLogAdapter',
            RedisAdapter::class => 'publishRedisAdapter',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provides(): array
    {
        return [
            Cache::class,
            Factory::class,
            Driver::class,
            Adapter::class,
            LogAdapter::class,
            RedisAdapter::class,
        ];
    }

    /**
     * Publish the cache service.
     *
     * @param Container $container The container
     */
    public static function publishCache(Container $container): void
    {
        $config = $container->getSingleton(Config::class);

        $container->setSingleton(
            Cache::class,
            new \Valkyrja\Cache\Managers\Cache(
                $container->getSingleton(Factory::class),
                $config['cache']
            )
        );
    }

    /**
     * Publish the factory service.
     *
     * @param Container $container The container
     */
    public static function publishFactory(Container $container): void
    {
        $container->setSingleton(
            Factory::class,
            new ContainerFactory($container),
        );
    }

    /**
     * Publish the default driver service.
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
            static function (string $name, Adapter $adapter): Driver {
                return new $name(
                    $adapter
                );
            }
        );
    }

    /**
     * Publish an adapter service.
     *
     * @param Container $container The container
     */
    public static function publishAdapter(Container $container): void
    {
        $container->setClosure(
            Adapter::class,
            /**
             * @param class-string<Adapter> $name
             */
            static function (string $name, array $config): Adapter {
                return new $name(
                    $config['prefix'] ?? null
                );
            }
        );
    }

    /**
     * Publish a log adapter service.
     *
     * @param Container $container The container
     */
    public static function publishLogAdapter(Container $container): void
    {
        $logger = $container->getSingleton(Logger::class);

        $container->setClosure(
            LogAdapter::class,
            /**
             * @param class-string<LogAdapter> $name
             */
            static function (string $name, array $config) use ($logger): LogAdapter {
                return new $name(
                    $logger->use($config['logger'] ?? null),
                    $config['prefix'] ?? null
                );
            }
        );
    }

    /**
     * Publish a redis adapter service.
     *
     * @param Container $container The container
     */
    public static function publishRedisAdapter(Container $container): void
    {
        $container->setClosure(
            RedisAdapter::class,
            /**
             * @param class-string<RedisAdapter> $name
             */
            static function (string $name, array $config): RedisAdapter {
                $predis = new Client($config);

                return new $name(
                    $predis,
                    $config['prefix'] ?? null
                );
            }
        );
    }
}
