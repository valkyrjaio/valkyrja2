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

namespace Valkyrja\Auth\Providers;

use Valkyrja\Auth\Adapter;
use Valkyrja\Auth\Auth;
use Valkyrja\Auth\Config\Config;
use Valkyrja\Auth\CryptTokenizedRepository;
use Valkyrja\Auth\EntityPolicy;
use Valkyrja\Auth\EntityRoutePolicy;
use Valkyrja\Auth\Factories\ContainerFactory;
use Valkyrja\Auth\Factory;
use Valkyrja\Auth\Gate;
use Valkyrja\Auth\JWTCryptRepository;
use Valkyrja\Auth\JWTRepository;
use Valkyrja\Auth\ORMAdapter;
use Valkyrja\Auth\Policy;
use Valkyrja\Auth\Repository;
use Valkyrja\Container\Container;
use Valkyrja\Container\Support\Provider;
use Valkyrja\Crypt\Crypt;
use Valkyrja\Http\Request;
use Valkyrja\Jwt\Jwt;
use Valkyrja\Orm\Orm;
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
            Auth::class                     => 'publishAuth',
            Factory::class                  => 'publishFactory',
            Gate::class                     => 'publishGate',
            Repository::class               => 'publishRepository',
            CryptTokenizedRepository::class => 'publishCryptTokenizedRepository',
            JWTRepository::class            => 'publishJWTRepository',
            JWTCryptRepository::class       => 'publishJWTCryptRepository',
            Adapter::class                  => 'publishAdapter',
            ORMAdapter::class               => 'publishOrmAdapter',
            Policy::class                   => 'publishPolicy',
            EntityPolicy::class             => 'publishEntityPolicy',
            EntityRoutePolicy::class        => 'publishEntityRoutePolicy',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provides(): array
    {
        return [
            Auth::class,
            Factory::class,
            Gate::class,
            Repository::class,
            CryptTokenizedRepository::class,
            JWTRepository::class,
            JWTCryptRepository::class,
            Adapter::class,
            ORMAdapter::class,
            Policy::class,
            EntityPolicy::class,
            EntityRoutePolicy::class,
        ];
    }

    /**
     * Publish the auth service.
     *
     * @param Container $container The container
     */
    public static function publishAuth(Container $container): void
    {
        $config = $container->getSingleton(\Valkyrja\Config\Config\Config::class);

        $container->setSingleton(
            Auth::class,
            new \Valkyrja\Auth\Managers\Auth(
                $container->getSingleton(Factory::class),
                $container->getSingleton(Request::class),
                $config['auth']
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
            new ContainerFactory(
                $container,
            )
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
            static function (string $name, Config|array $config): Adapter {
                return new $name(
                    $config,
                );
            }
        );
    }

    /**
     * Publish the orm adapter service.
     *
     * @param Container $container The container
     */
    public static function publishOrmAdapter(Container $container): void
    {
        $orm = $container->getSingleton(Orm::class);

        $container->setClosure(
            ORMAdapter::class,
            /**
             * @param class-string<ORMAdapter> $name
             */
            static function (string $name, Config|array $config) use ($orm): ORMAdapter {
                return new $name(
                    $orm,
                    $config
                );
            }
        );
    }

    /**
     * Publish a gate service.
     *
     * @param Container $container The container
     */
    public static function publishGate(Container $container): void
    {
        $container->setClosure(
            Gate::class,
            /**
             * @param class-string<Gate> $name
             */
            static function (string $name, Repository $repository) use ($container): Gate {
                return new $name(
                    $container->getSingleton(Auth::class),
                    $repository,
                );
            }
        );
    }

    /**
     * Publish a policy service.
     *
     * @param Container $container The container
     */
    public static function publishPolicy(Container $container): void
    {
        $container->setClosure(
            Policy::class,
            /**
             * @param class-string<Policy> $name
             */
            static function (string $name, Repository $repository): Policy {
                return new $name(
                    $repository,
                );
            }
        );
    }

    /**
     * Publish an entity policy service.
     *
     * @param Container $container The container
     */
    public static function publishEntityPolicy(Container $container): void
    {
        $container->setClosure(
            EntityPolicy::class,
            /**
             * @param class-string<EntityPolicy> $name
             */
            static function (string $name, Repository $repository) use ($container): EntityPolicy {
                return new $name(
                    $repository,
                    $container->getSingleton($name::getEntityClassName()),
                );
            }
        );
    }

    /**
     * Publish an entity route policy service.
     *
     * @param Container $container The container
     */
    public static function publishEntityRoutePolicy(Container $container): void
    {
        $container->setClosure(
            EntityRoutePolicy::class,
            /**
             * @param class-string<EntityRoutePolicy> $name
             */
            static function (string $name, Repository $repository) use ($container): EntityRoutePolicy {
                return new $name(
                    $repository,
                    $container->getSingleton($name::getEntityClassName() . $name::getEntityParamNumber()),
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
        $container->setClosure(
            Repository::class,
            /**
             * @param class-string<Repository> $name
             */
            static function (string $name, Adapter $adapter, string $user, Config|array $config) use ($container): Repository {
                return new $name(
                    $adapter,
                    $container->getSingleton(Session::class),
                    $config,
                    $user
                );
            }
        );
    }

    /**
     * Publish the crypt tokenized repository service.
     *
     * @param Container $container The container
     */
    public static function publishCryptTokenizedRepository(Container $container): void
    {
        $container->setClosure(
            CryptTokenizedRepository::class,
            /**
             * @param class-string<CryptTokenizedRepository> $name
             */
            static function (string $name, Adapter $adapter, string $user, Config|array $config) use ($container): CryptTokenizedRepository {
                return new $name(
                    $adapter,
                    $container->getSingleton(Crypt::class),
                    $container->getSingleton(Session::class),
                    $config,
                    $user
                );
            }
        );
    }

    /**
     * Publish the JWT crypt tokenized repository service.
     *
     * @param Container $container The container
     */
    public static function publishJWTCryptRepository(Container $container): void
    {
        $container->setClosure(
            JWTCryptRepository::class,
            /**
             * @param class-string<JWTCryptRepository> $name
             */
            static function (string $name, Adapter $adapter, string $user, Config|array $config) use ($container): JWTCryptRepository {
                return new $name(
                    $adapter,
                    $container->getSingleton(Jwt::class),
                    $container->getSingleton(Crypt::class),
                    $container->getSingleton(Session::class),
                    $config,
                    $user
                );
            }
        );
    }

    /**
     * Publish the JWT tokenized repository service.
     *
     * @param Container $container The container
     */
    public static function publishJWTRepository(Container $container): void
    {
        $container->setClosure(
            JWTRepository::class,
            /**
             * @param class-string<JWTRepository> $name
             */
            static function (string $name, Adapter $adapter, string $user, Config|array $config) use ($container): JWTRepository {
                return new $name(
                    $adapter,
                    $container->getSingleton(Jwt::class),
                    $container->getSingleton(Session::class),
                    $config,
                    $user
                );
            }
        );
    }
}
