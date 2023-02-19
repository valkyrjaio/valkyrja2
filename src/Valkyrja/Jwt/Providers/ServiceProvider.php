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

namespace Valkyrja\Jwt\Providers;

use Valkyrja\Config\Config\Config;
use Valkyrja\Container\Container;
use Valkyrja\Container\Support\Provider;
use Valkyrja\Jwt\Adapter;
use Valkyrja\Jwt\Driver;
use Valkyrja\Jwt\Factories\ContainerFactory;
use Valkyrja\Jwt\Factory;
use Valkyrja\Jwt\Jwt;

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
            Jwt::class     => 'publishJWT',
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
            Jwt::class,
            Factory::class,
            Driver::class,
            Adapter::class,
        ];
    }

    /**
     * Publish the JWT service.
     *
     * @param Container $container The container
     */
    public static function publishJWT(Container $container): void
    {
        $config = $container->getSingleton(Config::class);

        $container->setSingleton(
            Jwt::class,
            new \Valkyrja\Jwt\Managers\Jwt(
                $container->getSingleton(Factory::class),
                $config['jwt']
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
                    $config,
                );
            }
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
}
