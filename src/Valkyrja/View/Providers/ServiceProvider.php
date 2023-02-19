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

namespace Valkyrja\View\Providers;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Extension\ExtensionInterface;
use Twig\Loader\FilesystemLoader;
use Valkyrja\Config\Config\Config;
use Valkyrja\Container\Container;
use Valkyrja\Container\Support\Provider;
use Valkyrja\View\Engine;
use Valkyrja\View\Engines\OrkaEngine;
use Valkyrja\View\Engines\PhpEngine;
use Valkyrja\View\Engines\TwigEngine;
use Valkyrja\View\Exceptions\InvalidConfigPath;
use Valkyrja\View\Factories\ContainerFactory;
use Valkyrja\View\Factory;
use Valkyrja\View\Template;
use Valkyrja\View\View;

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
            View::class        => 'publishView',
            Factory::class     => 'publishFactory',
            Template::class    => 'publishTemplate',
            PhpEngine::class   => 'publishPhpEngine',
            OrkaEngine::class  => 'publishOrkaEngine',
            TwigEngine::class  => 'publishTwigEngine',
            Environment::class => 'publishTwigEnvironment',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provides(): array
    {
        return [
            View::class,
            Factory::class,
            Template::class,
            PhpEngine::class,
            OrkaEngine::class,
            TwigEngine::class,
            Environment::class,
        ];
    }

    /**
     * Publish the view service.
     *
     * @param Container $container The container
     *
     * @throws InvalidConfigPath
     */
    public static function publishView(Container $container): void
    {
        /** @var Config|array $config */
        $config = $container->getSingleton(Config::class);
        /** @var \Valkyrja\View\Config\Config|array{
         *     dir: string,
         *     engine: string,
         *     engines: array<string, class-string>,
         *     paths: array<string, string>,
         *     disks: array<string, array>
         * } $viewConfig
         */
        $viewConfig = $config['view'];

        $container->setSingleton(
            View::class,
            new \Valkyrja\View\Managers\View(
                $container,
                $container->getSingleton(Factory::class),
                $viewConfig
            )
        );
    }

    /**
     * Publish the factory service.
     *
     * @param Container $container The container
     */
    public static function publishFactory(Container $container): void
    {
        $container->setSingleton(
            Factory::class,
            new ContainerFactory($container)
        );
    }

    /**
     * Publish the template service.
     *
     * @param Container $container The container
     */
    public static function publishTemplate(Container $container): void
    {
        $container->setClosure(
            Template::class,
            static function (Engine $engine) {
                return new \Valkyrja\View\Templates\Template($engine);
            }
        );
    }

    /**
     * Publish the PHP engine service.
     *
     * @param Container $container The container
     */
    public static function publishPhpEngine(Container $container): void
    {
        /** @var Config|array $config */
        $config = $container->getSingleton(Config::class);
        /** @var \Valkyrja\View\Config\Config|array{
         *     dir: string,
         *     engine: string,
         *     engines: array<string, class-string>,
         *     paths: array<string, string>,
         *     disks: array{php?: array{fileExtension: string}}
         * } $viewConfig
         */
        $viewConfig = $config['view'];

        $container->setSingleton(
            PhpEngine::class,
            new PhpEngine($viewConfig)
        );
    }

    /**
     * Publish the Orka engine service.
     *
     * @param Container $container The container
     */
    public static function publishOrkaEngine(Container $container): void
    {
        /** @var Config|array $config */
        $config = $container->getSingleton(Config::class);
        /** @var \Valkyrja\Application\Config\Config|array $appConfig */
        $appConfig = $config['app'];
        /** @var bool $debug */
        $debug = $appConfig['debug'];
        /** @var \Valkyrja\View\Config\Config|array{
         *     dir: string,
         *     engine: string,
         *     engines: array<string, class-string>,
         *     paths: array<string, string>,
         *     disks: array{orka?: array{fileExtension: string}}
         * } $viewConfig
         */
        $viewConfig = $config['view'];

        $container->setSingleton(
            OrkaEngine::class,
            new OrkaEngine($viewConfig, $debug)
        );
    }

    /**
     * Publish the Twig engine service.
     *
     * @param Container $container The container
     */
    public static function publishTwigEngine(Container $container): void
    {
        $container->setSingleton(
            TwigEngine::class,
            new TwigEngine(
                $container->getSingleton(Environment::class)
            )
        );
    }

    /**
     * Publish the Twig environment service.
     *
     * @param Container $container The container
     *
     * @throws LoaderError
     */
    public static function publishTwigEnvironment(Container $container): void
    {
        /** @var Config|array $config */
        $config = $container->getSingleton(Config::class);
        /** @var \Valkyrja\Application\Config\Config|array $appConfig */
        $appConfig = $config['app'];
        /** @var bool $debug */
        $debug = $appConfig['debug'];
        /** @var \Valkyrja\View\Config\Config|array{
         *     dir: string,
         *     engine: string,
         *     engines: array<string, class-string>,
         *     paths: array<string, string>,
         *     disks: array{
         *          twig: array{
         *              compiledDir: string,
         *              paths: string[],
         *              extensions: class-string<ExtensionInterface>
         *          }
         *     }
         * } $viewConfig
         */
        $viewConfig = $config['view'];
        /** @var array $disks */
        $disks = $viewConfig['disks'];
        /** @var array $twigConfig */
        $twigConfig = $disks['twig'];
        /** @var array<string, string> $paths */
        $paths = $twigConfig['paths'];
        /** @var class-string<ExtensionInterface>[] $extensions */
        $extensions = $twigConfig['extensions'];
        /** @var string $compiledDir */
        $compiledDir = $twigConfig['compiledDir'];

        // Get the twig filesystem loader
        $loader = new FilesystemLoader();

        // Iterate through the dirs and add each as a path in the twig loader
        foreach ($paths as $namespace => $dir) {
            $loader->addPath($dir, $namespace);
        }

        // Create a new twig environment
        $twig = new Environment(
            $loader,
            [
                'cache'   => $compiledDir,
                'debug'   => $debug,
                'charset' => 'utf-8',
            ]
        );

        // Iterate through the extensions
        foreach ($extensions as $extension) {
            // And add each extension to the twig environment
            $twig->addExtension(new $extension());
        }

        // Set the twig environment as a singleton in the container
        $container->setSingleton(
            Environment::class,
            $twig
        );
    }
}
