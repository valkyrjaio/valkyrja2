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
use Valkyrja\Http\Exceptions\HttpRedirectException;

/**
 * Test the HttpRedirectException class.
 *
 * @author Melech Mizrachi
 */
class HttpRedirectExceptionTest extends TestCase
{
    /**
     * The exception.
     */
    protected HttpRedirectException $exception;

    /**
     * Get the exception.
     */
    protected function getException(): HttpRedirectException
    {
        return $this->exception ?? $this->exception = new HttpRedirectException();
    }

    /**
     * Test the construction of a new HttpRedirectException instance.
     */
    public function testConstruct(): void
    {
        self::assertEquals(true, $this->getException() instanceof HttpRedirectException);
    }

    /**
     * Test the getUri method.
     */
    public function testGetUri(): void
    {
        self::assertEquals('/', $this->getException()->getUri());
    }
}
