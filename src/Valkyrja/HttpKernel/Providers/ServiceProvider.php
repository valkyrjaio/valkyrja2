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

namespace Valkyrja\HttpKernel\Providers;

use Valkyrja\Config\Config\Config;
use Valkyrja\Container\Container;
use Valkyrja\Container\Support\Provider;
use Valkyrja\Event\Events;
use Valkyrja\HttpKernel\Kernel;
use Valkyrja\Routing\Router;

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
            Kernel::class => 'publishKernel',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provides(): array
    {
        return [
            Kernel::class,
        ];
    }

    /**
     * Publish the kernel service.
     *
     * @param Container $container The container
     */
    public static function publishKernel(Container $container): void
    {
        $config     = $container->getSingleton(Config::class);
        $httpKernel = $config['app']['httpKernel'] ?? \Valkyrja\HttpKernel\Kernels\Kernel::class;

        $container->setSingleton(
            Kernel::class,
            new $httpKernel(
                $container,
                $container->getSingleton(Events::class),
                $container->getSingleton(Router::class),
                $config['routing'],
                $config['app']['debug']
            )
        );
    }
}
