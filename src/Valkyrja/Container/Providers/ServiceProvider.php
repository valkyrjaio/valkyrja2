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

namespace Valkyrja\Container\Providers;

use Valkyrja\Annotation\Filter;
use Valkyrja\Container\Annotator;
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
            Annotator::class => 'publishAnnotator',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provides(): array
    {
        return [
            Annotator::class,
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
            new \Valkyrja\Container\Annotators\Annotator(
                $container->getSingleton(Filter::class),
                $container->getSingleton(Reflector::class)
            )
        );
    }
}
