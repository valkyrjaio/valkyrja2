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

namespace Valkyrja\Crypt\Providers;

use Valkyrja\Config\Config\Config;
use Valkyrja\Container\Container;
use Valkyrja\Container\Support\Provider;
use Valkyrja\Crypt\Adapter;
use Valkyrja\Crypt\Crypt;
use Valkyrja\Crypt\Driver;
use Valkyrja\Crypt\Factories\ContainerFactory;
use Valkyrja\Crypt\Factory;

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
            Crypt::class   => 'publishCrypt',
            Factory::class => 'publishFactory',
            Driver::class  => 'publishDriver',
            Adapter::class => 'publishAdapter',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provides(): array
    {
        return [
            Crypt::class,
            Factory::class,
            Driver::class,
            Adapter::class,
        ];
    }

    /**
     * Publish the crypt service.
     *
     * @param Container $container The container
     */
    public static function publishCrypt(Container $container): void
    {
        $config = $container->getSingleton(Config::class);

        $container->setSingleton(
            Crypt::class,
            new \Valkyrja\Crypt\Managers\Crypt(
                $container->getSingleton(Factory::class),
                $config['crypt']
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
     * Publish the adapter service.
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
}
