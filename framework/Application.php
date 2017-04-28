<?php

/*
 * This file is part of the Valkyrja framework.
 *
 * (c) Melech Mizrachi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valkyrja;

use Throwable;

use Valkyrja\Config\Config;
use Valkyrja\Contracts\Application as ApplicationContract;
use Valkyrja\Contracts\Config\Env;
use Valkyrja\Contracts\Container\Container;
use Valkyrja\Contracts\Events\Events;
use Valkyrja\Contracts\Http\JsonResponse;
use Valkyrja\Contracts\Http\RedirectResponse;
use Valkyrja\Contracts\Http\Request;
use Valkyrja\Contracts\Http\Response;
use Valkyrja\Contracts\Http\ResponseBuilder;
use Valkyrja\Contracts\Logger\Logger;
use Valkyrja\Contracts\Routing\Router;
use Valkyrja\Contracts\View\View;
use Valkyrja\Debug\Debug;
use Valkyrja\Debug\ExceptionHandler;
use Valkyrja\Http\Exceptions\HttpException;
use Valkyrja\Http\Exceptions\HttpRedirectException;
use Valkyrja\Http\ResponseCode;

/**
 * Class Application
 *
 * @package Valkyrja
 *
 * @author  Melech Mizrachi
 */
class Application implements ApplicationContract
{
    /**
     * Get the instance of the application.
     *
     * @var \Valkyrja\Contracts\Application
     */
    protected static $app;

    /**
     * Application config
     *
     * @var \Valkyrja\Contracts\Config\Config
     */
    protected $config;

    /**
     * Get the instance of the container.
     *
     * @var \Valkyrja\Contracts\Container\Container
     */
    protected $container;

    /**
     * Get the instance of the events.
     *
     * @var \Valkyrja\Contracts\Events\Events
     */
    protected $events;

    /**
     * Is the app using a compiled version?
     *
     * @var bool
     */
    protected $isCompiled = false;

    /**
     * Application constructor.
     *
     * @param \Valkyrja\Contracts\Container\Container $container The container to use
     * @param \Valkyrja\Contracts\Events\Events       $events    The events to use
     * @param \Valkyrja\Config\Config                 $config    The config to use
     */
    public function __construct(Container $container, Events $events, Config $config)
    {
        // If debug is on, enable debug handling
        if ($config->app->debug) {
            // Debug to output exceptions
            Debug::enable(E_ALL, $config->app->debug);
        }

        // Set the app static
        static::$app = $this;

        // Set the container within the application
        $this->container = $container;
        // Set the events within the application
        $this->events = $events;
        // Set the config within the application
        $this->config = $config;

        // Set the application instance in the container
        $container->singleton(ApplicationContract::class, $this);
        // Set the events instance in the container
        $container->singleton(Events::class, $events);
        // Setup the container
        $container->setup();
        // Setup the router
        $this->router()->setup();

        // Set the timezone for the application to run within
        $this->setTimezone();
    }

    /**
     * Get the application instance.
     *
     * @return \Valkyrja\Contracts\Application
     */
    public static function app(): ApplicationContract
    {
        return static::$app;
    }

    /**
     * Get the container instance.
     *
     * @return \Valkyrja\Contracts\Container\Container
     */
    public function container(): Container
    {
        return $this->container;
    }

    /**
     * Get the events instance.
     *
     * @return \Valkyrja\Contracts\Events\Events
     */
    public function events(): Events
    {
        return $this->events;
    }

    /**
     * Get the application version.
     *
     * @return string
     */
    public function version(): string
    {
        return static::VERSION;
    }

    /**
     * Get the config class instance.
     *
     * @return \Valkyrja\Config\Config|\config\Config
     */
    public function config(): Config
    {
        return $this->config;
    }

    /**
     * Get environment variables.
     *
     * @return \Valkyrja\Contracts\Config\Env||config|Env
     */
    public function env(): Env
    {
        return $this->config()->env;
    }

    /**
     * Get the environment with which the application is running in.
     *
     * @return string
     */
    public function environment(): string
    {
        return $this->config()->app->env ?? 'production';
    }

    /**
     * Whether the application is running in debug mode or not.
     *
     * @return string
     */
    public function debug(): string
    {
        return $this->config()->app->debug ?? false;
    }

    /**
     * Is twig enabled?
     *
     * @return bool
     */
    public function isTwigEnabled(): bool
    {
        return $this->config()->views->twig->enabled ?? false;
    }

    /**
     * Set the timezone for the application process.
     *
     * @return void
     */
    public function setTimezone(): void
    {
        date_default_timezone_set($this->config()->app->timezone ?? 'UTC');
    }

    /**
     * Get whether the application is using a compiled version.
     *
     * @return bool
     */
    public function isCompiled(): bool
    {
        return $this->isCompiled;
    }

    /**
     * Set the application as using compiled.
     *
     * @return void
     */
    public function setCompiled(): void
    {
        $this->isCompiled = true;
    }

    /**
     * Abort the application due to error.
     *
     * @param int    $statusCode The status code to use
     * @param string $message    [optional] The Exception message to throw
     * @param array  $headers    [optional] The headers to send
     * @param int    $code       [optional] The Exception code
     *
     * @return void
     *
     * @throws \Valkyrja\Http\Exceptions\HttpException
     */
    public function abort(
        int $statusCode = ResponseCode::HTTP_NOT_FOUND,
        string $message = '',
        array $headers = [],
        int $code = 0
    ): void
    {
        throw new HttpException($statusCode, $message, null, $headers, $code);
    }

    /**
     * Redirect to a given uri, and abort the application.
     *
     * @param string $uri        [optional] The URI to redirect to
     * @param int    $statusCode [optional] The response status code
     * @param array  $headers    [optional] An array of response headers
     *
     * @return void
     *
     * @throws \Valkyrja\Http\Exceptions\HttpRedirectException
     */
    public function redirectTo(
        string $uri = null,
        int $statusCode = ResponseCode::HTTP_FOUND,
        array $headers = []
    ): void
    {
        throw new HttpRedirectException($statusCode, $uri, null, $headers, 0);
    }

    /**
     * Handle a request.
     *
     * @param \Valkyrja\Contracts\Http\Request $request The request
     *
     * @return \Valkyrja\Contracts\Http\Response
     *
     * @throws \Valkyrja\Http\Exceptions\HttpException
     */
    public function handle(Request $request): Response
    {
        try {
            $response = $this->router()->dispatch($request);
        }
        catch (Throwable $exception) {
            $handler = new ExceptionHandler($this->config()->app->debug);
            $response = $handler->getResponse($exception);
        }

        // Dispatch the request and return it
        return $response;
    }

    /**
     * Run the application.
     *
     * @return void
     *
     * @throws \Valkyrja\Http\Exceptions\HttpException
     */
    public function run(): void
    {
        /** @var Request $request */
        $request = $this->container()->get(Request::class);

        // Handle the request and send the response
        $this->handle($request)->send();
    }

    /**
     * Return the logger instance from the container.
     *
     * @return \Valkyrja\Contracts\Logger\Logger
     */
    public function logger(): Logger
    {
        return $this->container->get(Logger::class);
    }

    /**
     * Return the request instance from the container.
     *
     * @return \Valkyrja\Contracts\Http\Request
     */
    public function request(): Request
    {
        return $this->container->get(Request::class);
    }

    /**
     * Return the router instance from the container.
     *
     * @return \Valkyrja\Contracts\Routing\Router
     */
    public function router(): Router
    {
        return $this->container->get(Router::class);
    }

    /**
     * Return a new response from the application.
     *
     * @param string $content    [optional] The content to set
     * @param int    $statusCode [optional] The status code to set
     * @param array  $headers    [optional] The headers to set
     *
     * @return \Valkyrja\Contracts\Http\Response
     *
     * @throws \InvalidArgumentException
     */
    public function response(
        string $content = '',
        int $statusCode = ResponseCode::HTTP_OK,
        array $headers = []
    ): Response
    {
        /** @var Response $response */
        $response = $this->container->get(Response::class);

        if (func_num_args() === 0) {
            return $response;
        }

        return $response->create($content, $statusCode, $headers);
    }

    /**
     * Return a new json response from the application.
     *
     * @param array $data       [optional] An array of data
     * @param int   $statusCode [optional] The status code to set
     * @param array $headers    [optional] The headers to set
     *
     * @return \Valkyrja\Contracts\Http\JsonResponse
     *
     * @throws \InvalidArgumentException
     */
    public function json(
        array $data = [],
        int $statusCode = ResponseCode::HTTP_OK,
        array $headers = []
    ): JsonResponse
    {
        /** @var JsonResponse $response */
        $response = $this->container->get(JsonResponse::class);

        if (func_num_args() === 0) {
            return $response;
        }

        return $response->createJson('', $statusCode, $headers, $data);
    }

    /**
     * Return a new json response from the application.
     *
     * @param string $uri        [optional] The URI to redirect to
     * @param int    $statusCode [optional] The response status code
     * @param array  $headers    [optional] An array of response headers
     *
     * @return \Valkyrja\Contracts\Http\RedirectResponse
     *
     * @throws \InvalidArgumentException
     * @throws \Valkyrja\Http\Exceptions\InvalidStatusCodeException
     */
    public function redirect(
        string $uri = null,
        int $statusCode = ResponseCode::HTTP_FOUND,
        array $headers = []
    ): RedirectResponse
    {
        /** @var RedirectResponse $response */
        $response = $this->container->get(RedirectResponse::class);

        if (func_num_args() === 0) {
            return $response;
        }

        return $response->createRedirect($uri, $statusCode, $headers);
    }

    /**
     * Return a new redirect response from the application for a given route.
     *
     * @param string $route      The route to match
     * @param array  $parameters [optional] Any parameters to set for dynamic routes
     * @param int    $statusCode [optional] The response status code
     * @param array  $headers    [optional] An array of response headers
     *
     * @return \Valkyrja\Contracts\Http\RedirectResponse
     *
     * @throws \InvalidArgumentException
     * @throws \Valkyrja\Http\Exceptions\InvalidStatusCodeException
     */
    public function redirectRoute(
        string $route,
        array $parameters = [],
        int $statusCode = ResponseCode::HTTP_FOUND,
        array $headers = []
    ): RedirectResponse
    {
        // Get the uri from the router using the route and parameters
        $uri = $this->router()->routeUrl($route, $parameters);

        return $this->redirect($uri, $statusCode, $headers);
    }

    /**
     * Return a new response from the application.
     *
     * @return \Valkyrja\Contracts\Http\ResponseBuilder
     */
    public function responseBuilder(): ResponseBuilder
    {
        return $this->container->get(ResponseBuilder::class);
    }

    /**
     * Helper function to get a new view.
     *
     * @param string $template  [optional] The template to use
     * @param array  $variables [optional] The variables to use
     *
     * @return \Valkyrja\Contracts\View\View
     */
    public function view(string $template = '', array $variables = []): View
    {
        return $this->container->get(
            View::class,
            [
                $template,
                $variables,
            ]
        );
    }
}
