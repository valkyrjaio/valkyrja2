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

namespace Valkyrja\Routing\Middleware;

use Valkyrja\Http\Request;
use Valkyrja\Http\Response;

/**
 * Trait MiddlewareAwareTrait.
 *
 * @author Melech Mizrachi
 */
trait MiddlewareAwareTrait
{
    /**
     * The collection of middleware.
     *
     * @var string[]
     */
    protected static array $middleware = [];

    /**
     * The collection of middleware groups.
     *
     * @var array[]
     */
    protected static array $middlewareGroups = [];

    /**
     * Determine if a middleware is a group of middleware.
     *
     * @param string $middleware The middleware to check
     */
    public function isMiddlewareGroup(string $middleware): bool
    {
        return isset(self::$middlewareGroups[$middleware]);
    }

    /**
     * Get a middleware group.
     *
     * @param string $middleware The middleware group
     *
     * @return string[]
     */
    public function getMiddlewareGroup(string $middleware): array
    {
        return self::$middlewareGroups[$middleware];
    }

    /**
     * Dispatch middleware.
     *
     * @param Request    $request    The request
     * @param array|null $middleware [optional] The middleware to dispatch
     */
    public function requestMiddleware(Request $request, array $middleware = null): Response|Request
    {
        // Set the middleware to any middleware passed or the base middleware
        $middleware = $middleware ?? static::$middleware;
        // Set the request
        $modifiedRequest = $request;

        // Iterate through the middleware
        foreach ($middleware as $item) {
            // If the middleware is a group
            if ($this->isMiddlewareGroup($item)) {
                // Recurse into that middleware group
                $modifiedRequest = $this->requestMiddleware($request, $this->getMiddlewareGroup($item));
            } else {
                /* @var Middleware $item */
                $modifiedRequest = $item::before($request);
            }

            // Check if the modified request is a response
            if ($modifiedRequest instanceof Response) {
                return $modifiedRequest;
            }
        }

        return $modifiedRequest;
    }

    /**
     * Dispatch after request processed middleware.
     *
     * @param Request    $request    The request
     * @param Response   $response   The response
     * @param array|null $middleware [optional] The middleware to dispatch
     */
    public function responseMiddleware(Request $request, Response $response, array $middleware = null): Response
    {
        // Set the middleware to any middleware passed or the base middleware
        $middleware = $middleware ?? static::$middleware;

        // Iterate through the middleware
        foreach ($middleware as $item) {
            // If the middleware is a group
            if ($this->isMiddlewareGroup($item)) {
                // Recurse into that middleware group
                $response = $this->responseMiddleware($request, $response, $this->getMiddlewareGroup($item));

                continue;
            }

            /* @var Middleware $item */
            $response = $item::after($request, $response);
        }

        return $response;
    }

    /**
     * Dispatch terminable middleware.
     *
     * @param Request    $request    The request
     * @param Response   $response   The response
     * @param array|null $middleware [optional] The middleware to dispatch
     */
    public function terminableMiddleware(Request $request, Response $response, array $middleware = null): void
    {
        // Set the middleware to any middleware passed or the base middleware
        $middleware = $middleware ?? static::$middleware;

        // Iterate through the middleware
        foreach ($middleware as $item) {
            // If the middleware is a group
            if ($this->isMiddlewareGroup($item)) {
                // Recurse into that middleware group
                $this->terminableMiddleware($request, $response, $this->getMiddlewareGroup($item));

                continue;
            }

            /* @var Middleware $item */
            $item::terminate($request, $response);
        }
    }
}
