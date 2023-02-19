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
use Valkyrja\Container\Annotations\Service\Context;

/**
 * Test the service context model.
 *
 * @author Melech Mizrachi
 */
class ServiceContextTest extends TestCase
{
    /**
     * The class to test with.
     */
    protected Context $class;

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

        $this->class = new Context();
    }

    /**
     * Test the getService method's default value.
     */
    public function testGetServiceDefault(): void
    {
        self::assertEquals(null, $this->class->getService());
    }

    /**
     * Test the getService method.
     */
    public function testGetService(): void
    {
        $this->class->setService($this->value);

        self::assertEquals($this->value, $this->class->getService());
    }

    /**
     * Test the setService method.
     */
    public function testSetService(): void
    {
        self::assertEquals($this->class, $this->class->setService($this->value));
    }
}
