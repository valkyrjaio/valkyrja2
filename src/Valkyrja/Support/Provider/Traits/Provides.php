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

namespace Valkyrja\Support\Provider\Traits;

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
     *      public static function publishProvidedClass(Application $app): void
     * </code>
     *
     * @return string[]
     */
    public static function publishers(): array
    {
        return [];
    }

    /**
     * The items provided by this provider.
     */
    abstract public static function provides(): array;

    /**
     * Publish the provider.
     *
     * @param object $providerAware The providers aware class
     */
    abstract public static function publish(object $providerAware): void;
}
