<?php

/*
 * This file is part of the Valkyrja framework.
 *
 * (c) Melech Mizrachi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valkyrja\Contracts\Http;

use Valkyrja\Contracts\Application;

/**
 * Interface ResponseBuilder
 *
 * @package Valkyrja\Contracts\Http
 *
 * @author  Melech Mizrachi
 */
interface ResponseBuilder
{
    /**
     * ResponseBuilder constructor.
     *
     * @param \Valkyrja\Contracts\Application $app
     */
    public function __construct(Application $app);

    /**
     * Make a new instance of Response.
     *
     * @param mixed $content [optional] The response content
     * @param int   $status  [optional] The response status code
     * @param array $headers [optional] An array of response headers
     *
     * @return \Valkyrja\Contracts\Http\Response
     */
    public function make(string $content = '', int $status = Response::HTTP_OK, array $headers = []): Response;

    /**
     * View response builder.
     *
     * @param string $template The view template to use
     * @param array  $data     [optional] The view data
     * @param int    $status   [optional] The response status code
     * @param array  $headers  [optional] An array of response headers
     *
     * @return \Valkyrja\Contracts\Http\Response
     */
    public function view(
        string $template,
        array $data = [],
        int $status = Response::HTTP_OK,
        array $headers = []
    ): Response;

    /**
     * Json response builder.
     *
     * @param array $data    [optional] The data to set
     * @param int   $status  [optional] The response status code
     * @param array $headers [optional] An array of response headers
     *
     * @return \Valkyrja\Contracts\Http\JsonResponse
     */
    public function json(
        array $data = [],
        int $status = Response::HTTP_OK,
        array $headers = []
    ): JsonResponse;

    /**
     * JsonP response builder.
     *
     * @param string $callback The jsonp callback
     * @param array  $data     [optional] The data to set
     * @param int    $status   [optional] The response status code
     * @param array  $headers  [optional] An array of response headers
     *
     * @return \Valkyrja\Contracts\Http\JsonResponse
     */
    public function jsonp(
        string $callback,
        array $data = [],
        int $status = Response::HTTP_OK,
        array $headers = []
    ): JsonResponse;

    /**
     * Redirect to response builder.
     *
     * @param string $uri     [optional] The uri to redirect to
     * @param int    $status  [optional] The response status code
     * @param array  $headers [optional] An array of response headers
     *
     * @return \Valkyrja\Contracts\Http\RedirectResponse
     */
    public function redirect(
        string $uri = '/',
        int $status = Response::HTTP_FOUND,
        array $headers = []
    ): RedirectResponse;

    /**
     * Redirect to a named route response builder.
     *
     * @param string $route      The route to match
     * @param array  $parameters [optional] Any parameters to set for dynamic routes
     * @param int    $status     [optional] The response status code
     * @param array  $headers    [optional] An array of response headers
     *
     * @return \Valkyrja\Contracts\Http\RedirectResponse
     */
    public function route(
        string $route,
        array $parameters = [],
        int $status = Response::HTTP_FOUND,
        array $headers = []
    ): RedirectResponse;
}
