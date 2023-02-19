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

namespace Valkyrja\Path\Providers;

use Valkyrja\Config\Config\Config;
use Valkyrja\Container\Container;
use Valkyrja\Container\Support\Provider;
use Valkyrja\Path\PathGenerator;
use Valkyrja\Path\PathParser;

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
            PathGenerator::class => 'publishGenerator',
            PathParser::class    => 'publishParser',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provides(): array
    {
        return [
            PathGenerator::class,
            PathParser::class,
        ];
    }

    /**
     * Publish the generator service.
     *
     * @param Container $container The container
     */
    public static function publishGenerator(Container $container): void
    {
        $container->setSingleton(
            PathGenerator::class,
            new \Valkyrja\Path\Generators\PathGenerator()
        );
    }

    /**
     * Publish the parser service.
     *
     * @param Container $container The container
     */
    public static function publishParser(Container $container): void
    {
        $config = $container->getSingleton(Config::class);

        $container->setSingleton(
            PathParser::class,
            new \Valkyrja\Path\Parsers\PathParser(
                $config['path']
            )
        );
    }
}
