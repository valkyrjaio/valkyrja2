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

namespace Valkyrja\Filesystem\Providers;

use Aws\S3\S3Client as AwsS3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter as FlysystemAwsS3Adapter;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\Local\LocalFilesystemAdapter as FlysystemLocalAdapter;
use Valkyrja\Config\Config\Config;
use Valkyrja\Container\Container;
use Valkyrja\Container\Support\Provider;
use Valkyrja\Filesystem\Adapter;
use Valkyrja\Filesystem\Driver;
use Valkyrja\Filesystem\Factories\ContainerFactory;
use Valkyrja\Filesystem\Factory;
use Valkyrja\Filesystem\Filesystem;
use Valkyrja\Filesystem\FlysystemAdapter;

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
            Filesystem::class            => 'publishFilesystem',
            Factory::class               => 'publishFactory',
            Driver::class                => 'publishDriver',
            Adapter::class               => 'publishAdapter',
            FlysystemAdapter::class      => 'publishFlysystemAdapter',
            FlysystemLocalAdapter::class => 'publishFlysystemLocalAdapter',
            FlysystemAwsS3Adapter::class => 'publishFlysystemAwsS3Adapter',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provides(): array
    {
        return [
            Filesystem::class,
            Factory::class,
            Driver::class,
            Adapter::class,
            FlysystemAdapter::class,
            FlysystemLocalAdapter::class,
            FlysystemAwsS3Adapter::class,
        ];
    }

    /**
     * Publish the filesystem service.
     *
     * @param Container $container The container
     */
    public static function publishFilesystem(Container $container): void
    {
        $config = $container->getSingleton(Config::class);

        $container->setSingleton(
            Filesystem::class,
            new \Valkyrja\Filesystem\Managers\Filesystem(
                $container->getSingleton(Factory::class),
                $config['filesystem']
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
                    $config,
                );
            }
        );
    }

    /**
     * Publish a flysystem adapter service.
     *
     * @param Container $container The container
     */
    public static function publishFlysystemAdapter(Container $container): void
    {
        $container->setClosure(
            FlysystemAdapter::class,
            /**
             * @param class-string<FlysystemAdapter> $name
             */
            static function (string $name, array $config) use ($container): FlysystemAdapter {
                return new $name(
                    new Flysystem(
                        $container->get(
                            $config['flysystemAdapter'],
                            [
                                $config,
                            ]
                        )
                    )
                );
            }
        );
    }

    /**
     * Publish the flysystem local adapter service.
     *
     * @param Container $container The container
     */
    public static function publishFlysystemLocalAdapter(Container $container): void
    {
        $container->setClosure(
            FlysystemLocalAdapter::class,
            static function (array $config) {
                return new FlysystemLocalAdapter(
                    $config['dir']
                );
            }
        );
    }

    /**
     * Publish the flysystem s3 adapter service.
     *
     * @param Container $container The container
     */
    public static function publishFlysystemAwsS3Adapter(Container $container): void
    {
        $container->setClosure(
            FlysystemAwsS3Adapter::class,
            static function (array $config) {
                $clientConfig = [
                    'credentials' => [
                        'key'    => $config['key'],
                        'secret' => $config['secret'],
                    ],
                    'region'      => $config['region'],
                    'version'     => $config['version'],
                ];

                return new FlysystemAwsS3Adapter(
                    client : new AwsS3Client($clientConfig),
                    bucket : $config['bucket'],
                    prefix : $config['prefix'],
                    options: $config['options']
                );
            }
        );
    }
}
