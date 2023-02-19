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

namespace Valkyrja\Facade;

use Valkyrja\Container\Container;

use function is_object;

/**
 * Abstract Class ContainerFacade.
 *
 * @author Melech Mizrachi
 */
abstract class ContainerFacade extends Facade
{
    /**
     * The container.
     */
    private static Container $container;

    /**
     * Get the container.
     */
    public static function getContainer(): Container
    {
        return self::$container ??= \Valkyrja\container();
    }

    /**
     * Set the container.
     *
     * @param Container $container The container
     */
    public static function setContainer(Container $container): void
    {
        self::$container = $container;
    }

    /**
     * @inheritDoc
     */
    public static function setInstance(object|string $instance): void
    {
        if (is_object($instance)) {
            self::$instances[static::class] = $instance;

            return;
        }

        self::$instances[static::class] = self::getContainer()->get($instance);
    }
}
