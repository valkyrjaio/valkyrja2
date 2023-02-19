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

namespace Valkyrja\Api\Providers;

use Valkyrja\Api\Api;
use Valkyrja\Config\Config\Config;
use Valkyrja\Container\Container;
use Valkyrja\Container\Support\Provider;
use Valkyrja\Http\ResponseFactory;

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
            Api::class => 'publishApi',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provides(): array
    {
        return [
            Api::class,
        ];
    }

    /**
     * Publish the api service.
     *
     * @param Container $container The container
     */
    public static function publishApi(Container $container): void
    {
        $config = $container->getSingleton(Config::class);

        $container->setSingleton(
            Api::class,
            new \Valkyrja\Api\Apis\Api(
                $container->getSingleton(ResponseFactory::class),
                $config['api'],
                $config['app']['debug']
            )
        );
    }
}
