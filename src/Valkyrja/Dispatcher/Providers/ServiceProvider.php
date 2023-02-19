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

namespace Valkyrja\Dispatcher\Providers;

use Valkyrja\Container\Container;
use Valkyrja\Container\Support\Provider;
use Valkyrja\Dispatcher\Dispatcher;
use Valkyrja\Dispatcher\Validator;

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
            Dispatcher::class => 'publishDispatcher',
            Validator::class  => 'publishValidator',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provides(): array
    {
        return [
            Dispatcher::class,
            Validator::class,
        ];
    }

    /**
     * Publish the dispatcher service.
     *
     * @param Container $container The container
     */
    public static function publishDispatcher(Container $container): void
    {
        $container->setSingleton(
            Dispatcher::class,
            new \Valkyrja\Dispatcher\Dispatchers\Dispatcher(
                $container
            )
        );
    }

    /**
     * Publish the validator service.
     *
     * @param Container $container The container
     */
    public static function publishValidator(Container $container): void
    {
        $container->setSingleton(
            Validator::class,
            new \Valkyrja\Dispatcher\Validators\Validator()
        );
    }
}
