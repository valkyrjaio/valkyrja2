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

namespace Valkyrja\Routing\Collections;

use JsonException;
use Valkyrja\Config\Config;
use Valkyrja\Container\Container;
use Valkyrja\Dispatcher\Dispatcher;
use Valkyrja\Routing\Annotator;
use Valkyrja\Routing\Config\Cache;
use Valkyrja\Routing\Config\Config as RoutingConfig;
use Valkyrja\Routing\Exceptions\InvalidRoutePath;
use Valkyrja\Routing\RouteAttributes;
use Valkyrja\Support\Cacheable\Cacheable;

/**
 * Class CacheableCollection.
 *
 * @author Melech Mizrachi
 */
class CacheableCollection extends Collection
{
    /**
     * @use Cacheable<RoutingConfig, Cache>
     */
    use Cacheable;

    /**
     * RouteCollection constructor.
     *
     * @param Container           $container
     * @param Dispatcher          $dispatcher
     * @param RoutingConfig|array $config
     */
    public function __construct(
        Container $container,
        Dispatcher $dispatcher,
        protected RoutingConfig|array $config
    ) {
        parent::__construct($container, $dispatcher);
    }

    /**
     * @inheritDoc
     */
    public function getCacheable(): Config
    {
        $this->setup(true, false);

        $config          = new Cache();
        $config->routes  = [];
        $config->static  = $this->static;
        $config->dynamic = $this->dynamic;
        $config->named   = $this->named;

        foreach ($this->routes as $id => $route) {
            $config->routes[$id] = $route->asArray();
        }

        return $config;
    }

    /**
     * @inheritDoc
     */
    protected function getConfig(): Config|array
    {
        return $this->config;
    }

    /**
     * @inheritDoc
     */
    protected function beforeSetup(Config|array $config): void
    {
    }

    /**
     * @inheritDoc
     */
    protected function setupNotCached(Config|array $config): void
    {
    }

    /**
     * @inheritDoc
     */
    protected function setupFromCache(Config|array $config): void
    {
        $cache = $config['cache'] ?? require $config['cacheFilePath'];

        $this->routes  = $cache['routes'];
        $this->static  = $cache['static'];
        $this->dynamic = $cache['dynamic'];
        $this->named   = $cache['named'];
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidRoutePath
     * @throws JsonException
     */
    protected function setupAnnotations(Config|array $config): void
    {
        /** @var Annotator $routeAnnotations */
        $routeAnnotations = $this->container->getSingleton(Annotator::class);

        // Get all the annotated routes from the list of controllers
        // Iterate through the routes
        foreach ($routeAnnotations->getRoutes(...$config['controllers']) as $route) {
            // Set the route
            $this->add($route);
        }
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidRoutePath
     * @throws JsonException
     */
    protected function setupAttributes(Config|array $config): void
    {
        /** @var RouteAttributes $routeAttributes */
        $routeAttributes = $this->container->getSingleton(RouteAttributes::class);

        // Get all the attributes routes from the list of controllers
        // Iterate through the routes
        foreach ($routeAttributes->getRoutes(...$config['controllers']) as $route) {
            // Set the route
            $this->add($route);
        }
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidRoutePath
     */
    protected function afterSetup(Config|array $config): void
    {
        $this->dynamic = [];

        foreach ($this->routes as $route) {
            $this->setRouteToRequestMethods($route);
        }
    }
}
