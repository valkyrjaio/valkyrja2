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

namespace Valkyrja\Client\Providers;

use GuzzleHttp\Client as Guzzle;
use Valkyrja\Client\Adapter;
use Valkyrja\Client\Client;
use Valkyrja\Client\Driver;
use Valkyrja\Client\Factories\ContainerFactory;
use Valkyrja\Client\Factory;
use Valkyrja\Client\GuzzleAdapter;
use Valkyrja\Client\LogAdapter;
use Valkyrja\Config\Config\Config;
use Valkyrja\Container\Container;
use Valkyrja\Container\Support\Provider;
use Valkyrja\Http\ResponseFactory;
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
            Client::class        => 'publishClient',
            Factory::class       => 'publishFactory',
            Driver::class        => 'publishDriver',
            GuzzleAdapter::class => 'publishGuzzleAdapter',
            LogAdapter::class    => 'publishLogAdapter',
            Adapter::class       => 'publishAdapter',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provides(): array
    {
        return [
            Client::class,
            Factory::class,
            Driver::class,
            GuzzleAdapter::class,
            LogAdapter::class,
            Adapter::class,
        ];
    }

    /**
     * Publish the client service.
     *
     * @param Container $container The container
     */
    public static function publishClient(Container $container): void
    {
        $config = $container->getSingleton(Config::class);

        $container->setSingleton(
            Client::class,
            new \Valkyrja\Client\Managers\Client(
                $container->getSingleton(Factory::class),
                $config['client']
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
     * Publish a driver.
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
                return new $name($adapter);
            }
        );
    }

    /**
     * Publish a guzzle adapter.
     *
     * @param Container $container The container
     */
    public static function publishGuzzleAdapter(Container $container): void
    {
        $responseFactory = $container->getSingleton(ResponseFactory::class);

        $container->setClosure(
            GuzzleAdapter::class,
            /**
             * @param class-string<GuzzleAdapter> $name
             */
            static function (string $name, array $config) use ($responseFactory): GuzzleAdapter {
                return new $name(
                    new Guzzle($config['options'] ?? []),
                    $responseFactory,
                    $config
                );
            }
        );
    }

    /**
     * Publish a log adapter.
     *
     * @param Container $container The container
     */
    public static function publishLogAdapter(Container $container): void
    {
        $logger          = $container->getSingleton(Logger::class);
        $responseFactory = $container->getSingleton(ResponseFactory::class);

        $container->setClosure(
            LogAdapter::class,
            /**
             * @param class-string<LogAdapter> $name
             */
            static function (string $name, array $config) use ($logger, $responseFactory): LogAdapter {
                return new $name(
                    $logger->use($config['logger'] ?? null),
                    $responseFactory,
                    $config
                );
            }
        );
    }

    /**
     * Publish an adapter.
     *
     * @param Container $container The container
     */
    public static function publishAdapter(Container $container): void
    {
        $responseFactory = $container->getSingleton(ResponseFactory::class);

        $container->setClosure(
            Adapter::class,
            /**
             * @param class-string<Adapter> $name
             */
            static function (string $name, array $config) use ($responseFactory): Adapter {
                return new $name(
                    $responseFactory,
                    $config
                );
            }
        );
    }
}
