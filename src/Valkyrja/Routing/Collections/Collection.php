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
use Valkyrja\Http\Constants\RequestMethod;
use Valkyrja\Routing\Collection as Contract;
use Valkyrja\Routing\Route;

use function array_merge;
use function assert;
use function is_array;
use function is_string;

/**
 * Class Collection.
 *
 * @author Melech Mizrachi
 */
class Collection implements Contract
{
    /**
     * The routes.
     *
     * @var Route[]
     */
    protected array $routes = [];

    /**
     * The static routes.
     *
     * @var string[][]
     */
    protected array $static = [];

    /**
     * The dynamic routes.
     *
     * @var string[][]
     */
    protected array $dynamic = [];

    /**
     * The named routes.
     *
     * @var string[]
     */
    protected array $named = [];

    /**
     * @inheritDoc
     */
    public function add(Route $route): void
    {
        assert($route->getPath());

        // Set the route to its request methods
        $this->setRouteToRequestMethods($route);
        // Set the route to the named
        $this->setRouteToNamed($route);

        $this->routes[(string) $route->getId()] = $route;
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function get(string $path, string $method = null): ?Route
    {
        return $this->getStatic($path, $method) ?? $this->getDynamic($path, $method);
    }

    /**
     * @inheritDoc
     */
    public function isset(string $path, string $method = null): bool
    {
        return $this->hasStatic($path, $method) || $this->hasDynamic($path, $method);
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->ensureMethodRoutes(array_merge($this->static, $this->dynamic));
    }

    /**
     * @inheritDoc
     */
    public function allFlattened(): array
    {
        return $this->ensureRoutes($this->routes);
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function getStatic(string $path, string $method = null): ?Route
    {
        return $this->getOfType($this->static, $path, $method);
    }

    /**
     * @inheritDoc
     */
    public function hasStatic(string $path, string $method = null): bool
    {
        return $this->hasOfType($this->static, $path, $method);
    }

    /**
     * @inheritDoc
     */
    public function allStatic(string $method = null): array
    {
        return $this->allOfType($this->static, $method);
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function getDynamic(string $regex, string $method = null): ?Route
    {
        return $this->getOfType($this->dynamic, $regex, $method);
    }

    /**
     * @inheritDoc
     */
    public function hasDynamic(string $regex, string $method = null): bool
    {
        return $this->hasOfType($this->dynamic, $regex, $method);
    }

    /**
     * @inheritDoc
     */
    public function allDynamic(string $method = null): array
    {
        return $this->allOfType($this->dynamic, $method);
    }

    /**
     * @inheritDoc
     */
    public function getNamed(string $name): ?Route
    {
        return $this->ensureRoute($this->named[$name] ?? null);
    }

    /**
     * @inheritDoc
     */
    public function hasNamed(string $name): bool
    {
        return isset($this->named[$name]);
    }

    /**
     * @inheritDoc
     */
    public function allNamed(): array
    {
        return $this->ensureRoutes($this->named);
    }

    /**
     * Set a route to its request methods.
     *
     * @param Route $route The route
     */
    protected function setRouteToRequestMethods(Route $route): void
    {
        foreach ($route->getMethods() as $requestMethod) {
            $this->setRouteToRequestMethod($route, $requestMethod);
        }
    }

    /**
     * Set the route to the request method.
     *
     * @param Route  $route         The route
     * @param string $requestMethod The request method
     */
    protected function setRouteToRequestMethod(Route $route, string $requestMethod): void
    {
        $id = $route->getId();

        assert($id !== null);

        // If this is a dynamic route
        if ($route->isDynamic()) {
            $regex = $route->getRegex();

            assert(is_string($regex));

            // Set the route in the dynamic routes list
            $this->dynamic[$requestMethod][$regex] = $id;
        } // Otherwise set it in the static routes array
        else {
            // Set the route in the static routes list
            $this->static[$requestMethod][$route->getPath()] = $id;
        }
    }

    /**
     * Set the route to the named.
     *
     * @param Route $route The route
     */
    protected function setRouteToNamed(Route $route): void
    {
        // If this route has a name set
        if (($name = $route->getName()) !== null && ($id = $route->getId()) !== null) {
            // Set the route in the named routes list
            $this->named[$name] = $id;
        }
    }

    /**
     * Get a route of type (static|dynamic).
     *
     * @param array       $type   The type [static|dynamic]
     * @param string      $path   The path
     * @param string|null $method [optional] The request method
     *
     * @throws JsonException
     */
    protected function getOfType(array $type, string $path, string $method = null): ?Route
    {
        if ($method === null) {
            return $this->getAnyOfType($type, $path);
        }

        return $this->ensureRoute($type[$method][$path] ?? null);
    }

    /**
     * Get a route of any type (static|dynamic).
     *
     * @param array  $type The type [static|dynamic]
     * @param string $path The path
     *
     * @throws JsonException
     */
    protected function getAnyOfType(array $type, string $path): ?Route
    {
        return $this->getOfType($type, $path, RequestMethod::GET)
            ?? $this->getOfType($type, $path, RequestMethod::HEAD)
            ?? $this->getOfType($type, $path, RequestMethod::POST)
            ?? $this->getOfType($type, $path, RequestMethod::PUT)
            ?? $this->getOfType($type, $path, RequestMethod::PATCH)
            ?? $this->getOfType($type, $path, RequestMethod::DELETE);
    }

    /**
     * Has a path of type (static|dynamic).
     *
     * @param array       $type   The type [static|dynamic]
     * @param string      $path   The path
     * @param string|null $method [optional] The request method
     */
    protected function hasOfType(array $type, string $path, string $method = null): bool
    {
        if ($method === null) {
            return $this->hasAnyOfType($type, $path);
        }

        return isset($type[$method][$path]);
    }

    /**
     * Has a path of any type.
     *
     * @param array  $type The type [static|dynamic]
     * @param string $path The path
     */
    protected function hasAnyOfType(array $type, string $path): bool
    {
        foreach ($type as $requestMethod) {
            if (isset($requestMethod[$path])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all of type with optional by request method.
     *
     * @param array       $type   The type [static|dynamic]
     * @param string|null $method [optional] The request method
     *
     * @return array<string, Route>
     */
    protected function allOfType(array $type, string $method = null): array
    {
        if ($method === null) {
            return $this->ensureMethodRoutes($type);
        }

        return $this->ensureRoutes($type[$method] ?? []);
    }

    /**
     * Ensure request methods are arrays of routes.
     */
    protected function ensureMethodRoutes(array $methodsArray): array
    {
        foreach ($methodsArray as $method => $routes) {
            $methodsArray[$method] = $this->ensureRoutes($routes);
        }

        return $methodsArray;
    }

    /**
     * Ensure an array is an array of routes.
     *
     * @param array $routesArray The routes array
     */
    protected function ensureRoutes(array $routesArray): array
    {
        $routes = [];

        foreach ($routesArray as $key => $route) {
            $routes[$key] = $this->ensureRoute($route);
        }

        return $routes;
    }

    /**
     * Ensure a route, or null, is returned.
     *
     * @param Route|array|string|null $route The route
     */
    protected function ensureRoute(Route|array|string $route = null): ?Route
    {
        if (is_string($route)) {
            $route = $this->routes[$route];
        }

        if (is_array($route)) {
            return \Valkyrja\Routing\Models\Route::fromArray($route);
        }

        return $route;
    }
}
