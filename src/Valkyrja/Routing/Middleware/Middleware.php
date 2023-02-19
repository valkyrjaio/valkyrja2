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

use Valkyrja\Container\Container;
use Valkyrja\Http\Request;
use Valkyrja\Http\Response;
use Valkyrja\Http\ResponseFactory;
use Valkyrja\Routing\Route;
use Valkyrja\Routing\Router;

/**
 * Abstract Class Middleware.
 *
 * @author Melech Mizrachi
 */
abstract class Middleware
{
    /**
     * The container.
     */
    private static Container $container;

    /**
     * The response factory.
     */
    private static ResponseFactory $responseFactory;

    /**
     * The router.
     */
    private static Router $router;

    /**
     * The matched route.
     */
    public static Route $route;

    /**
     * Middleware handler for before a request is dispatched.
     *
     * @param Request $request The request
     */
    public static function before(Request $request): Request|Response
    {
        // Do logic using the request before it is processed by the controller action, here

        return $request;
    }

    /**
     * Middleware handler for after a request is dispatched.
     *
     * @param Request  $request  The request
     * @param Response $response The response
     */
    public static function after(Request $request, Response $response): Response
    {
        // Modify the response after its been processed by the controller action, here

        return $response;
    }

    /**
     * Middleware handler run when the application is terminating.
     *
     * @param Request  $request  The request
     * @param Response $response The response
     */
    public static function terminate(Request $request, Response $response): void
    {
        // Do stuff after termination (after the response has been sent) here
    }

    /**
     * Get the container service.
     */
    protected static function getContainer(): Container
    {
        return self::$container ??= \Valkyrja\container();
    }

    /**
     * Get the response factory service.
     */
    protected static function getResponseFactory(): ResponseFactory
    {
        return self::$responseFactory ??= self::getContainer()->getSingleton(ResponseFactory::class);
    }

    /**
     * Get the router service.
     */
    protected static function getRouter(): Router
    {
        return self::$router ??= self::getContainer()->getSingleton(Router::class);
    }

    /**
     * Get the matched route.
     *
     *  NOTE: This will only be instantiated and available to middlewares' before methods that are set to a route, and
     *  not to global middleware that run on every request. It will be available for all middlewares' after and
     *  terminate assuming a route was matched
     */
    protected static function getRoute(): ?Route
    {
        if (self::getContainer()->isSingleton(Route::class)) {
            return self::$route ??= self::getContainer()->getSingleton(Route::class);
        }

        return null;
    }
}
