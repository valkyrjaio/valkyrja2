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

namespace Valkyrja\Annotation\Providers;

use Valkyrja\Annotation\Annotator;
use Valkyrja\Annotation\Filter;
use Valkyrja\Annotation\Parser;
use Valkyrja\Config\Config\Config;
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
            Filter::class    => 'publishFilter',
            Parser::class    => 'publishParser',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provides(): array
    {
        return [
            Annotator::class,
            Filter::class,
            Parser::class,
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
            new \Valkyrja\Annotation\Annotators\Annotator(
                $container->getSingleton(Parser::class),
                $container->getSingleton(Reflector::class)
            )
        );
    }

    /**
     * Publish the filter service.
     *
     * @param Container $container The container
     */
    public static function publishFilter(Container $container): void
    {
        $container->setSingleton(
            Filter::class,
            new \Valkyrja\Annotation\Filters\Filter(
                $container->getSingleton(Annotator::class)
            )
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
            Parser::class,
            new \Valkyrja\Annotation\Parsers\Parser(
                $config['annotation']
            )
        );
    }
}
