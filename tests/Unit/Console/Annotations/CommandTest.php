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

namespace Valkyrja\Tests\Unit\Console\Annotations;

use PHPUnit\Framework\TestCase;
use Valkyrja\Console\Annotations\Command;

/**
 * Test the command model.
 *
 * @author Melech Mizrachi
 */
class CommandTest extends TestCase
{
    /**
     * The class to test with.
     */
    protected Command $class;

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

        $this->class = new Command();
    }

    /**
     * Test the getPath method's default value.
     */
    public function testGetPathDefault(): void
    {
        self::assertEquals(null, $this->class->getPath());
    }

    /**
     * Test the getPath method.
     */
    public function testGetPath(): void
    {
        $this->class->setPath($this->value);

        self::assertEquals($this->value, $this->class->getPath());
    }

    /**
     * Test the setPath method.
     */
    public function testSetPath(): void
    {
        self::assertEquals($this->class, $this->class->setPath($this->value));
    }

    /**
     * Test the getRegex method's default value.
     */
    public function testGetRegexDefault(): void
    {
        self::assertEquals(null, $this->class->getRegex());
    }

    /**
     * Test the getRegex method.
     */
    public function testGetRegex(): void
    {
        $this->class->setRegex($this->value);

        self::assertEquals($this->value, $this->class->getRegex());
    }

    /**
     * Test the setRegex method with null value.
     */
    public function testSetRegexNull(): void
    {
        self::assertEquals($this->class, $this->class->setRegex(null));
    }

    /**
     * Test the setRegex method.
     */
    public function testSetRegex(): void
    {
        self::assertEquals($this->class, $this->class->setRegex($this->value));
    }

    /**
     * Test the getParams method's default value.
     */
    public function testGetParamsDefault(): void
    {
        self::assertEquals(null, $this->class->getParams());
    }

    /**
     * Test the getParams method.
     */
    public function testGetParams(): void
    {
        $this->class->setParams([$this->value]);

        self::assertEquals([$this->value], $this->class->getParams());
    }

    /**
     * Test the setParams method with null value.
     */
    public function testSetParamsNull(): void
    {
        self::assertEquals($this->class, $this->class->setParams(null));
    }

    /**
     * Test the setParams method.
     */
    public function testSetParams(): void
    {
        self::assertEquals($this->class, $this->class->setParams([$this->value]));
    }

    /**
     * Test the getSegments method's default value.
     */
    public function testGetSegmentsDefault(): void
    {
        self::assertEquals(null, $this->class->getSegments());
    }

    /**
     * Test the getSegments method.
     */
    public function testGetSegments(): void
    {
        $this->class->setSegments([$this->value]);

        self::assertEquals([$this->value], $this->class->getSegments());
    }

    /**
     * Test the setSegments method with null value.
     */
    public function testSetSegmentsNull(): void
    {
        self::assertEquals($this->class, $this->class->setSegments(null));
    }

    /**
     * Test the setSegments method.
     */
    public function testSetSegments(): void
    {
        self::assertEquals($this->class, $this->class->setSegments([$this->value]));
    }

    /**
     * Test the getDescription method's default value.
     */
    public function testGetDescriptionDefault(): void
    {
        self::assertEquals(null, $this->class->getDescription());
    }

    /**
     * Test the getDescription method.
     */
    public function testGetDescription(): void
    {
        $this->class->setDescription($this->value);

        self::assertEquals($this->value, $this->class->getDescription());
    }

    /**
     * Test the setDescription method with null value.
     */
    public function testSetDescriptionNull(): void
    {
        self::assertEquals($this->class, $this->class->setDescription(null));
    }

    /**
     * Test the setDescription method.
     */
    public function testSetDescription(): void
    {
        self::assertEquals($this->class, $this->class->setDescription($this->value));
    }
}
