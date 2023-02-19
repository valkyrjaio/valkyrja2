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

namespace Valkyrja\Tests\Unit\Annotations;

use PHPUnit\Framework\TestCase;
use Valkyrja\Annotation\Models\Annotation;

/**
 * Test the Annotatable trait.
 *
 * @author Melech Mizrachi
 */
class AnnotatableTest extends TestCase
{
    /**
     * The class to test with.
     *
     * @var AnnotatableClass
     */
    protected $class;

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

        $this->class = new Annotation();
    }

    /**
     * Test the getAnnotationType method's default value.
     */
    public function testGetAnnotationTypeDefault(): void
    {
        self::assertEquals(null, $this->class->getType());
    }

    /**
     * Test the getAnnotationType method.
     */
    public function testGetAnnotationType(): void
    {
        $this->class->setType($this->value);

        self::assertEquals($this->value, $this->class->getType());
    }

    /**
     * Test the setAnnotationType method with null value.
     */
    public function testSetAnnotationTypeNull(): void
    {
        self::assertEquals($this->class, $this->class->setType(null));
    }

    /**
     * Test the setAnnotationType method.
     */
    public function testSetAnnotationType(): void
    {
        self::assertEquals($this->class, $this->class->setType($this->value));
    }

    /**
     * Test the getId method's default value.
     */
    public function testGetIdDefault(): void
    {
        self::assertEquals(null, $this->class->getId());
    }

    /**
     * Test the getId method.
     */
    public function testGetId(): void
    {
        $this->class->setId($this->value);

        self::assertEquals($this->value, $this->class->getId());
    }

    /**
     * Test the setId method with null value.
     */
    public function testSetIdNull(): void
    {
        self::assertEquals($this->class, $this->class->setId(null));
    }

    /**
     * Test the setId method.
     */
    public function testSetId(): void
    {
        self::assertEquals($this->class, $this->class->setId($this->value));
    }

    /**
     * Test the getName method's default value.
     */
    public function testGetNameDefault(): void
    {
        self::assertEquals(null, $this->class->getName());
    }

    /**
     * Test the getName method.
     */
    public function testGetName(): void
    {
        $this->class->setName($this->value);

        self::assertEquals($this->value, $this->class->getName());
    }

    /**
     * Test the setName method with null value.
     */
    public function testSetNameNull(): void
    {
        self::assertEquals($this->class, $this->class->setName(null));
    }

    /**
     * Test the setName method.
     */
    public function testSetName(): void
    {
        self::assertEquals($this->class, $this->class->setName($this->value));
    }

    /**
     * Test the getClass method's default value.
     */
    public function testGetClassDefault(): void
    {
        self::assertEquals(null, $this->class->getClass());
    }

    /**
     * Test the getClass method.
     */
    public function testGetClass(): void
    {
        $this->class->setClass($this->value);

        self::assertEquals($this->value, $this->class->getClass());
    }

    /**
     * Test the setClass method with null value.
     */
    public function testSetClassNull(): void
    {
        self::assertEquals($this->class, $this->class->setClass(null));
    }

    /**
     * Test the setClass method.
     */
    public function testSetClass(): void
    {
        self::assertEquals($this->class, $this->class->setClass($this->value));
    }

    /**
     * Test the getProperty method's default value.
     */
    public function testGetPropertyDefault(): void
    {
        self::assertEquals(null, $this->class->getProperty());
    }

    /**
     * Test the getProperty method.
     */
    public function testGetProperty(): void
    {
        $this->class->setProperty($this->value);

        self::assertEquals($this->value, $this->class->getProperty());
    }

    /**
     * Test the setProperty method with null value.
     */
    public function testSetPropertyNull(): void
    {
        self::assertEquals($this->class, $this->class->setProperty(null));
    }

    /**
     * Test the setProperty method.
     */
    public function testSetProperty(): void
    {
        self::assertEquals($this->class, $this->class->setProperty($this->value));
    }

    /**
     * Test the getMethod method's default value.
     */
    public function testGetMethodDefault(): void
    {
        self::assertEquals(null, $this->class->getMethod());
    }

    /**
     * Test the getMethod method.
     */
    public function testGetMethod(): void
    {
        $this->class->setMethod($this->value);

        self::assertEquals($this->value, $this->class->getMethod());
    }

    /**
     * Test the setMethod method with null value.
     */
    public function testSetMethodNull(): void
    {
        self::assertEquals($this->class, $this->class->setMethod(null));
    }

    /**
     * Test the setMethod method.
     */
    public function testSetMethod(): void
    {
        self::assertEquals($this->class, $this->class->setMethod($this->value));
    }

    /**
     * Test the isStatic method's default value.
     */
    public function testIsStaticDefault(): void
    {
        self::assertEquals(null, $this->class->isStatic());
    }

    /**
     * Test the isStatic method.
     */
    public function testIsStatic(): void
    {
        $this->class->setStatic(true);

        self::assertEquals(true, $this->class->isStatic());
    }

    /**
     * Test the setStatic method.
     */
    public function testSetStatic(): void
    {
        self::assertEquals($this->class, $this->class->setStatic(true));
    }

    /**
     * Test the getFunction method's default value.
     */
    public function testGetFunctionDefault(): void
    {
        self::assertEquals(null, $this->class->getFunction());
    }

    /**
     * Test the getFunction method.
     */
    public function testGetFunction(): void
    {
        $this->class->setFunction($this->value);

        self::assertEquals($this->value, $this->class->getFunction());
    }

    /**
     * Test the setFunction method with null value.
     */
    public function testSetFunctionNull(): void
    {
        self::assertEquals($this->class, $this->class->setFunction(null));
    }

    /**
     * Test the setFunction method.
     */
    public function testSetFunction(): void
    {
        self::assertEquals($this->class, $this->class->setFunction($this->value));
    }

    /**
     * Test the getMatches method's default value.
     */
    public function testGetMatchesDefault(): void
    {
        self::assertEquals(null, $this->class->getMatches());
    }

    /**
     * Test the getMatches method.
     */
    public function testGetMatches(): void
    {
        $this->class->setMatches([$this->value]);

        self::assertEquals([$this->value], $this->class->getMatches());
    }

    /**
     * Test the setMatches method with null value.
     */
    public function testSetMatchesNull(): void
    {
        self::assertEquals($this->class, $this->class->setMatches(null));
    }

    /**
     * Test the setMatches method.
     */
    public function testSetMatches(): void
    {
        self::assertEquals($this->class, $this->class->setMatches([$this->value]));
    }

    /**
     * Test the getArguments method's default value.
     */
    public function testGetArgumentsDefault(): void
    {
        self::assertEquals(null, $this->class->getArguments());
    }

    /**
     * Test the getArguments method.
     */
    public function testGetArguments(): void
    {
        $this->class->setArguments([$this->value]);

        self::assertEquals([$this->value], $this->class->getArguments());
    }

    /**
     * Test the setArguments method with null value.
     */
    public function testSetArgumentsNull(): void
    {
        self::assertEquals($this->class, $this->class->setArguments(null));
    }

    /**
     * Test the setArguments method.
     */
    public function testSetArguments(): void
    {
        self::assertEquals($this->class, $this->class->setArguments([$this->value]));
    }

    /**
     * Test the getDependencies method's default value.
     */
    public function testGetDependenciesDefault(): void
    {
        self::assertEquals(null, $this->class->getDependencies());
    }

    /**
     * Test the getDependencies method.
     */
    public function testGetDependencies(): void
    {
        $this->class->setDependencies([$this->value]);

        self::assertEquals([$this->value], $this->class->getDependencies());
    }

    /**
     * Test the setDependencies method with null value.
     */
    public function testSetDependenciesNull(): void
    {
        self::assertEquals($this->class, $this->class->setDependencies(null));
    }

    /**
     * Test the setDependencies method.
     */
    public function testSetDependencies(): void
    {
        self::assertEquals($this->class, $this->class->setDependencies([$this->value]));
    }
}
