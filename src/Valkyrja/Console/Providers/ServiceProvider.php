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

namespace Valkyrja\Console\Providers;

use Valkyrja\Annotation\Filter;
use Valkyrja\Config\Config\Config;
use Valkyrja\Console\Annotator;
use Valkyrja\Console\Console;
use Valkyrja\Console\Dispatchers\CacheableConsole;
use Valkyrja\Console\Formatter;
use Valkyrja\Console\Input;
use Valkyrja\Console\Kernel;
use Valkyrja\Console\Output;
use Valkyrja\Container\Container;
use Valkyrja\Container\Support\Provider;
use Valkyrja\Dispatcher\Dispatcher;
use Valkyrja\Dispatcher\Validator;
use Valkyrja\Event\Events;
use Valkyrja\Path\PathParser;
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
            Console::class   => 'publishConsole',
            Formatter::class => 'publishFormatter',
            Input::class     => 'publishInput',
            Kernel::class    => 'publishKernel',
            Output::class    => 'publishOutput',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provides(): array
    {
        return [
            Annotator::class,
            Console::class,
            Formatter::class,
            Input::class,
            Kernel::class,
            Output::class,
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
            new \Valkyrja\Console\Annotators\Annotator(
                $container->getSingleton(Filter::class),
                $container->getSingleton(Reflector::class)
            )
        );
    }

    /**
     * Publish the console service.
     *
     * @param Container $container The container
     */
    public static function publishConsole(Container $container): void
    {
        $config = $container->getSingleton(Config::class);

        $container->setSingleton(
            Console::class,
            $console = new CacheableConsole(
                $container,
                $container->getSingleton(Dispatcher::class),
                $container->getSingleton(Validator::class),
                $container->getSingleton(Events::class),
                $container->getSingleton(PathParser::class),
                $config['console'],
                $config['app']['debug'],
            )
        );

        $console->setup();
    }

    /**
     * Publish the formatter service.
     *
     * @param Container $container The container
     */
    public static function publishFormatter(Container $container): void
    {
        $container->setSingleton(
            Formatter::class,
            new \Valkyrja\Console\Formatters\Formatter()
        );
    }

    /**
     * Publish the input service.
     *
     * @param Container $container The container
     */
    public static function publishInput(Container $container): void
    {
        $arguments = $_SERVER['argv'] ?? [];

        // Strip the application name
        array_shift($arguments);

        $container->setSingleton(
            Input::class,
            new \Valkyrja\Console\Inputs\Input($arguments)
        );
    }

    /**
     * Publish the kernel service.
     *
     * @param Container $container The container
     */
    public static function publishKernel(Container $container): void
    {
        $container->setSingleton(
            Kernel::class,
            new \Valkyrja\Console\Kernels\Kernel(
                $container->getSingleton(Console::class),
                $container,
                $container->getSingleton(Events::class)
            )
        );
    }

    /**
     * Publish the output service.
     *
     * @param Container $container The container
     */
    public static function publishOutput(Container $container): void
    {
        $container->setSingleton(
            Output::class,
            new \Valkyrja\Console\Outputs\Output()
        );
    }
}
