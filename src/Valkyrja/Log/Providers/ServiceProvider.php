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

namespace Valkyrja\Log\Providers;

use Exception;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as Monolog;
use Psr\Log\LoggerInterface;
use Valkyrja\Config\Config\Config;
use Valkyrja\Container\Container;
use Valkyrja\Container\Support\Provider;
use Valkyrja\Log\Adapter;
use Valkyrja\Log\Constants\LogLevel;
use Valkyrja\Log\Driver;
use Valkyrja\Log\Factories\ContainerFactory;
use Valkyrja\Log\Factory;
use Valkyrja\Log\Logger;
use Valkyrja\Log\PsrAdapter;

use function date;

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
            Logger::class          => 'publishLogger',
            Factory::class         => 'publishFactory',
            Driver::class          => 'publishDriver',
            Adapter::class         => 'publishAdapter',
            PsrAdapter::class      => 'publishPsrAdapter',
            LoggerInterface::class => 'publishLoggerInterface',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provides(): array
    {
        return [
            Logger::class,
            Factory::class,
            Driver::class,
            Adapter::class,
            PsrAdapter::class,
            LoggerInterface::class,
        ];
    }

    /**
     * @inheritDoc
     *
     * @throws Exception
     */
    public static function publish(Container $container): void
    {
    }

    /**
     * Bind the logger service.
     *
     * @param Container $container The container
     */
    public static function publishLogger(Container $container): void
    {
        $config = $container->getSingleton(Config::class);

        $container->setSingleton(
            Logger::class,
            new \Valkyrja\Log\Managers\Logger(
                $container->getSingleton(Factory::class),
                $config['log']
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
     * Bind the psr adapter service.
     *
     * @param Container $container The container
     */
    public static function publishPsrAdapter(Container $container): void
    {
        $container->setClosure(
            PsrAdapter::class,
            /**
             * @param class-string<PsrAdapter> $name
             */
            static function (string $name, array $config) use ($container): PsrAdapter {
                return new $name(
                    $container->get(
                        LoggerInterface::class,
                        [
                            $config,
                        ]
                    ),
                    $config
                );
            }
        );
    }

    /**
     * Bind an adapter service.
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
     * Bind a logger interface.
     *
     * @param Container $container The container
     *
     * @throws Exception
     */
    public static function publishLoggerInterface(Container $container): void
    {
        $container->setClosure(
            LoggerInterface::class,
            static function (array $config): LoggerInterface {
                $filePath  = $config['filePath'];
                $name      = $config['name'] . date('-Y-m-d');
                $handler   = new StreamHandler(
                    "$filePath/$name.log",
                    LogLevel::DEBUG
                );
                $formatter = new LineFormatter(
                    null,
                    null,
                    true,
                    true
                );

                $handler->setFormatter($formatter);

                return new Monolog(
                    $name,
                    [
                        $handler,
                    ]
                );
            }
        );
    }
}
