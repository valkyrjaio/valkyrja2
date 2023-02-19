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

namespace Valkyrja\Tests\Unit\Http\Exceptions;

use PHPUnit\Framework\TestCase;
use Valkyrja\Http\Constants\StatusCode;
use Valkyrja\Http\Exceptions\HttpException;

/**
 * Test the HttpException class.
 *
 * @author Melech Mizrachi
 */
class HttpExceptionTest extends TestCase
{
    /**
     * The exception.
     */
    protected HttpException $exception;

    /**
     * Get the exception.
     */
    protected function getException(): HttpException
    {
        return $this->exception ?? $this->exception = new HttpException();
    }

    /**
     * Test the construction of a new HttpException instance.
     */
    public function testConstruct(): void
    {
        self::assertEquals(true, $this->getException() instanceof HttpException);
    }

    /**
     * Test the getStatusCode method.
     */
    public function testGetStatusCode(): void
    {
        self::assertEquals(StatusCode::INTERNAL_SERVER_ERROR, $this->getException()->getStatusCode());
    }

    /**
     * Test the getHeaders method.
     */
    public function testGetHeaders(): void
    {
        self::assertEquals([], $this->getException()->getHeaders());
    }
}
