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
use Valkyrja\Http\Exceptions\NotFoundHttpException;

/**
 * Test the NotFoundHttpException class.
 *
 * @author Melech Mizrachi
 */
class NotFoundHttpExceptionTest extends TestCase
{
    /**
     * The exception.
     */
    protected NotFoundHttpException $exception;

    /**
     * Get the exception.
     */
    protected function getException(): NotFoundHttpException
    {
        return $this->exception ?? $this->exception = new NotFoundHttpException();
    }

    /**
     * Test the construction of a new NotFoundHttpException instance.
     */
    public function testConstruct(): void
    {
        self::assertEquals(true, $this->getException() instanceof NotFoundHttpException);
    }
}
