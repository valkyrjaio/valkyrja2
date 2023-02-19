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

namespace Valkyrja\Validation\Providers;

use Valkyrja\Config\Config\Config;
use Valkyrja\Container\Container;
use Valkyrja\Container\Support\Provider;
use Valkyrja\Orm\Orm as ORMManager;
use Valkyrja\Validation\Factories\ContainerFactory;
use Valkyrja\Validation\Factory;
use Valkyrja\Validation\Rules\ORM;
use Valkyrja\Validation\Validator;

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
            Validator::class => 'publishValidator',
            Factory::class   => 'publishFactory',
            ORM::class       => 'publishOrmRules',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provides(): array
    {
        return [
            Validator::class,
            Factory::class,
            ORM::class,
        ];
    }

    /**
     * Publish the validator service.
     *
     * @param Container $container The container
     */
    public static function publishValidator(Container $container): void
    {
        $config = $container->getSingleton(Config::class);

        $container->setSingleton(
            Validator::class,
            new \Valkyrja\Validation\Validators\Validator(
                $container->getSingleton(Factory::class),
                $config['validation']
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
     * Publish the ORM rules service.
     *
     * @param Container $container The container
     */
    public static function publishOrmRules(Container $container): void
    {
        $container->setSingleton(
            ORM::class,
            new ORM(
                $container,
                $container->getSingleton(ORMManager::class)
            )
        );
    }
}
