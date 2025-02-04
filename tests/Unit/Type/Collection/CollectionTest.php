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

namespace Valkyrja\Tests\Unit\Type\Collection;

use Valkyrja\Tests\Unit\TestCase;
use Valkyrja\Type\Collection\Collection;

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
     *
     * @var Collection
     */
    protected Collection $class;

    /**
     * The value to test with.
     *
     * @var array
     */
    protected array $value = ['foo' => 'bar'];

    /**
     * The value to test with.
     *
     * @var array
     */
    protected array $valueAlt = ['bar' => 'foo'];

    /**
     * Setup the test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->class = new Collection($this->value);
    }

    /**
     * Test the constructor with no data.
     *
     * @return void
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(Collection::class, new Collection());
    }

    /**
     * Test the constructor with data.
     *
     * @return void
     */
    public function testConstructWithData(): void
    {
        self::assertInstanceOf(Collection::class, new Collection(['test', 'test2']));
    }

    /**
     * Test getting a value from the collection.
     *
     * @return void
     */
    public function testGet(): void
    {
        self::assertSame($this->value['foo'], $this->class->get('foo', false));
    }

    /**
     * Test getting a value from the collection that doesn't exist.
     *
     * @return void
     */
    public function testGetNonExistent(): void
    {
        self::assertNull($this->class->get('invalid'));
    }

    /**
     * Test getting a value from the collection that doesn't exist with a default value set.
     *
     * @return void
     */
    public function testGetNonExistentWithDefault(): void
    {
        self::assertSame('default', $this->class->get('invalid', 'default'));
    }

    /**
     * Test the has method.
     *
     * @return void
     */
    public function testHas(): void
    {
        self::assertTrue($this->class->has('foo'));
    }

    /**
     * Test the has method with a non existent key.
     *
     * @return void
     */
    public function testHasNonExistent(): void
    {
        self::assertFalse($this->class->has('invalid'));
    }

    /**
     * Test the exists method.
     *
     * @return void
     */
    public function testExists(): void
    {
        self::assertTrue($this->class->exists('bar'));
    }

    /**
     * Test the exists method with a non existent value.
     *
     * @return void
     */
    public function testExistsNonExistent(): void
    {
        self::assertFalse($this->class->exists('invalid'));
    }

    /**
     * Test the set method.
     *
     * @return void
     */
    public function testSet(): void
    {
        self::assertInstanceOf(Collection::class, $this->class->set('bar', 'foo'));
    }

    /**
     * Test the has method with the new key set.
     *
     * @return void
     */
    public function testHasNewValue(): void
    {
        $this->class->set('bar', 'foo');

        self::assertTrue($this->class->has('bar'));
    }

    /**
     * Test the exists method with the new value set.
     *
     * @return void
     */
    public function testExistsNewValue(): void
    {
        $this->class->set('bar', 'foo');

        self::assertTrue($this->class->exists('foo'));
    }

    /**
     * Test the remove method.
     *
     * @return void
     */
    public function testRemove(): void
    {
        $this->class->set('bar', 'foo');

        self::assertInstanceOf(Collection::class, $this->class->remove('bar'));
    }

    /**
     * Test the remove method with a non existent key.
     *
     * @return void
     */
    public function testRemoveNonExistent(): void
    {
        self::assertInstanceOf(Collection::class, $this->class->remove('invalid'));
    }

    /**
     * Test the all method.
     *
     * @return void
     */
    public function testAll(): void
    {
        self::assertSame($this->value, $this->class->all());
    }

    /**
     * Test the setAll method.
     *
     * @return void
     */
    public function testSetAll(): void
    {
        self::assertInstanceOf(Collection::class, $this->class->setAll($this->valueAlt));
    }

    /**
     * Test the keys method.
     *
     * @return void
     */
    public function testKeys(): void
    {
        self::assertSame(array_keys($this->value), $this->class->keys());
    }

    /**
     * Test the count method.
     *
     * @return void
     */
    public function testCount(): void
    {
        self::assertSame(count($this->value), $this->class->count());
    }

    /**
     * Test the isEmpty method.
     *
     * @return void
     */
    public function testIsEmpty(): void
    {
        self::assertSame(empty($this->value), $this->class->isEmpty());
    }

    /**
     * Test the magic __get method.
     *
     * @return void
     */
    public function testMagicGet(): void
    {
        $this->class->setAll($this->valueAlt);

        self::assertSame($this->valueAlt['bar'], $this->class->bar);
    }

    /**
     * Test the magic __isset method.
     *
     * @return void
     */
    public function testMagicIsset(): void
    {
        $this->class->setAll($this->valueAlt);

        self::assertTrue(isset($this->class->bar));
    }

    /**
     * Test the magic __set method.
     *
     * @return void
     */
    public function testMagicSet(): void
    {
        $this->class->setAll($this->valueAlt);

        self::assertSame('test', $this->class->foo = 'test');
    }

    /**
     * Test the magic __unset method.
     *
     * @return void
     */
    public function testMagicUnset(): void
    {
        unset($this->class->foo);

        self::assertFalse($this->class->has('foo'));
    }

    /**
     * Test the magic __toString method.
     *
     * @return void
     */
    public function testMagicToString(): void
    {
        $this->class->setAll($this->valueAlt);

        self::assertSame(json_encode($this->valueAlt, JSON_THROW_ON_ERROR), (string) $this->class);
    }
}
