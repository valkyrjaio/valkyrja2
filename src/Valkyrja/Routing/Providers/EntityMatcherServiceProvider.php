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

namespace Valkyrja\Routing\Providers;

use Valkyrja\Container\Container;
use Valkyrja\Container\Support\Provider;
use Valkyrja\Routing\Collection;
use Valkyrja\Routing\Matcher;
use Valkyrja\Routing\Matchers\EntityCapableMatcher;

/**
 * Class EntityMatcherServiceProvider.
 *
 * @author Melech Mizrachi
 */
class EntityMatcherServiceProvider extends Provider
{
    /**
     * @inheritDoc
     */
    public static function publishers(): array
    {
        return [
            Matcher::class => 'publishMatcher',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provides(): array
    {
        return [
            Matcher::class,
        ];
    }

    /**
     * Publish the matcher service.
     *
     * @param Container $container The container
     */
    public static function publishMatcher(Container $container): void
    {
        $container->setSingleton(
            Matcher::class,
            new EntityCapableMatcher(
                $container,
                $container->getSingleton(Collection::class)
            )
        );
    }
}
