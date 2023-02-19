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

namespace Valkyrja\Reflection\Providers;

use Valkyrja\Container\Container;
use Valkyrja\Container\Support\Provider;
use Valkyrja\Reflection\Reflector;

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
            Reflector::class => 'publishReflector',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provides(): array
    {
        return [
            Reflector::class,
        ];
    }

    /**
     * Publish the reflector service.
     *
     * @param Container $container The container
     */
    public static function publishReflector(Container $container): void
    {
        $container->setSingleton(
            Reflector::class,
            new \Valkyrja\Reflection\Reflectors\Reflector()
        );
    }
}
