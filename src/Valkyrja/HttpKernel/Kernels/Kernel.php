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

namespace Valkyrja\HttpKernel\Kernels;

use Throwable;
use Valkyrja\Container\Container;
use Valkyrja\Event\Events;
use Valkyrja\Http\Constants\StatusCode;
use Valkyrja\Http\Exceptions\HttpException;
use Valkyrja\Http\Request;
use Valkyrja\Http\Response;
use Valkyrja\Http\ResponseFactory;
use Valkyrja\HttpKernel\Events\RequestHandled;
use Valkyrja\HttpKernel\Events\RequestTerminating;
use Valkyrja\HttpKernel\Kernel as Contract;
use Valkyrja\Log\Logger;
use Valkyrja\Routing\Config\Config;
use Valkyrja\Routing\Middleware\MiddlewareAwareTrait;
use Valkyrja\Routing\Route;
use Valkyrja\Routing\Router;

use function count;
use function defined;
use function function_exists;

use const PHP_OUTPUT_HANDLER_CLEANABLE;
use const PHP_OUTPUT_HANDLER_FLUSHABLE;
use const PHP_OUTPUT_HANDLER_REMOVABLE;
use const PHP_SAPI;

/**
 * Class Kernel.
 *
 * @author Melech Mizrachi
 */
class Kernel implements Contract
{
    use MiddlewareAwareTrait;

    /**
     * The request.
     */
    protected Request $request;

    /**
     * The errors template directory.
     */
    protected string $errorsTemplateDir = 'errors';

    /**
     * Kernel constructor.
     *
     * @param Container    $container The container
     * @param Events       $events    The events
     * @param Router       $router    The router
     * @param Config|array $config    The config
     * @param bool         $debug     [optional] Whether to run in debug
     */
    public function __construct(
        protected Container $container,
        protected Events $events,
        protected Router $router,
        protected Config|array $config,
        protected bool $debug = false
    ) {
        self::$middleware       = $config['middleware'];
        self::$middlewareGroups = $config['middlewareGroups'];
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable
     */
    public function handle(Request $request): Response
    {
        $this->request = $request;

        try {
            $response = $this->dispatchRouter($request);

            // Dispatch the after request handled middleware and return the response
            $response = $this->responseMiddleware($request, $response);
        } catch (Throwable $exception) {
            $response = $this->getExceptionResponse($exception);

            // Log the error
            $this->logException($exception);
        }

        // Set the returned response in the container
        $this->container->setSingleton(Response::class, $response);
        // Trigger an event for the request having been handled
        $this->events->trigger(RequestHandled::class, [$request, $response]);

        return $response;
    }

    /**
     * @inheritDoc
     */
    public function send(Response $response): static
    {
        $response->send();

        $this->finishSession();
        $this->finishRequest();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function terminate(Request $request, Response $response): void
    {
        try {
            // Trigger an event for the request being terminated
            $this->events->trigger(RequestTerminating::class, [$request, $response]);
            // Dispatch the terminable middleware
            $this->terminableMiddleware($request, $response);
        } catch (Throwable $exception) {
            $this->logException($exception);
        }

        // If a route was dispatched
        if ($this->container->has(Route::class)) {
            // Terminate the route middleware
            $this->terminateRoute($request, $response);
        }
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable
     */
    public function run(Request $request): void
    {
        // Handle the request, dispatch the after request middleware
        $response = $this->handle($request);

        // Send the response
        $this->send($response);
        // Terminate the application
        $this->terminate($request, $response);
    }

    /**
     * Dispatch the request via the router.
     *
     * @param Request $request The request
     */
    protected function dispatchRouter(Request $request): Response
    {
        // Set the request object in the container
        $this->container->setSingleton(Request::class, $request);

        // Dispatch the before request handled middleware
        $requestAfterMiddleware = $this->requestMiddleware($request);

        // If the return value after middleware is a response return it
        if ($requestAfterMiddleware instanceof Response) {
            return $requestAfterMiddleware;
        }

        // Set the returned request in the container
        $this->container->setSingleton(Request::class, $requestAfterMiddleware);

        return $this->router->dispatch($requestAfterMiddleware);
    }

    /**
     * Get a response from an exception.
     *
     * @param Throwable $exception The exception
     *
     * @throws Throwable
     */
    protected function getExceptionResponse(Throwable $exception): Response
    {
        if ($this->debug) {
            // Log the error
            $this->logException($exception);

            throw $exception;
        }

        // If no response has been set and there is a template with the error code
        if ($exception instanceof HttpException) {
            return $this->getHttpExceptionResponse($exception);
        }

        return $this->getResponseFactory()
            ->view(
                "$this->errorsTemplateDir/500",
                null,
                StatusCode::INTERNAL_SERVER_ERROR
            );
    }

    /**
     * Get an http exception response.
     *
     * @param HttpException $exception The http exception
     */
    protected function getHttpExceptionResponse(HttpException $exception): Response
    {
        $responseFactory = $this->getResponseFactory();

        try {
            // Set the response as the error template
            return $exception->getResponse()
                ?? $responseFactory->view(
                    $this->errorsTemplateDir . '/' . $exception->getStatusCode(),
                    null,
                    $exception->getStatusCode()
                );
        } catch (Throwable) {
            return $responseFactory->view(
                "$this->errorsTemplateDir/error",
                [
                    'exception' => $exception,
                ],
                $exception->getStatusCode()
            );
        }
    }

    /**
     * Get the response factory.
     */
    protected function getResponseFactory(): ResponseFactory
    {
        return $this->container->getSingleton(ResponseFactory::class);
    }

    /**
     * Log an error.
     */
    protected function logException(Throwable $exception): void
    {
        /** @var Logger $logger */
        $logger     = $this->container->getSingleton(Logger::class);
        $url        = $this->request->getUri()->getPath();
        $logMessage = "Kernel Error\nUrl: $url";

        $logger->exception($exception, $logMessage);
    }

    /**
     * Terminate a route's middleware.
     *
     * @param Request  $request  The request
     * @param Response $response The response
     */
    protected function terminateRoute(Request $request, Response $response): void
    {
        try {
            /* @var Route $route */
            $route = $this->container->getSingleton(Route::class);

            // If the dispatched route has middleware
            if ($route->getMiddleware() !== null) {
                // Terminate each middleware
                $this->terminableMiddleware($request, $response, $route->getMiddleware());
            }
        } catch (Throwable $exception) {
            $this->logException($exception);
        }
    }

    /**
     * Finish a session if it is active.
     */
    protected function finishSession(): void
    {
        if (session_id()) {
            session_write_close();
        }
    }

    /**
     * Finish the request.
     */
    protected function finishRequest(): void
    {
        // If fastcgi is enabled
        if (function_exists('fastcgi_finish_request')) {
            // Use it to finish the request
            fastcgi_finish_request();
        } // Otherwise if this isn't a cli request
        elseif ('cli' !== PHP_SAPI) {
            // Use an internal method to finish the request
            $this->closeOutputBuffers(0, true);
        }
    }

    /**
     * Cleans or flushes output buffers up to target level.
     * Resulting level can be greater than target level if a non-removable
     * buffer has been encountered.
     *
     * @param int  $targetLevel The target output buffering level
     * @param bool $flush       Whether to flush or clean the buffers
     */
    protected function closeOutputBuffers(int $targetLevel, bool $flush): void
    {
        $status = ob_get_status(true);
        $level  = count($status);
        // PHP_OUTPUT_HANDLER_* are not defined on HHVM 3.3
        $flags = defined('PHP_OUTPUT_HANDLER_REMOVABLE')
            ? PHP_OUTPUT_HANDLER_REMOVABLE | ($flush
                ? PHP_OUTPUT_HANDLER_FLUSHABLE
                : PHP_OUTPUT_HANDLER_CLEANABLE)
            : -1;

        while (
            $level-- > $targetLevel
            && ($s = $status[$level])
            && ($s['del'] ?? (! isset($s['flags']) || $flags === ($s['flags'] & $flags)))
        ) {
            if ($flush) {
                ob_end_flush();
            } else {
                ob_end_clean();
            }
        }
    }
}
