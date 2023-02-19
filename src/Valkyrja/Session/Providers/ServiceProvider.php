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

namespace Valkyrja\Session\Providers;

use Valkyrja\Cache\Cache;
use Valkyrja\Config\Config\Config;
use Valkyrja\Container\Container;
use Valkyrja\Container\Support\Provider;
use Valkyrja\Crypt\Crypt;
use Valkyrja\Http\Request;
use Valkyrja\Log\Logger;
use Valkyrja\Session\Adapter;
use Valkyrja\Session\Adapters\CookieAdapter;
use Valkyrja\Session\CacheAdapter;
use Valkyrja\Session\Driver;
use Valkyrja\Session\Factories\ContainerFactory;
use Valkyrja\Session\Factory;
use Valkyrja\Session\LogAdapter;
use Valkyrja\Session\Session;

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
            Session::class       => 'publishSession',
            Factory::class       => 'publishFactory',
            Driver::class        => 'publishDriver',
            Adapter::class       => 'publishAdapter',
            CacheAdapter::class  => 'publishCacheAdapter',
            CookieAdapter::class => 'publishCookieAdapter',
            LogAdapter::class    => 'publishLogAdapter',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provides(): array
    {
        return [
            Session::class,
            Factory::class,
            Driver::class,
            Adapter::class,
            CacheAdapter::class,
            CookieAdapter::class,
            LogAdapter::class,
        ];
    }

    /**
     * Publish the session service.
     *
     * @param Container $container The container
     */
    public static function publishSession(Container $container): void
    {
        $config = $container->getSingleton(Config::class);

        $container->setSingleton(
            Session::class,
            new \Valkyrja\Session\Managers\Session(
                $container->getSingleton(Factory::class),
                $config['session']
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
                    $config
                );
            }
        );
    }

    /**
     * Publish a cache adapter service.
     *
     * @param Container $container The container
     */
    public static function publishCacheAdapter(Container $container): void
    {
        $cache = $container->getSingleton(Cache::class);

        $container->setClosure(
            CacheAdapter::class,
            /**
             * @param class-string<CacheAdapter> $name
             */
            static function (string $name, array $config) use ($cache): CacheAdapter {
                return new $name(
                    $cache,
                    $config
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
                    $config
                );
            }
        );
    }

    /**
     * Publish the cookie adapter service.
     *
     * @param Container $container The container
     */
    public static function publishCookieAdapter(Container $container): void
    {
        $crypt   = $container->getSingleton(Crypt::class);
        $request = $container->getSingleton(Request::class);

        $container->setClosure(
            CookieAdapter::class,
            static function (array $config) use ($crypt, $request): CookieAdapter {
                return new CookieAdapter(
                    $crypt,
                    $request,
                    $config
                );
            }
        );
    }
}
