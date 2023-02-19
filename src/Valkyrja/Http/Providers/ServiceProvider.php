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

namespace Valkyrja\Http\Providers;

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
            ResponseFactory::class => 'publishResponseFactory',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provides(): array
    {
        return [
            ResponseFactory::class,
        ];
    }

    /**
     * Publish the response factory service.
     *
     * @param Container $container The container
     */
    public static function publishResponseFactory(Container $container): void
    {
        $container->setSingleton(
            ResponseFactory::class,
            new \Valkyrja\Http\Factories\ResponseFactory(
                $container
            )
        );
    }
}
