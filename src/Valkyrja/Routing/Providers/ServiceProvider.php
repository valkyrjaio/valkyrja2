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

use Valkyrja\Annotation\Annotator as AnnotationAnnotator;
use Valkyrja\Annotation\Filter;
use Valkyrja\Config\Config\Config;
use Valkyrja\Container\Container;
use Valkyrja\Container\Support\Provider;
use Valkyrja\Dispatcher\Dispatcher;
use Valkyrja\Event\Events;
use Valkyrja\Http\Request;
use Valkyrja\Http\ResponseFactory;
use Valkyrja\Reflection\Reflector;
use Valkyrja\Routing\Annotator;
use Valkyrja\Routing\Attributes;
use Valkyrja\Routing\Collection;
use Valkyrja\Routing\Collections\CacheableCollection;
use Valkyrja\Routing\Collector;
use Valkyrja\Routing\Matcher;
use Valkyrja\Routing\Matchers\EntityCapableMatcher;
use Valkyrja\Routing\Processor;
use Valkyrja\Routing\Router;
use Valkyrja\Routing\Url;

/**
 * Class ServiceProvider.
 *
 * @author Melech Mizrachi
 */
class ServiceProvider extends Provider
{
    protected static Router|null     $router;
    protected static Annotator|null  $annotator;
    protected static Attributes|null $attributes;
    protected static Collector|null  $collector;
    protected static Collection|null $collection;
    protected static Matcher|null    $matcher;
    protected static Processor|null  $processor;
    protected static Url|null        $url;

    /**
     * @inheritDoc
     */
    public static function publishers(): array
    {
        return [
            Annotator::class  => 'publishAnnotator',
            Router::class     => 'publishRouter',
            Collector::class  => 'publishCollector',
            Collection::class => 'publishCollection',
            Matcher::class    => 'publishMatcher',
            Url::class        => 'publishUrl',
            Attributes::class => 'publishAttributes',
            Processor::class  => 'publishProcessor',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provides(): array
    {
        return [
            Annotator::class,
            Router::class,
            Collector::class,
            Collection::class,
            Matcher::class,
            Url::class,
            Attributes::class,
            Processor::class,
        ];
    }

    /**
     * Publish the router service.
     *
     * @param Container $container The container
     *
     * @return Router
     */
    public static function publishRouter(Container $container): Router
    {
        if (isset(self::$router)) {
            return self::$router;
        }

        $config = $container->getSingleton(Config::class);

        $container->setSingleton(
            Router::class,
            self::$router = new \Valkyrja\Routing\Dispatchers\Router(
                self::publishCollection($container),
                $container->getSingleton(Container::class),
                $container->getSingleton(Dispatcher::class),
                $container->getSingleton(Events::class),
                self::publishMatcher($container),
                $container->getSingleton(ResponseFactory::class),
                $config['routing'],
                $config['app']['debug']
            )
        );

        return self::$router;
    }

    /**
     * Publish the annotator service.
     *
     * @param Container $container The container
     *
     * @return Annotator
     */
    public static function publishAnnotator(Container $container): Annotator
    {
        if (isset(self::$annotator)) {
            return self::$annotator;
        }

        $container->setSingleton(
            Annotator::class,
            self::$annotator = new \Valkyrja\Routing\Annotators\Annotator(
                $container->getSingleton(AnnotationAnnotator::class),
                $container->getSingleton(Filter::class),
                $container->getSingleton(Reflector::class),
                self::publishProcessor($container)
            )
        );

        return self::$annotator;
    }

    /**
     * Publish the collector service.
     *
     * @param Container $container The container
     *
     * @return Collector
     */
    public static function publishCollector(Container $container): Collector
    {
        if (isset(self::$collector)) {
            return self::$collector;
        }

        $container->setSingleton(
            Collector::class,
            self::$collector = new \Valkyrja\Routing\Collectors\Collector(
                self::publishCollection($container),
                self::publishProcessor($container)
            )
        );

        return self::$collector;
    }

    /**
     * Publish the collection service.
     *
     * @param Container $container The container
     *
     * @return Collection
     */
    public static function publishCollection(Container $container): Collection
    {
        if (isset(self::$collection)) {
            return self::$collection;
        }

        $config = $container->getSingleton(Config::class);

        $container->setSingleton(
            Collection::class,
            self::$collection = $collection = new CacheableCollection(
                $container,
                $config['routing']
            )
        );

        $collection->setup();

        return self::$collection;
    }

    /**
     * Publish the matcher service.
     *
     * @param Container $container The container
     *
     * @return Matcher
     */
    public static function publishMatcher(Container $container): Matcher
    {
        if (isset(self::$matcher)) {
            return self::$matcher;
        }

        $container->setSingleton(
            Matcher::class,
            self::$matcher = new EntityCapableMatcher(
                $container,
                self::publishCollection($container)
            )
        );

        return self::$matcher;
    }

    /**
     * Publish the url service.
     *
     * @param Container $container The container
     *
     * @return Url
     */
    public static function publishUrl(Container $container): Url
    {
        if (isset(self::$url)) {
            return self::$url;
        }

        $config = $container->getSingleton(Config::class);

        $container->setSingleton(
            Url::class,
            self::$url = new \Valkyrja\Routing\Urls\Url(
                $container->getSingleton(Request::class),
                self::publishRouter($container),
                $config['routing']
            )
        );

        return self::$url;
    }

    /**
     * Publish the route attributes service.
     *
     * @param Container $container The container
     *
     * @return Attributes
     */
    public static function publishAttributes(Container $container): Attributes
    {
        if (isset(self::$attributes)) {
            return self::$attributes;
        }

        $container->setSingleton(
            Attributes::class,
            self::$attributes = new Attributes\Attributes(
                $container->getSingleton(\Valkyrja\Attribute\Attributes::class),
                $container->getSingleton(Reflector::class),
                self::publishProcessor($container)
            )
        );

        return self::$attributes;
    }

    /**
     * Publish the processor service.
     *
     * @param Container $container The container
     *
     * @return Processor
     */
    public static function publishProcessor(Container $container): Processor
    {
        if (isset(self::$processor)) {
            return self::$processor;
        }

        $container->setSingleton(
            Processor::class,
            self::$processor = new \Valkyrja\Routing\Processors\Processor(
                $container->getSingleton(Dispatcher::class)
            )
        );

        return self::$processor;
    }
}
