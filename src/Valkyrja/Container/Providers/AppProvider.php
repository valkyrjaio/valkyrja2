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

use Valkyrja\Application\Application;
use Valkyrja\Application\Env;
use Valkyrja\Application\Support\Provider;
use Valkyrja\Config\Config\Config;
use Valkyrja\Container\Container;
use Valkyrja\Container\Managers\CacheableContainer;
use Valkyrja\Exception\ExceptionHandler;

use const E_ALL;

/**
 * Class AppProvider.
 *
 * @author Melech Mizrachi
 */
class AppProvider extends Provider
{
    /**
     * @inheritDoc
     */
    public static function publish(Application $app): void
    {
        $config = $app->config();

        $container = new CacheableContainer($config['container'], $app->debug());

        $app->setContainer($container);

        $container->setup();

        static::bootstrapContainer($app, $container);
        static::bootstrapServices($app, $container, $config);
        // Bootstrap debug capabilities
        static::bootstrapExceptionHandler($app, $container, $config);
        // Bootstrap the timezone
        static::bootstrapTimezone($config);
    }

    /**
     * Bootstrap container.
     *
     * @param Application $app       The application
     * @param Container   $container The container
     */
    protected static function bootstrapContainer(Application $app, Container $container): void
    {
    }

    /**
     * Bootstrap services.
     *
     * @param Application  $app       The application
     * @param Container    $container The container
     * @param Config|array $config    The config
     */
    protected static function bootstrapServices(Application $app, Container $container, Config|array $config): void
    {
        $container->setSingleton(Application::class, $app);
        $container->setSingleton('env', $app->env());
        $container->bindAlias('env', Env::class);
        $container->setSingleton(Config::class, $config);
        $container->bindAlias('config', Config::class);
        $container->setSingleton(Container::class, $container);
    }

    /**
     * Bootstrap exception handler.
     *
     * @param Application  $app       The application
     * @param Container    $container The container
     * @param Config|array $config    The config
     */
    protected static function bootstrapExceptionHandler(Application $app, Container $container, Config|array $config): void
    {
        /** @var ExceptionHandler $exceptionHandler */
        $exceptionHandler = $config['app']['exceptionHandler'];

        // Set exception handler in the service container
        $container->setSingleton(ExceptionHandler::class, $exceptionHandler);

        // If debug is on, enable debug handling
        if ($app->debug()) {
            // Enable exception handling
            $exceptionHandler::enable(E_ALL, true);
        }
    }

    /**
     * Bootstrap the timezone.
     *
     * @param Config|array $config The config
     */
    protected static function bootstrapTimezone(Config|array $config): void
    {
        date_default_timezone_set($config['app']['timezone']);
    }
}
