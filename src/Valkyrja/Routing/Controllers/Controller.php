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

namespace Valkyrja\Routing\Controllers;

use Valkyrja\Container\Container;
use Valkyrja\Http\Request;
use Valkyrja\Http\ResponseFactory;
use Valkyrja\Routing\Route;
use Valkyrja\Routing\Router;

/**
 * Abstract Class Controller.
 *
 * @author Melech Mizrachi
 */
abstract class Controller
{
    /**
     * The container.
     */
    private static Container $container;

    /**
     * The request.
     */
    private static Request $request;

    /**
     * The response factory.
     */
    private static ResponseFactory $responseFactory;

    /**
     * The router.
     */
    private static Router $router;

    /**
     * The route.
     */
    private static Route $route;

    /**
     * Get the container service.
     */
    protected static function getContainer(): Container
    {
        return self::$container ??= \Valkyrja\container();
    }

    /**
     * The request.
     */
    protected static function getRequest(): Request
    {
        return self::$request ??= self::getContainer()->getSingleton(Request::class);
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
     */
    protected static function getRoute(): Route
    {
        return self::$route ??= self::getContainer()->getSingleton(Route::class);
    }
}
