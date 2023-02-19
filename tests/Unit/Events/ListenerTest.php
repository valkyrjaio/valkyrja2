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

namespace Valkyrja\Tests\Unit\Events;

use PHPUnit\Framework\TestCase;
use Valkyrja\Event\Models\Listener;

/**
 * Test the listener model.
 *
 * @author Melech Mizrachi
 */
class ListenerTest extends TestCase
{
    /**
     * The class to test with.
     *
     * @var \Valkyrja\Event\Listener
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

        $this->class = new Listener();
    }

    /**
     * Test the getEvent method.
     */
    public function testGetEvent(): void
    {
        $this->class->setEvent($this->value);

        self::assertEquals($this->value, $this->class->getEvent());
    }

    /**
     * Test the setEvent method.
     */
    public function testSetEvent(): void
    {
        $set = $this->class->setEvent($this->value);

        self::assertEquals(true, $set instanceof Listener);
    }
}
