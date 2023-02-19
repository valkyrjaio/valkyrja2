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

use Closure;

/**
 * Interface Collector.
 *
 * @author Melech Mizrachi
 */
interface Collector
{
    /**
     * Get a router with a path context to group routes with.
     *
     * @param string $path The path
     */
    public function withPath(string $path): static;

    /**
     * Get a router with a controller context to group routes with.
     *
     * @param string $controller The controller
     */
    public function withController(string $controller): static;

    /**
     * Get a router with a name context to group routes with.
     *
     * @param string $name The name
     */
    public function withName(string $name): static;

    /**
     * Get a router with middleware context to group routes with.
     *
     * @param array $middleware The middleware
     */
    public function withMiddleware(array $middleware): static;

    /**
     * Get a router with a secure context to group routes with.
     *
     * @param bool $secure [optional] Whether to be secure
     */
    public function withSecure(bool $secure = true): static;

    /**
     * Group routes together.
     *
     * @param Closure $group The group
     */
    public function group(Closure $group): static;

    /**
     * Helper method to set a GET route.
     * <code>
     *      get('/', 'Controller->property')
     *      get('/', 'Controller::property')
     *      get('/', 'Controller->method()')
     *      get('/', 'Controller::method()')
     *      get('/', 'function()')
     *      get('/', Closure)
     * </code>.
     *
     * @param string         $path            The path
     * @param Closure|string $handler         The handler
     * @param string|null    $name            [optional] The name of the route
     * @param bool           $setDependencies [optional] Whether to dynamically set dependencies
     */
    public function get(string $path, Closure|string $handler, string $name = null, bool $setDependencies = false): Route;

    /**
     * Helper method to set a POST route.
     * <code>
     *      post('/', 'Controller->property')
     *      post('/', 'Controller::property')
     *      post('/', 'Controller->method()')
     *      post('/', 'Controller::method()')
     *      post('/', 'function()')
     *      post('/', Closure)
     * </code>.
     *
     * @param string         $path            The path
     * @param Closure|string $handler         The handler
     * @param string|null    $name            [optional] The name of the route
     * @param bool           $setDependencies [optional] Whether to dynamically set dependencies
     */
    public function post(string $path, Closure|string $handler, string $name = null, bool $setDependencies = false): Route;

    /**
     * Helper method to set a PUT route.
     * <code>
     *      put('/', 'Controller->property')
     *      put('/', 'Controller::property')
     *      put('/', 'Controller->method()')
     *      put('/', 'Controller::method()')
     *      put('/', 'function()')
     *      put('/', Closure)
     * </code>.
     *
     * @param string         $path            The path
     * @param Closure|string $handler         The handler
     * @param string|null    $name            [optional] The name of the route
     * @param bool           $setDependencies [optional] Whether to dynamically set dependencies
     */
    public function put(string $path, Closure|string $handler, string $name = null, bool $setDependencies = false): Route;

    /**
     * Helper method to set a PATCH route.
     * <code>
     *      patch('/', 'Controller->property')
     *      patch('/', 'Controller::property')
     *      patch('/', 'Controller->method()')
     *      patch('/', 'Controller::method()')
     *      patch('/', 'function()')
     *      patch('/', Closure)
     * </code>.
     *
     * @param string         $path            The path
     * @param Closure|string $handler         The handler
     * @param string|null    $name            [optional] The name of the route
     * @param bool           $setDependencies [optional] Whether to dynamically set dependencies
     */
    public function patch(string $path, Closure|string $handler, string $name = null, bool $setDependencies = false): Route;

    /**
     * Helper method to set a DELETE route.
     * <code>
     *      delete('/', 'Controller->property')
     *      delete('/', 'Controller::property')
     *      delete('/', 'Controller->method()')
     *      delete('/', 'Controller::method()')
     *      delete('/', 'function()')
     *      delete('/', Closure)
     * </code>.
     *
     * @param string         $path            The path
     * @param Closure|string $handler         The handler
     * @param string|null    $name            [optional] The name of the route
     * @param bool           $setDependencies [optional] Whether to dynamically set dependencies
     */
    public function delete(string $path, Closure|string $handler, string $name = null, bool $setDependencies = false): Route;

    /**
     * Helper method to set a HEAD route.
     * <code>
     *      head('/', 'Controller->property')
     *      head('/', 'Controller::property')
     *      head('/', 'Controller->method()')
     *      head('/', 'Controller::method()')
     *      head('/', 'function()')
     *      head('/', Closure)
     * </code>.
     *
     * @param string         $path            The path
     * @param Closure|string $handler         The handler
     * @param string|null    $name            [optional] The name of the route
     * @param bool           $setDependencies [optional] Whether to dynamically set dependencies
     */
    public function head(string $path, Closure|string $handler, string $name = null, bool $setDependencies = false): Route;

    /**
     * Helper method to set any request method route.
     * <code>
     *      any('/', 'Controller->property')
     *      any('/', 'Controller::property')
     *      any('/', 'Controller->method()')
     *      any('/', 'Controller::method()')
     *      any('/', 'function()')
     *      any('/', Closure)
     * </code>.
     *
     * @param string         $path            The path
     * @param Closure|string $handler         The handler
     * @param string|null    $name            [optional] The name of the route
     * @param bool           $setDependencies [optional] Whether to dynamically set dependencies
     */
    public function any(string $path, Closure|string $handler, string $name = null, bool $setDependencies = false): Route;

    /**
     * Helper method to set any request method route.
     * <code>
     *      redirect('/', '/to', ['GET'], 301, true)
     * </code>.
     *
     * @param string      $path    The path
     * @param string      $to      The path to redirect to
     * @param array|null  $methods [optional] The request methods
     * @param string|null $name    [optional] The name of the route
     */
    public function redirect(string $path, string $to, array $methods = null, string $name = null): Route;
}
