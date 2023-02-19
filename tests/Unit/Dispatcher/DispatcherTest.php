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

use PHPUnit\Framework\TestCase;
use Valkyrja\Application\Application;
use Valkyrja\Container\Config\Container as Config;
use Valkyrja\Container\Managers\Container;
use Valkyrja\Dispatcher\Dispatchers\Dispatcher;
use Valkyrja\Dispatcher\Models\Dispatch;

use function count;
use function microtime;

/**
 * Test the dispatcher trait.
 *
 * @author Melech Mizrachi
 */
class DispatcherTest extends TestCase
{
    /**
     * The class to test with.
     */
    protected Dispatcher $dispatcher;

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

        $this->dispatcher = new Dispatcher(new Container(new Config(setup: true), true));
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
     * Test the dispatchClassMethod method.
     */
    public function testDispatchClassMethod(): void
    {
        $dispatch = (new Dispatch())
            ->setClass(static::class)
            ->setMethod('validMethod');

        self::assertEquals($this->validMethod(), $this->dispatcher->dispatchClassMethod($dispatch));
    }

    /**
     * Test the dispatchClassMethod method with arguments.
     */
    public function testDispatchClassMethodWithArgs(): void
    {
        $dispatch = (new Dispatch())
            ->setClass(static::class)
            ->setMethod('validMethod');

        self::assertEquals($this->validMethod('test'), $this->dispatcher->dispatchClassMethod($dispatch, ['test']));
    }

    /**
     * Test the dispatchClassMethod method with a static dispatch.
     */
    public function testDispatchClassMethodStatic(): void
    {
        $dispatch = (new Dispatch())
            ->setClass(static::class)
            ->setMethod('validStaticMethod')
            ->setStatic();

        self::assertEquals(static::validStaticMethod(), $this->dispatcher->dispatchClassMethod($dispatch));
    }

    /**
     * Test the dispatchClassMethod method with a static dispatch and arguments.
     */
    public function testDispatchClassMethodStaticWithArgs(): void
    {
        $dispatch = (new Dispatch())
            ->setClass(static::class)
            ->setMethod('validStaticMethod')
            ->setStatic();

        self::assertEquals(
            static::validStaticMethod('test'),
            $this->dispatcher->dispatchClassMethod($dispatch, ['test'])
        );
    }

    /**
     * Test the dispatchClassProperty method.
     */
    public function testDispatchClassProperty(): void
    {
        $dispatch = (new Dispatch())
            ->setClass(static::class)
            ->setProperty('validProperty');

        self::assertEquals($this->validProperty, $this->dispatcher->dispatchClassProperty($dispatch));
    }

    /**
     * Test the dispatchClassProperty method with a static dispatch.
     */
    public function testDispatchClassPropertyStatic(): void
    {
        $dispatch = (new Dispatch())
            ->setClass(static::class)
            ->setProperty('validStaticProperty')
            ->setStatic();

        self::assertEquals(static::$validStaticProperty, $this->dispatcher->dispatchClassProperty($dispatch));
    }

    /**
     * Test the dispatchClass method.
     */
    public function testDispatchClass(): void
    {
        $dispatch = (new Dispatch())
            ->setClass(static::class)
            ->setId(static::class);

        self::assertInstanceOf(static::class, $this->dispatcher->dispatchClass($dispatch));
    }

    /**
     * Test the dispatchClass method with arguments.
     */
    public function testDispatchClassWithArgs(): void
    {
        $dispatch = (new Dispatch())
            ->setClass(InvalidDispatcherClass::class)
            ->setId(InvalidDispatcherClass::class);

        $app = $this->createMock(Application::class);

        self::assertInstanceOf(
            InvalidDispatcherClass::class,
            $this->dispatcher->dispatchClass(
                $dispatch,
                [
                    $app,
                ]
            )
        );
    }

    /**
     * Test the dispatchClass method with a class from the container.
     */
    public function testDispatchClassFromContainer(): void
    {
        $dispatch = (new Dispatch())
            ->setClass(static::class);

        self::assertInstanceOf(static::class, $this->dispatcher->dispatchClass($dispatch));
    }

    /**
     * Test the dispatchFunction method.
     */
    public function testDispatchFunction(): void
    {
        $dispatch = (new Dispatch())
            ->setFunction('microtime');

        self::assertEquals(true, microtime() <= $this->dispatcher->dispatchFunction($dispatch));
    }

    /**
     * Test the dispatchFunction method with arguments.
     */
    public function testDispatchFunctionWithArgs(): void
    {
        $array    = ['foo', 'bar'];
        $dispatch = (new Dispatch())
            ->setFunction('count');

        self::assertEquals(count($array), $this->dispatcher->dispatchFunction($dispatch, [$array]));
    }

    /**
     * Test the dispatchClosure method.
     */
    public function testDispatchClosure(): void
    {
        $dispatch = (new Dispatch())
            ->setClosure(
                static function () {
                    return 'test';
                }
            );

        self::assertEquals('test', $this->dispatcher->dispatchClosure($dispatch));
    }

    /**
     * Test the dispatchClosure method with arguments.
     */
    public function testDispatchClosureWithArgs(): void
    {
        $array    = ['foo', 'bar'];
        $dispatch = (new Dispatch())
            ->setClosure(
                static function (array $array) {
                    return count($array);
                }
            );

        self::assertEquals(count($array), $this->dispatcher->dispatchClosure($dispatch, [$array]));
    }

    /**
     * Test the dispatchCallable method.
     */
    public function testDispatchCallable(): void
    {
        $dispatch = new Dispatch();

        self::assertEquals(null, $this->dispatcher->dispatch($dispatch));
    }

    /**
     * Test the dispatchCallable method with a null dispatch return.
     */
    public function testDispatchCallableNullDispatchReturn(): void
    {
        $dispatch = (new Dispatch())
            ->setClass(static::class)
            ->setProperty('validPropertyNull');

        self::assertEquals(null, $this->dispatcher->dispatch($dispatch));
    }

    /**
     * Test the dispatchCallable method with arguments.
     */
    public function testDispatchCallableWithArgs(): void
    {
        $array    = ['foo', 'bar'];
        $dispatch = new Dispatch();

        self::assertEquals(null, $this->dispatcher->dispatch($dispatch, [$array]));
    }

    /**
     * Test the dispatchCallable method with arguments in the dispatch.
     */
    public function testDispatchCallableWithArgsInDispatch(): void
    {
        $dispatch = new Dispatch();
        $dispatch = (new Dispatch())->setArguments(['test', $dispatch]);

        self::assertEquals(null, $this->dispatcher->dispatch($dispatch));
    }

    /**
     * Test the dispatchCallable method with dependencies.
     */
    public function testDispatchCallableWithDependencies(): void
    {
        $dispatch = (new Dispatch())
            ->setDependencies([static::class]);

        self::assertEquals(null, $this->dispatcher->dispatch($dispatch));
    }

    /**
     * Test the dispatchCallable method with a static dispatch and dependencies.
     */
    public function testDispatchCallableStaticWithDependencies(): void
    {
        $dispatch = (new Dispatch())
            ->setStatic()
            ->setDependencies([static::class]);

        self::assertEquals(null, $this->dispatcher->dispatch($dispatch));
    }
}
