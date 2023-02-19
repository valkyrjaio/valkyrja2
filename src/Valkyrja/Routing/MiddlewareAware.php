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

use Valkyrja\Http\Request;
use Valkyrja\Http\Response;

/**
 * Interface MiddlewareAware.
 *
 * @author Melech Mizrachi
 */
interface MiddlewareAware
{
    /**
     * Determine if a middleware is a group of middleware.
     *
     * @param string $middleware The middleware to check
     */
    public function isMiddlewareGroup(string $middleware): bool;

    /**
     * Get a middleware group.
     *
     * @param string $middleware The middleware group
     *
     * @return string[]
     */
    public function getMiddlewareGroup(string $middleware): array;

    /**
     * Dispatch middleware.
     *
     * @param Request    $request    The request
     * @param array|null $middleware [optional] The middleware to dispatch
     */
    public function requestMiddleware(Request $request, array $middleware = null): Response|Request;

    /**
     * Dispatch after request processed middleware.
     *
     * @param Request    $request    The request
     * @param Response   $response   The response
     * @param array|null $middleware [optional] The middleware to dispatch
     */
    public function responseMiddleware(Request $request, Response $response, array $middleware = null): Response;

    /**
     * Dispatch terminable middleware.
     *
     * @param Request    $request    The request
     * @param Response   $response   The response
     * @param array|null $middleware [optional] The middleware to dispatch
     */
    public function terminableMiddleware(Request $request, Response $response, array $middleware = null): void;
}
