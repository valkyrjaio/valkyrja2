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

namespace Valkyrja\Routing\Support;

use Valkyrja\Http\Constants\StatusCode;
use Valkyrja\Http\Constants\StatusText;
use Valkyrja\Http\Exceptions\HttpException;
use Valkyrja\Http\Exceptions\HttpRedirectException;
use Valkyrja\Http\Response;

/**
 * Class Abort.
 *
 * @author Melech Mizrachi
 */
class Abort
{
    /**
     * Abort with a 404.
     *
     * @param array|null    $headers  [optional] The headers
     * @param Response|null $response [optional] The response to send
     */
    public static function abort404(array $headers = null, Response $response = null): never
    {
        static::abort(StatusCode::NOT_FOUND, StatusText::NOT_FOUND, $headers, $response);
    }

    /**
     * Abort with a 405.
     *
     * @param array|null    $headers  [optional] The headers
     * @param Response|null $response [optional] The response to send
     */
    public static function abort405(array $headers = null, Response $response = null): never
    {
        static::abort(StatusCode::METHOD_NOT_ALLOWED, StatusText::METHOD_NOT_ALLOWED, $headers, $response);
    }

    /**
     * Abort.
     *
     * @param int|null      $statusCode [optional] The status code
     * @param string|null   $message    [optional] The message
     * @param array|null    $headers    [optional] The headers
     * @param Response|null $response   [optional] The response to send
     */
    public static function abort(
        int $statusCode = null,
        string $message = null,
        array $headers = null,
        Response $response = null
    ): never {
        throw new HttpException($statusCode, $message, $headers, $response);
    }

    /**
     * Abort with a response.
     *
     * @param Response $response The response
     */
    public static function response(Response $response): never
    {
        static::abort(null, null, null, $response);
    }

    /**
     * Redirect to a given uri, and abort.
     *
     * @param string|null $uri        [optional] The URI to redirect to
     * @param int|null    $statusCode [optional] The response status code
     * @param array|null  $headers    [optional] An array of response headers
     *
     * @throws HttpRedirectException
     */
    public static function redirect(string $uri = null, int $statusCode = null, array $headers = null): never
    {
        throw new HttpRedirectException($statusCode, $uri, $headers);
    }
}
