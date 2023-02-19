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

namespace Valkyrja\Tests\Unit\Container\Annotations;

use PHPUnit\Framework\TestCase;
use Valkyrja\Container\Annotations\Service;

/**
 * Test the service model.
 *
 * @author Melech Mizrachi
 */
class ServiceTest extends TestCase
{
    /**
     * The class to test with.
     */
    protected Service $class;

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

        $this->class = new Service();
    }

    /**
     * Test the isSingleton method's default value.
     */
    public function testIsSingletonDefault(): void
    {
        self::assertEquals(null, $this->class->isSingleton());
    }

    /**
     * Test the isSingleton method.
     */
    public function testIsSingleton(): void
    {
        $this->class->setSingleton(true);

        self::assertEquals(true, $this->class->isSingleton());
    }

    /**
     * Test the setSingleton method.
     */
    public function testSetSingleton(): void
    {
        self::assertEquals($this->class, $this->class->setSingleton(true));
    }

    /**
     * Test the getDefaults method's default value.
     */
    public function testGetDefaultsDefault(): void
    {
        self::assertEquals(null, $this->class->getDefaults());
    }

    /**
     * Test the getDefaults method.
     */
    public function testGetDefaults(): void
    {
        $this->class->setDefaults([$this->value]);

        self::assertEquals([$this->value], $this->class->getDefaults());
    }

    /**
     * Test the setDefaults method with null value.
     */
    public function testSetDefaultsNull(): void
    {
        self::assertEquals($this->class, $this->class->setDefaults(null));
    }

    /**
     * Test the setDefaults method.
     */
    public function testSetDefaults(): void
    {
        self::assertEquals($this->class, $this->class->setDefaults([$this->value]));
    }
}
