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

/**
 * Interface Collection.
 *
 * @author Melech Mizrachi
 */
interface Collection
{
    /**
     * Add a route.
     *
     * @param Route $route The route
     */
    public function add(Route $route): void;

    /**
     * Get a route.
     *
     * @param string      $path   The path
     * @param string|null $method [optional] The request method
     *
     * @return Route|null
     *                    The route if found or null when no route is
     *                    found for the path combination specified
     */
    public function get(string $path, string $method = null): ?Route;

    /**
     * Determine if a route exists.
     *
     * @param string      $path   The path
     * @param string|null $method [optional] The request method
     */
    public function isset(string $path, string $method = null): bool;

    /**
     * Get all routes.
     *
     * @return Route[][]
     */
    public function all(): array;

    /**
     * Get a flat array of routes.
     *
     * @return Route[]
     */
    public function allFlattened(): array;

    /**
     * Get a static route.
     *
     * @param string      $path   The path
     * @param string|null $method [optional] The request method
     *
     * @return Route|null
     *                    The route if found or null when no static route is
     *                    found for the path and method combination specified
     */
    public function getStatic(string $path, string $method = null): ?Route;

    /**
     * Determine if a static route exists.
     *
     * @param string      $path   The path
     * @param string|null $method [optional] The request method
     */
    public function hasStatic(string $path, string $method = null): bool;

    /**
     * Get static routes of a certain request method.
     *
     * @param string|null $method [optional] The request method
     *
     * @return array<string, Route>|array<string, array<string, Route>>
     */
    public function allStatic(string $method = null): array;

    /**
     * Get a dynamic route.
     *
     * @param string      $regex  The regex
     * @param string|null $method [optional] The request method
     *
     * @return Route|null
     *                    The route if found or null when no dynamic route is
     *                    found for the path and method combination specified
     */
    public function getDynamic(string $regex, string $method = null): ?Route;

    /**
     * Determine if a dynamic route exists.
     *
     * @param string      $regex  The regex
     * @param string|null $method [optional] The request method
     */
    public function hasDynamic(string $regex, string $method = null): bool;

    /**
     * Get the dynamic routes in this collection.
     *
     * @return array<string, Route>|array<string, array<string, Route>>
     */
    public function allDynamic(string $method = null): array;

    /**
     * Get a named route.
     *
     * @param string $name The name
     *
     * @return Route|null
     *                    The route if found or null when no named route is
     *                    found for the path and method combination specified
     */
    public function getNamed(string $name): ?Route;

    /**
     * Determine if a named route exists.
     *
     * @param string $name The name
     */
    public function hasNamed(string $name): bool;

    /**
     * Get the named routes in this collection.
     *
     * @return string[]
     */
    public function allNamed(): array;
}
