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

namespace Valkyrja\Event\Providers;

use Valkyrja\Annotation\Filter;
use Valkyrja\Config\Config\Config;
use Valkyrja\Container\Container;
use Valkyrja\Container\Support\Provider;
use Valkyrja\Dispatcher\Dispatcher;
use Valkyrja\Event\Annotator;
use Valkyrja\Event\Dispatchers\CacheableEvents;
use Valkyrja\Event\Events;
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
            Annotator::class => 'publishAnnotator',
            Events::class    => 'publishEvents',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provides(): array
    {
        return [
            Annotator::class,
            Events::class,
        ];
    }

    /**
     * Publish the annotator service.
     *
     * @param Container $container The container
     */
    public static function publishAnnotator(Container $container): void
    {
        $container->setSingleton(
            Annotator::class,
            new \Valkyrja\Event\Annotators\Annotator(
                $container->getSingleton(Filter::class),
                $container->getSingleton(Reflector::class)
            )
        );
    }

    /**
     * Publish the events service.
     *
     * @param Container $container The container
     */
    public static function publishEvents(Container $container): void
    {
        $config = $container->getSingleton(Config::class);

        $container->setSingleton(
            Events::class,
            $events = new CacheableEvents(
                $container,
                $container->getSingleton(Dispatcher::class),
                $config['event']
            )
        );

        $events->setup();
    }
}
