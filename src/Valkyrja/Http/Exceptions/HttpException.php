<?php

/*
 * This file is part of the Valkyrja framework.
 *
 * (c) Melech Mizrachi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valkyrja\Http\Exceptions;

use Exception;
use RuntimeException;
use Valkyrja\Http\Response;
use Valkyrja\Http\StatusCode;

/**
 * Class HttpException.
 *
 * @author Melech Mizrachi
 */
class HttpException extends RuntimeException
{
    /**
     * The status code for this exception.
     *
     * @var int
     */
    protected $statusCode;

    /**
     * The headers for this exception.
     *
     * @var array
     */
    protected $headers;

    /**
     * The response to send for this exception.
     *
     * @var null|Response
     */
    protected $response;

    /**
     * HttpException constructor.
     *
     * @link http://php.net/manual/en/exception.construct.php
     *
     * @param int        $statusCode [optional] The status code to use
     * @param string     $message    [optional] The Exception message to throw
     * @param \Exception $previous   [optional] The previous exception used for
     *                               the exception chaining
     * @param array      $headers    [optional] The headers to send
     * @param int        $code       [optional] The Exception code
     * @param Response   $response   [optional] The Response to send
     */
    public function __construct(
        int $statusCode = StatusCode::INTERNAL_SERVER_ERROR,
        string $message = '',
        Exception $previous = null,
        array $headers = [],
        int $code = 0,
        Response $response = null
    ) {
        $this->statusCode = $statusCode;
        $this->headers    = $headers;
        $this->response   = $response;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the status code for this exception.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get the headers set for this exception.
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get the response for this exception.
     *
     * @return null|Response
     */
    public function getResponse(): ? Response
    {
        return $this->response;
    }
}
