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

namespace Valkyrja\Tests\Unit\Dispatcher;

use Exception;
use PHPUnit\Framework\TestCase;
use Valkyrja\Dispatcher\Exceptions\InvalidDispatchCapabilityException;
use Valkyrja\Dispatcher\Exceptions\InvalidFunctionException;
use Valkyrja\Dispatcher\Exceptions\InvalidMethodException;
use Valkyrja\Dispatcher\Exceptions\InvalidPropertyException;
use Valkyrja\Dispatcher\Models\Dispatch;
use Valkyrja\Dispatcher\Validators\Validator;

/**
 * Test the dispatcher trait.
 *
 * @author Melech Mizrachi
 */
class ValidatorTest extends TestCase
{
    /**
     * The class to test with.
     */
    protected Validator $validator;

    /**
     * The value to test with.
     */
    protected string $value = 'test';

    /**
     * A valid property.
     */
    public string $validProperty = 'test';

    /**
     * A valid property with null value.
     */
    public ?string $validPropertyNull = null;

    /**
     * A valid static property.
     */
    public static string $validStaticProperty = 'test';

    /**
     * Setup the test.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->validator = new Validator();
    }

    /**
     * A valid method.
     *
     * @param string|null $arg [optional] An argument
     */
    public function validMethod(string $arg = null): string
    {
        return 'test' . ($arg ?: '');
    }

    /**
     * A valid static method.
     *
     * @param string|null $arg [optional] An argument
     */
    public static function validStaticMethod(string $arg = null): string
    {
        return 'test' . ($arg ?: '');
    }

    /**
     * Verify a valid class/method dispatch.
     */
    public function testVerifyClassMethod(): void
    {
        $valid = $this->validator->classMethod(
            (new Dispatch())
                ->setClass(self::class)
                ->setMethod('validMethod')
        ) ?? null;

        self::assertEquals(null, $valid);
    }

    /**
     * Verify an invalid class/method dispatch.
     */
    public function testVerifyClassMethodInvalid(): void
    {
        try {
            $this->validator->classMethod(
                (new Dispatch())
                    ->setClass(self::class)
                    ->setMethod('invalidMethod')
            );
        } catch (Exception $exception) {
            self::assertEquals(true, $exception instanceof InvalidMethodException);
        }
    }

    /**
     * Verify a valid class/property dispatch.
     */
    public function testVerifyClassProperty(): void
    {
        $valid = $this->validator->classProperty(
            (new Dispatch())
                ->setClass(self::class)
                ->setProperty('validProperty')
        ) ?? null;

        self::assertEquals(null, $valid);
    }

    /**
     * Verify an invalid class/property dispatch.
     */
    public function testVerifyClassPropertyInvalid(): void
    {
        try {
            $this->validator->classProperty(
                (new Dispatch())
                    ->setClass(self::class)
                    ->setProperty('invalidProperty')
            );
        } catch (Exception $exception) {
            self::assertEquals(true, $exception instanceof InvalidPropertyException);
        }
    }

    /**
     * Verify a valid function dispatch.
     */
    public function testVerifyFunction(): void
    {
        $valid = $this->validator->func(
            (new Dispatch())
                ->setFunction('\Valkyrja\routeUrl')
        ) ?? null;

        self::assertEquals(null, $valid);
    }

    /**
     * Verify an invalid function dispatch.
     */
    public function testVerifyFunctionInvalid(): void
    {
        try {
            $this->validator->func(
                (new Dispatch())
                    ->setFunction('invalidFunction')
            );
        } catch (Exception $exception) {
            self::assertEquals(true, $exception instanceof InvalidFunctionException);
        }
    }

    /**
     * Verify a valid dispatch.
     */
    public function testVerifyDispatch(): void
    {
        $valid = $this->validator->dispatch(
            (new Dispatch())
                ->setClosure(
                    static function () {
                    }
                )
        ) ?? null;

        self::assertEquals(null, $valid);
    }

    /**
     * Verify an invalid dispatch.
     */
    public function testVerifyDispatchInvalid(): void
    {
        try {
            $this->validator->dispatch(
                new Dispatch()
            );
        } catch (Exception $exception) {
            self::assertEquals(true, $exception instanceof InvalidDispatchCapabilityException);
        }
    }
}
