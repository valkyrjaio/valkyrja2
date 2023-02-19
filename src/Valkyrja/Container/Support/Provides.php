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

namespace Valkyrja\Container\Support;

use Valkyrja\Container\Container;

/**
 * Trait Provides.
 *
 * @author Melech Mizrachi
 */
trait Provides
{
    /**
     * Whether this provider is deferred.
     */
    public static function deferred(): bool
    {
        return true;
    }

    /**
     * The items provided by this provider.
     *
     * <code>
     *      [
     *          Provided::class => 'publish',
     *          Provided::class => 'publishProvidedClass',
     *      ]
     *
     * ...
     *      public static function publishProvidedClass(Container $container): void
     * </code>
     *
     * @return array<class-string, string>
     */
    public static function publishers(): array
    {
        return [];
    }

    /**
     * The items provided by this provider.
     *
     * @return class-string[]
     */
    abstract public static function provides(): array;

    /**
     * Publish the provider.
     *
     * @param Container $container The container
     */
    public static function publish(Container $container): void
    {
    }
}
