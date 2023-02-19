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

namespace Valkyrja\Routing;

use Valkyrja\Http\Exceptions\HttpException;
use Valkyrja\Http\Request;
use Valkyrja\Http\Response;
use Valkyrja\Routing\Config\Config;
use Valkyrja\Routing\Exceptions\InvalidRouteName;

/**
 * Interface Router.
 *
 * @author Melech Mizrachi
 */
interface Router extends MiddlewareAware
{
    /**
     * Get the config.
     */
    public function getConfig(): Config|array;

    /**
     * Whether to run in debug.
     */
    public function debug(): bool;

    /**
     * Get the route collection.
     */
    public function getCollection(): Collection;

    /**
     * Get the route matcher.
     */
    public function getMatcher(): Matcher;

    /**
     * Set a single route.
     */
    public function addRoute(Route $route): void;

    /**
     * Get all routes set by the application.
     *
     * @return Route[]
     */
    public function getRoutes(): array;

    /**
     * Get a route by name.
     *
     * @param string $name The name of the route to get
     *
     * @throws InvalidRouteName
     */
    public function getRoute(string $name): Route;

    /**
     * Determine whether a route name exists.
     *
     * @param string $name The name of the route
     */
    public function hasRoute(string $name): bool;

    /**
     * Get a route from a request.
     *
     * @param Request $request The request
     *
     * @throws HttpException
     */
    public function getRouteFromRequest(Request $request): Route;

    /**
     * Dispatch the route and find a match.
     *
     * @param Request $request The request
     */
    public function dispatch(Request $request): Response;
}
