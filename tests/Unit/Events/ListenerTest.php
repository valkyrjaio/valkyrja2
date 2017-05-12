<?php

namespace Valkyrja\Tests\Unit\Events;

use PHPUnit\Framework\TestCase;
use Valkyrja\Events\Listener;

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
     * @var \Valkyrja\Events\Listener
     */
    protected $class;

    /**
     * The value to test with.
     *
     * @var string
     */
    protected $value = 'test';

    /**
     * Setup the test.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->class = new Listener();
    }

    /**
     * Test the getEvent method's default value.
     *
     * @return void
     */
    public function testGetEventDefault(): void
    {
        $this->assertEquals(null, $this->class->getEvent());
    }

    /**
     * Test the getEvent method.
     *
     * @return void
     */
    public function testGetEvent(): void
    {
        $this->class->setEvent($this->value);

        $this->assertEquals($this->value, $this->class->getEvent());
    }

    /**
     * Test the setEvent method with null value.
     *
     * @return void
     */
    public function testSetEventNull(): void
    {
        $set = $this->class->setEvent(null);

        $this->assertEquals(true, $set instanceof Listener);
    }

    /**
     * Test the setEvent method.
     *
     * @return void
     */
    public function testSetEvent(): void
    {
        $set = $this->class->setEvent($this->value);

        $this->assertEquals(true, $set instanceof Listener);
    }
}
