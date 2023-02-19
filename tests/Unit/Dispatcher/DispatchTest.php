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
use Valkyrja\Dispatcher\Models\Dispatch;

/**
 * Test the dispatch model.
 *
 * @author Melech Mizrachi
 */
class DispatchTest extends TestCase
{
    /**
     * The class to test with.
     *
     * @var \Valkyrja\Dispatcher\Dispatch
     */
    protected Dispatch $dispatch;

    /**
     * The value to test with.
     */
    protected string $value = 'test';

    /**
     * Setup the test.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->dispatch = new Dispatch();
    }

    /**
     * Test the getId method's default value.
     */
    public function testGetIdDefault(): void
    {
        self::assertEquals(null, $this->dispatch->getId());
    }

    /**
     * Test the getId method.
     */
    public function testGetId(): void
    {
        $this->dispatch->setId($this->value);

        self::assertEquals($this->value, $this->dispatch->getId());
    }

    /**
     * Test the setId method with null value.
     */
    public function testSetIdNull(): void
    {
        $set = $this->dispatch->setId(null);

        self::assertEquals(true, $set instanceof Dispatch);
    }

    /**
     * Test the setId method.
     */
    public function testSetId(): void
    {
        $set = $this->dispatch->setId($this->value);

        self::assertEquals(true, $set instanceof Dispatch);
    }

    /**
     * Test the getName method's default value.
     */
    public function testGetNameDefault(): void
    {
        self::assertEquals(null, $this->dispatch->getName());
    }

    /**
     * Test the getName method.
     */
    public function testGetName(): void
    {
        $this->dispatch->setName($this->value);

        self::assertEquals($this->value, $this->dispatch->getName());
    }

    /**
     * Test the setName method with null value.
     */
    public function testSetNameNull(): void
    {
        $set = $this->dispatch->setName(null);

        self::assertEquals(true, $set instanceof Dispatch);
    }

    /**
     * Test the setName method.
     */
    public function testSetName(): void
    {
        $set = $this->dispatch->setName($this->value);

        self::assertEquals(true, $set instanceof Dispatch);
    }

    /**
     * Test the getClass method's default value.
     */
    public function testGetClassDefault(): void
    {
        self::assertEquals(null, $this->dispatch->getClass());
    }

    /**
     * Test the getClass method.
     */
    public function testGetClass(): void
    {
        $this->dispatch->setClass($this->value);

        self::assertEquals($this->value, $this->dispatch->getClass());
    }

    /**
     * Test the setClass method with null value.
     */
    public function testSetClassNull(): void
    {
        $set = $this->dispatch->setClass(null);

        self::assertEquals(true, $set instanceof Dispatch);
    }

    /**
     * Test the setClass method.
     */
    public function testSetClass(): void
    {
        $set = $this->dispatch->setClass($this->value);

        self::assertEquals(true, $set instanceof Dispatch);
    }

    /**
     * Test the getProperty method's default value.
     */
    public function testGetPropertyDefault(): void
    {
        self::assertEquals(null, $this->dispatch->getProperty());
    }

    /**
     * Test the getProperty method.
     */
    public function testGetProperty(): void
    {
        $this->dispatch->setProperty($this->value);

        self::assertEquals($this->value, $this->dispatch->getProperty());
    }

    /**
     * Test the setProperty method with null value.
     */
    public function testSetPropertyNull(): void
    {
        $set = $this->dispatch->setProperty(null);

        self::assertEquals(true, $set instanceof Dispatch);
    }

    /**
     * Test the setProperty method.
     */
    public function testSetProperty(): void
    {
        $set = $this->dispatch->setProperty($this->value);

        self::assertEquals(true, $set instanceof Dispatch);
    }

    /**
     * Test the getMethod method's default value.
     */
    public function testGetMethodDefault(): void
    {
        self::assertEquals(null, $this->dispatch->getMethod());
    }

    /**
     * Test the getMethod method.
     */
    public function testGetMethod(): void
    {
        $this->dispatch->setMethod($this->value);

        self::assertEquals($this->value, $this->dispatch->getMethod());
    }

    /**
     * Test the setMethod method with null value.
     */
    public function testSetMethodNull(): void
    {
        $set = $this->dispatch->setMethod(null);

        self::assertEquals(true, $set instanceof Dispatch);
    }

    /**
     * Test the setMethod method.
     */
    public function testSetMethod(): void
    {
        $set = $this->dispatch->setMethod($this->value);

        self::assertEquals(true, $set instanceof Dispatch);
    }

    /**
     * Test the isStatic method's default value.
     */
    public function testIsStaticDefault(): void
    {
        self::assertEquals(null, $this->dispatch->isStatic());
    }

    /**
     * Test the isStatic method.
     */
    public function testIsStatic(): void
    {
        $this->dispatch->setStatic(true);

        self::assertEquals(true, $this->dispatch->isStatic());
    }

    /**
     * Test the setStatic method.
     */
    public function testSetStatic(): void
    {
        $set = $this->dispatch->setStatic(true);

        self::assertEquals(true, $set instanceof Dispatch);
    }

    /**
     * Test the getFunction method's default value.
     */
    public function testGetFunctionDefault(): void
    {
        self::assertEquals(null, $this->dispatch->getFunction());
    }

    /**
     * Test the getFunction method.
     */
    public function testGetFunction(): void
    {
        $this->dispatch->setFunction($this->value);

        self::assertEquals($this->value, $this->dispatch->getFunction());
    }

    /**
     * Test the setFunction method with null value.
     */
    public function testSetFunctionNull(): void
    {
        $set = $this->dispatch->setFunction(null);

        self::assertEquals(true, $set instanceof Dispatch);
    }

    /**
     * Test the setFunction method.
     */
    public function testSetFunction(): void
    {
        $set = $this->dispatch->setFunction($this->value);

        self::assertEquals(true, $set instanceof Dispatch);
    }

    /**
     * Test the getClosure method's default value.
     */
    public function testGetClosureDefault(): void
    {
        self::assertEquals(null, $this->dispatch->getClosure());
    }

    /**
     * Test the getClosure method.
     */
    public function testGetClosure(): void
    {
        $value = static function () {
        };
        $this->dispatch->setClosure($value);

        self::assertEquals($value, $this->dispatch->getClosure());
    }

    /**
     * Test the setClosure method with null value.
     */
    public function testSetClosureNull(): void
    {
        $set = $this->dispatch->setClosure(null);

        self::assertEquals(true, $set instanceof Dispatch);
    }

    /**
     * Test the setClosure method.
     */
    public function testSetClosure(): void
    {
        $set = $this->dispatch->setClosure(
            static function () {
            }
        );

        self::assertEquals(true, $set instanceof Dispatch);
    }

    /**
     * Test the getMatches method's default value.
     */
    public function testGetMatchesDefault(): void
    {
        self::assertEquals(null, $this->dispatch->getMatches());
    }

    /**
     * Test the getMatches method.
     */
    public function testGetMatches(): void
    {
        $this->dispatch->setMatches([$this->value]);

        self::assertEquals([$this->value], $this->dispatch->getMatches());
    }

    /**
     * Test the setMatches method with null value.
     */
    public function testSetMatchesNull(): void
    {
        $set = $this->dispatch->setMatches(null);

        self::assertEquals(true, $set instanceof Dispatch);
    }

    /**
     * Test the setMatches method.
     */
    public function testSetMatches(): void
    {
        $set = $this->dispatch->setMatches([$this->value]);

        self::assertEquals(true, $set instanceof Dispatch);
    }

    /**
     * Test the getDependencies method's default value.
     */
    public function testGetDependenciesDefault(): void
    {
        self::assertEquals(null, $this->dispatch->getDependencies());
    }

    /**
     * Test the getDependencies method.
     */
    public function testGetDependencies(): void
    {
        $this->dispatch->setDependencies([$this->value]);

        self::assertEquals([$this->value], $this->dispatch->getDependencies());
    }

    /**
     * Test the setDependencies method with null value.
     */
    public function testSetDependenciesNull(): void
    {
        $set = $this->dispatch->setDependencies(null);

        self::assertEquals(true, $set instanceof Dispatch);
    }

    /**
     * Test the setDependencies method.
     */
    public function testSetDependencies(): void
    {
        $set = $this->dispatch->setDependencies([$this->value]);

        self::assertEquals(true, $set instanceof Dispatch);
    }

    /**
     * Test the getArguments method's default value.
     */
    public function testGetArgumentsDefault(): void
    {
        self::assertEquals(null, $this->dispatch->getArguments());
    }

    /**
     * Test the getArguments method.
     */
    public function testGetArguments(): void
    {
        $this->dispatch->setArguments([$this->value]);

        self::assertEquals([$this->value], $this->dispatch->getArguments());
    }

    /**
     * Test the setArguments method with null value.
     */
    public function testSetArgumentsNull(): void
    {
        $set = $this->dispatch->setArguments(null);

        self::assertEquals(true, $set instanceof Dispatch);
    }

    /**
     * Test the setArguments method.
     */
    public function testSetArguments(): void
    {
        $set = $this->dispatch->setArguments([$this->value]);

        self::assertEquals(true, $set instanceof Dispatch);
    }
}
