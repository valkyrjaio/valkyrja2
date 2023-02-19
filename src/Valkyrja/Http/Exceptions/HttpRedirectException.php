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

namespace Valkyrja\Http\Exceptions;

use Valkyrja\Http\Constants\StatusCode;
use Valkyrja\Http\Response;
use Valkyrja\Http\Responses\RedirectResponse;

/**
 * Class HttpRedirectException.
 *
 * @author Melech Mizrachi
 */
class HttpRedirectException extends HttpException
{
    /**
     * The uri to redirect to for this exception.
     */
    protected string $uri = '/';

    /**
     * HttpException constructor.
     *
     * @see http://php.net/manual/en/exception.construct.php
     *
     * @param int|null      $statusCode [optional] The status code to use
     * @param string|null   $uri        [optional] The Exception message to throw
     * @param array|null    $headers    [optional] The headers to send
     * @param Response|null $response   [optional] The Response
     */
    public function __construct(
        int $statusCode = null,
        string $uri = null,
        array $headers = null,
        Response $response = null
    ) {
        $statusCode ??= StatusCode::FOUND;
        $headers ??= [];
        $uri ??= '/';
        // Set a new redirect response if one wasn't passed in
        $response ??= new RedirectResponse($uri, $statusCode, $headers);
        // Set the uri
        $this->uri = $uri;

        parent::__construct($statusCode, 'Redirect', $headers, $response);
    }

    /**
     * Get the uri to redirect to for this exception.
     */
    public function getUri(): string
    {
        return $this->uri;
    }
}
