<?php

declare(strict_types = 1);

/*
 * This file is part of the Valkyrja framework.
 *
 * (c) Melech Mizrachi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valkyrja\Http;

use Throwable;
use Valkyrja\Application\Application;
use Valkyrja\Config\Enums\ConfigKeyPart;
use Valkyrja\Http\Events\HttpKernelHandled;
use Valkyrja\Http\Events\HttpKernelTerminate;
use Valkyrja\Http\Middleware\MiddlewareAwareTrait;
use Valkyrja\Routing\Route;
use Valkyrja\Routing\Router;
use Valkyrja\Support\Providers\Provides;

/**
 * Class NativeKernel.
 *
 * @author Melech Mizrachi
 */
class NativeKernel implements Kernel
{
    use MiddlewareAwareTrait;
    use Provides;

    /**
     * The application.
     *
     * @var Application
     */
    protected Application $app;

    /**
     * The router.
     *
     * @var Router
     */
    protected Router $router;

    /**
     * Kernel constructor.
     *
     * @param Application $application The application
     * @param Router      $router      The router
     */
    public function __construct(Application $application, Router $router)
    {
        $this->app    = $application;
        $this->router = $router;

        $config = $application->config()[ConfigKeyPart::ROUTING];

        self::$middleware       = $config[ConfigKeyPart::MIDDLEWARE];
        self::$middlewareGroups = $config[ConfigKeyPart::MIDDLEWARE_GROUPS];
    }

    /**
     * Handle a request.
     *
     * @param Request $request The request
     *
     * @throws Throwable
     *
     * @return Response
     */
    public function handle(Request $request): Response
    {
        try {
            $response = $this->dispatchRouter($request);
        } catch (Throwable $exception) {
            $response = $this->getExceptionResponse($exception);

            // Log the error
            $this->app->logger()->error((string) $exception);
        }

        // Dispatch the after request handled middleware and return the response
        $this->responseMiddleware($request, $response);

        // Trigger an event for kernel handled
        $this->app->events()->trigger(
            HttpKernelHandled::class,
            [
                new HttpKernelHandled($request, $response),
            ]
        );

        return $response;
    }

    /**
     * Dispatch the request via the router.
     *
     * @param Request $request The request
     *
     * @return Response
     */
    protected function dispatchRouter(Request $request): Response
    {
        // Set the request object in the container
        $this->app->container()->singleton(Request::class, $request);

        // Dispatch the before request handled middleware
        $middlewareReturn = $this->requestMiddleware($request);

        // If the middleware returned a response
        if ($middlewareReturn instanceof Response) {
            // Return the response
            return $middlewareReturn;
        }

        return $this->router->dispatch($request);
    }

    /**
     * Get a response from an exception.
     *
     * @param Throwable $exception The exception
     *
     * @throws Throwable
     *
     * @return Response
     */
    protected function getExceptionResponse(Throwable $exception): Response
    {
        return $this->app->exceptionHandler()->response($exception);
    }

    /**
     * Terminate the request.
     *
     * @param Request  $request  The request
     * @param Response $response The response
     *
     * @return void
     */
    public function terminate(Request $request, Response $response): void
    {
        // Dispatch the terminable middleware
        $this->terminableMiddleware($request, $response);

        // Terminate route middleware
        $this->terminateRouteMiddleware($request, $response);

        // Trigger an event for kernel terminate
        $this->app->events()->trigger(
            HttpKernelTerminate::class,
            [
                new HttpKernelTerminate($request, $response),
            ]
        );
    }

    /**
     * Run the kernel.
     *
     * @param Request $request The request
     *
     * @throws Throwable
     *
     * @return void
     */
    public function run(Request $request = null): void
    {
        // If no request was passed get the bootstrapped definition
        if (null === $request) {
            $request = $this->app->container()->getSingleton(Request::class);
        }

        // Handle the request, dispatch the after request middleware, and send the response
        $response = $this->handle($request)->send();

        // Terminate the application
        $this->terminate($request, $response);
    }

    /**
     * Get the application.
     *
     * @return Application
     */
    protected function getApplication(): Application
    {
        return $this->app;
    }

    /**
     * Terminate the found route's middleware.
     *
     * @param Request  $request  The request
     * @param Response $response The response
     *
     * @return void
     */
    protected function terminateRouteMiddleware(Request $request, Response $response): void
    {
        // Ensure a route exists
        if (! $this->app->container()->isSingleton(Route::class)) {
            return;
        }

        /* @var Route $route */
        $route = $this->app->container()->getSingleton(Route::class);

        // If the dispatched route has middleware
        if (null !== $route->getMiddleware()) {
            // Terminate each middleware
            $this->terminableMiddleware($request, $response, $route->getMiddleware());
        }
    }

    /**
     * The items provided by this provider.
     *
     * @return array
     */
    public static function provides(): array
    {
        return [
            Kernel::class,
        ];
    }

    /**
     * Publish the provider.
     *
     * @param Application $app The application
     *
     * @return void
     */
    public static function publish(Application $app): void
    {
        $app->container()->singleton(Kernel::class, new static($app, $app->router()));
    }
}
