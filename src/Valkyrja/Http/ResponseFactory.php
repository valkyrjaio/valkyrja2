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

namespace Valkyrja\Http;

/**
 * Interface ResponseFactory.
 *
 * @author Melech Mizrachi
 */
interface ResponseFactory
{
    /**
     * Create a response.
     *
     * @param string|null $content    [optional] The response content
     * @param int|null    $statusCode [optional] The response status code
     * @param array|null  $headers    [optional] An array of response headers
     */
    public function createResponse(string $content = null, int $statusCode = null, array $headers = null): Response;

    /**
     * Create a JSON response.
     *
     * @param array|null $data       [optional] The data to set
     * @param int|null   $statusCode [optional] The response status code
     * @param array|null $headers    [optional] An array of response headers
     */
    public function createJsonResponse(array $data = null, int $statusCode = null, array $headers = null): JsonResponse;

    /**
     * Create a JSONP response.
     *
     * @param string     $callback   The jsonp callback
     * @param array|null $data       [optional] The data to set
     * @param int|null   $statusCode [optional] The response status code
     * @param array|null $headers    [optional] An array of response headers
     */
    public function createJsonpResponse(string $callback, array $data = null, int $statusCode = null, array $headers = null): JsonResponse;

    /**
     * Create a redirect response.
     *
     * @param string|null $uri        [optional] The uri to redirect to
     * @param int|null    $statusCode [optional] The response status code
     * @param array|null  $headers    [optional] An array of response headers
     */
    public function createRedirectResponse(string $uri = null, int $statusCode = null, array $headers = null): RedirectResponse;

    /**
     * Redirect to a named route response builder.
     *
     * @param string     $name       The name of the route
     * @param array|null $data       [optional] The data for dynamic routes
     * @param int|null   $statusCode [optional] The response status code
     * @param array|null $headers    [optional] An array of response headers
     */
    public function route(string $name, array $data = null, int $statusCode = null, array $headers = null): RedirectResponse;

    /**
     * View response builder.
     *
     * @param string     $template   The view template to use
     * @param array|null $data       [optional] The view data
     * @param int|null   $statusCode [optional] The response status code
     * @param array|null $headers    [optional] An array of response headers
     */
    public function view(string $template, array $data = null, int $statusCode = null, array $headers = null): Response;
}
