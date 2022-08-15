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

namespace Valkyrja\Broadcast\Providers;

use Pusher\Pusher;
use Pusher\PusherException;
use Valkyrja\Broadcast\Adapter;
use Valkyrja\Broadcast\Adapters\CryptPusherAdapter;
use Valkyrja\Broadcast\Broadcast;
use Valkyrja\Broadcast\Driver;
use Valkyrja\Broadcast\Loader;
use Valkyrja\Broadcast\Loaders\ContainerLoader;
use Valkyrja\Broadcast\LogAdapter;
use Valkyrja\Broadcast\Message;
use Valkyrja\Broadcast\PusherAdapter;
use Valkyrja\Container\Container;
use Valkyrja\Container\Support\Provider;
use Valkyrja\Crypt\Crypt;
use Valkyrja\Log\Logger;

use const CURL_IPRESOLVE_V4;
use const CURLOPT_IPRESOLVE;

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
            Broadcast::class          => 'publishBroadcaster',
            Loader::class             => 'publishLoader',
            Driver::class             => 'publishDriver',
            Adapter::class            => 'publishAdapter',
            CryptPusherAdapter::class => 'publishCryptPusherAdapter',
            LogAdapter::class         => 'publishLogAdapter',
            Pusher::class             => 'publishPusher',
            PusherAdapter::class      => 'publishPusherAdapter',
            Message::class            => 'publishMessage',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provides(): array
    {
        return [
            Broadcast::class,
            Loader::class,
            CryptPusherAdapter::class,
            Driver::class,
            Adapter::class,
            LogAdapter::class,
            Pusher::class,
            PusherAdapter::class,
            Message::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function publish(Container $container): void
    {
    }

    /**
     * Publish the broadcaster service.
     *
     * @param Container $container The container
     *
     * @return void
     */
    public static function publishBroadcaster(Container $container): void
    {
        $config = $container->getSingleton('config');

        $container->setSingleton(
            Broadcast::class,
            new \Valkyrja\Broadcast\Managers\Broadcast(
                $container->getSingleton(Loader::class),
                $config['client']
            )
        );
    }

    /**
     * Publish the loader service.
     *
     * @param Container $container The container
     *
     * @return void
     */
    public static function publishLoader(Container $container): void
    {
        $container->setSingleton(
            Loader::class,
            new ContainerLoader($container),
        );
    }

    /**
     * Publish a driver service.
     *
     * @param Container $container The container
     *
     * @return void
     */
    public static function publishDriver(Container $container): void
    {
        $container->setClosure(
            Driver::class,
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
     *
     * @return void
     */
    public static function publishAdapter(Container $container): void
    {
        $container->setClosure(
            Adapter::class,
            static function (string $name, array $config): Adapter {
                return new $name(
                    $config
                );
            }
        );
    }

    /**
     * Publish a log adapter service.
     *
     * @param Container $container The container
     *
     * @return void
     */
    public static function publishLogAdapter(Container $container): void
    {
        $logger = $container->getSingleton(Logger::class);

        $container->setClosure(
            LogAdapter::class,
            static function (string $name, array $config) use ($logger): LogAdapter {
                return new $name(
                    $logger->use($config['logger'] ?? null),
                    $config
                );
            }
        );
    }

    /**
     * Publish a pusher adapter service.
     *
     * @param Container $container The container
     *
     * @return void
     */
    public static function publishPusherAdapter(Container $container): void
    {
        $container->setClosure(
            PusherAdapter::class,
            static function (string $name, array $config) use ($container): PusherAdapter {
                return new $name(
                    $container->get(Pusher::class, [$config]),
                    $config
                );
            }
        );
    }

    /**
     * Publish the crypt pusher adapter service.
     *
     * @param Container $container The container
     *
     * @return void
     */
    public static function publishCryptPusherAdapter(Container $container): void
    {
        $container->setClosure(
            CryptPusherAdapter::class,
            static function (string $name, array $config) use ($container): CryptPusherAdapter {
                return new $name(
                    $container->get(Pusher::class, [$config]),
                    $container->getSingleton(Crypt::class),
                    $config
                );
            }
        );
    }

    /**
     * Publish the Pusher service.
     *
     * @param Container $container The container
     *
     * @throws PusherException
     *
     * @return void
     */
    public static function publishPusher(Container $container): void
    {
        $container->setClosure(
            Pusher::class,
            static function (array $config): Pusher {
                return new Pusher(
                    $config['key'],
                    $config['secret'],
                    $config['id'],
                    [
                        'cluster'      => $config['cluster'],
                        'useTLS'       => $config['useTLS'],
                        'curl_options' => [
                            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                        ],
                    ]
                );
            }
        );
    }

    /**
     * Publish a message service.
     *
     * @param Container $container The container
     *
     * @return void
     */
    public static function publishMessage(Container $container): void
    {
        $container->setClosure(
            Message::class,
            static function (string $name, array $config): Message {
                return (new $name())->setChannel($config['channel']);
            }
        );
    }
}
