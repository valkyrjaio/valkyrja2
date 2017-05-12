<?php

namespace Valkyrja\Tests\Unit\Debug;

use ErrorException;
use Exception;
use PHPUnit\Framework\TestCase;
use Valkyrja\Debug\ErrorHandler;

/**
 * Test the error handler class.
 *
 * @author Melech Mizrachi
 */
class ErrorHandlerTest extends TestCase
{
    /**
     * The class to test with.
     *
     * @var \Valkyrja\Debug\ErrorHandler
     */
    protected $class;

    /**
     * Setup the test.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        error_reporting(1);
        $this->class = new ErrorHandler();
    }

    /**
     * Test the handleError method.
     *
     * @return void
     */
    public function testHandleError(): void
    {
        try {
            $this->class->handleError(1, 'message');
        } catch (Exception $exception) {
            $this->assertEquals(ErrorException::class, get_class($exception));
        }
    }

    /**
     * Test the fatalExceptionFromError method.
     *
     * @return void
     */
    public function testFatalExceptionFromError(): void
    {
        $this->assertEquals(
            ErrorException::class,
            $this->class->fatalExceptionFromError(
                [
                    'message' => 'test',
                    'type'    => 1,
                    'file'    => 'test',
                    'line'    => 1,
                ]
            )
        );
    }
}
