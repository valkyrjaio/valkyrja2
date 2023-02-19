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

namespace Valkyrja\Attribute\Providers;

use Valkyrja\Attribute\Attributes;
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
            Attributes::class => 'publishAttributes',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provides(): array
    {
        return [
            Attributes::class,
        ];
    }

    /**
     * Publish the attributes service.
     *
     * @param Container $container The container
     */
    public static function publishAttributes(Container $container): void
    {
        $container->setSingleton(
            Attributes::class,
            new \Valkyrja\Attribute\Managers\Attributes(
                $container->getSingleton(Reflector::class),
            )
        );
    }
}
