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

namespace Valkyrja\Tests\Unit\Support;

use PHPUnit\Framework\TestCase;
use Valkyrja\Collection\Collections\Collection;

use function array_keys;
use function count;
use function json_encode;

use const JSON_THROW_ON_ERROR;

/**
 * Test the collection support class.
 *
 * @author Melech Mizrachi
 */
class CollectionTest extends TestCase
{
    /**
     * The class to test with.
     */
    protected Collection $class;

    /**
     * The value to test with.
     */
    protected array $value = ['foo' => 'bar'];

    /**
     * The value to test with.
     */
    protected array $valueAlt = ['bar' => 'foo'];

    /**
     * Setup the test.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->class = new Collection($this->value);
    }

    /**
     * Test the constructor with no data.
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(Collection::class, new Collection());
    }

    /**
     * Test the constructor with data.
     */
    public function testConstructWithData(): void
    {
        self::assertInstanceOf(Collection::class, new Collection(['test', 'test2']));
    }

    /**
     * Test getting a value from the collection.
     */
    public function testGet(): void
    {
        self::assertEquals($this->value['foo'], $this->class->get('foo', false));
    }

    /**
     * Test getting a value from the collection that doesn't exist.
     */
    public function testGetNonExistent(): void
    {
        self::assertEquals(null, $this->class->get('invalid'));
    }

    /**
     * Test getting a value from the collection that doesn't exist with a default value set.
     */
    public function testGetNonExistentWithDefault(): void
    {
        self::assertEquals('default', $this->class->get('invalid', 'default'));
    }

    /**
     * Test the has method.
     */
    public function testHas(): void
    {
        self::assertEquals(true, $this->class->has('foo'));
    }

    /**
     * Test the has method with a non existent key.
     */
    public function testHasNonExistent(): void
    {
        self::assertEquals(false, $this->class->has('invalid'));
    }

    /**
     * Test the exists method.
     */
    public function testExists(): void
    {
        self::assertEquals(true, $this->class->exists('bar'));
    }

    /**
     * Test the exists method with a non existent value.
     */
    public function testExistsNonExistent(): void
    {
        self::assertEquals(false, $this->class->exists('invalid'));
    }

    /**
     * Test the set method.
     */
    public function testSet(): void
    {
        self::assertInstanceOf(Collection::class, $this->class->set('bar', 'foo'));
    }

    /**
     * Test the has method with the new key set.
     */
    public function testHasNewValue(): void
    {
        $this->class->set('bar', 'foo');

        self::assertEquals(true, $this->class->has('bar'));
    }

    /**
     * Test the exists method with the new value set.
     */
    public function testExistsNewValue(): void
    {
        $this->class->set('bar', 'foo');

        self::assertEquals(true, $this->class->exists('foo'));
    }

    /**
     * Test the remove method.
     */
    public function testRemove(): void
    {
        $this->class->set('bar', 'foo');

        self::assertInstanceOf(Collection::class, $this->class->remove('bar'));
    }

    /**
     * Test the remove method with a non existent key.
     */
    public function testRemoveNonExistent(): void
    {
        self::assertInstanceOf(Collection::class, $this->class->remove('invalid'));
    }

    /**
     * Test the all method.
     */
    public function testAll(): void
    {
        self::assertEquals($this->value, $this->class->all());
    }

    /**
     * Test the setAll method.
     */
    public function testSetAll(): void
    {
        self::assertInstanceOf(Collection::class, $this->class->setAll($this->valueAlt));
    }

    /**
     * Test the keys method.
     */
    public function testKeys(): void
    {
        self::assertEquals(array_keys($this->value), $this->class->keys());
    }

    /**
     * Test the count method.
     */
    public function testCount(): void
    {
        self::assertEquals(count($this->value), $this->class->count());
    }

    /**
     * Test the isEmpty method.
     */
    public function testIsEmpty(): void
    {
        self::assertEquals(empty($this->value), $this->class->isEmpty());
    }

    /**
     * Test the magic __get method.
     */
    public function testMagicGet(): void
    {
        $this->class->setAll($this->valueAlt);

        self::assertEquals($this->valueAlt['bar'], $this->class->bar);
    }

    /**
     * Test the magic __isset method.
     */
    public function testMagicIsset(): void
    {
        $this->class->setAll($this->valueAlt);

        self::assertEquals(true, isset($this->class->bar));
    }

    /**
     * Test the magic __set method.
     */
    public function testMagicSet(): void
    {
        $this->class->setAll($this->valueAlt);

        self::assertEquals('test', $this->class->foo = 'test');
    }

    /**
     * Test the magic __unset method.
     */
    public function testMagicUnset(): void
    {
        unset($this->class->foo);

        self::assertEquals(false, $this->class->has('foo'));
    }

    /**
     * Test the magic __toString method.
     */
    public function testMagicToString(): void
    {
        $this->class->setAll($this->valueAlt);

        self::assertEquals(json_encode($this->valueAlt, JSON_THROW_ON_ERROR), (string) $this->class);
    }
}
