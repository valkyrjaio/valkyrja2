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

namespace Valkyrja\Tests\Unit\Attributes;

use PHPUnit\Framework\TestCase;
use ReflectionException;
use Valkyrja\Attribute\Managers\Attributes;
use Valkyrja\Tests\Unit\Attributes\Classes\Attribute;
use Valkyrja\Tests\Unit\Attributes\Classes\AttributeChild;
use Valkyrja\Tests\Unit\Attributes\Classes\AttributedClass;

/**
 * Test the Attributes service.
 *
 * @author Melech Mizrachi
 */
class AttributesTest extends TestCase
{
    public const VALUE1  = 1;
    public const VALUE2  = 2;
    public const VALUE3  = 3;
    public const VALUE4  = 4;
    public const VALUE5  = 5;
    public const VALUE6  = 6;
    public const VALUE7  = 7;
    public const VALUE8  = 8;
    public const VALUE9  = 9;
    public const VALUE10 = 10;
    public const VALUE11 = 11;
    public const VALUE12 = 12;
    public const VALUE13 = 13;
    public const VALUE14 = 14;
    public const VALUE15 = 15;
    public const VALUE16 = 16;
    public const VALUE17 = 17;
    public const VALUE18 = 18;
    public const VALUE19 = 19;
    public const VALUE20 = 20;
    public const VALUE21 = 21;

    public const THREE      = 'three';
    public const SIX        = 'six';
    public const NINE       = 'nine';
    public const TWELVE     = 'twelve';
    public const FIFTEEN    = 'fifteen';
    public const EIGHTEEN   = 'eighteen';
    public const TWENTY_ONE = 'twenty one';

    protected const CONST_NAME           = 'CONST';
    protected const PROTECTED_CONST_NAME = 'PROTECTED_CONST';
    protected const STATIC_PROPERTY_NAME = 'staticProperty';
    protected const PROPERTY_NAME        = 'property';
    protected const STATIC_METHOD_NAME   = 'staticMethod';
    protected const METHOD_NAME          = 'method';

    /**
     * The attributes service.
     */
    protected Attributes $attributes;

    /**
     * The class to test with.
     */
    protected AttributedClass $class;

    /**
     * Setup the test.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->attributes = new Attributes();
        $this->class      = new AttributedClass();
    }

    /**
     * Test the forClass() method.
     *
     * @throws ReflectionException
     */
    public function testForClass(): void
    {
        $attributes = $this->attributes->forClass(AttributedClass::class, Attribute::class);

        $this->baseTests(...$attributes);
        $this->valueTests(
            self::VALUE1,
            self::VALUE2,
            self::VALUE3,
            self::THREE,
            ...$attributes
        );
    }

    /**
     * Test the forClassMembers() method.
     *
     * @throws ReflectionException
     */
    public function testForClassMembers(): void
    {
        $attributes = $this->attributes->forClassMembers(AttributedClass::class, Attribute::class);

        $this->assertCount(18, $attributes);
        $this->testsForConst($attributes[0], $attributes[1], $attributes[2]);
        $this->testsForProtectedConst($attributes[3], $attributes[4], $attributes[5]);
        $this->testsForStaticProperty($attributes[6], $attributes[7], $attributes[8]);
        $this->testsForProperty($attributes[9], $attributes[10], $attributes[11]);
        $this->testsForStaticMethod($attributes[12], $attributes[13], $attributes[14]);
        $this->testsForMethod($attributes[15], $attributes[16], $attributes[17]);
    }

    /**
     * Test the forClassMembers() method.
     *
     * @throws ReflectionException
     */
    public function testForClassAndMembers(): void
    {
        $attributes = $this->attributes->forClassAndMembers(AttributedClass::class, Attribute::class);

        $this->assertCount(21, $attributes);
        $this->baseTests($attributes[0], $attributes[1], $attributes[2]);
        $this->testsForConst($attributes[3], $attributes[4], $attributes[5]);
        $this->testsForProtectedConst($attributes[6], $attributes[7], $attributes[8]);
        $this->testsForStaticProperty($attributes[9], $attributes[10], $attributes[11]);
        $this->testsForProperty($attributes[12], $attributes[13], $attributes[14]);
        $this->testsForStaticMethod($attributes[15], $attributes[16], $attributes[17]);
        $this->testsForMethod($attributes[18], $attributes[19], $attributes[20]);
    }

    /**
     * Test the forConstant() method.
     *
     * @throws ReflectionException
     */
    public function testForConstant(): void
    {
        $attributes = $this->attributes->forConstant(
            AttributedClass::class,
            self::CONST_NAME,
            Attribute::class
        );

        $this->testsForConst(...$attributes);

        $attributes = $this->attributes->forConstant(
            AttributedClass::class,
            self::PROTECTED_CONST_NAME,
            Attribute::class
        );

        $this->testsForProtectedConst(...$attributes);
    }

    /**
     * Test the forConstants() method.
     *
     * @throws ReflectionException
     */
    public function testForConstants(): void
    {
        $attributes = $this->attributes->forConstants(AttributedClass::class, Attribute::class);

        $this->assertCount(6, $attributes);
        $this->testsForConst($attributes[0], $attributes[1], $attributes[2]);
        $this->testsForProtectedConst($attributes[3], $attributes[4], $attributes[5]);
    }

    /**
     * Test the forProperty() method.
     *
     * @throws ReflectionException
     */
    public function testForProperty(): void
    {
        $attributes = $this->attributes->forProperty(
            AttributedClass::class,
            self::STATIC_PROPERTY_NAME,
            Attribute::class
        );

        $this->testsForStaticProperty(...$attributes);

        $attributes = $this->attributes->forProperty(
            AttributedClass::class,
            self::PROPERTY_NAME,
            Attribute::class
        );

        $this->testsForProperty(...$attributes);
    }

    /**
     * Test the forProperties() method.
     *
     * @throws ReflectionException
     */
    public function testForProperties(): void
    {
        $attributes = $this->attributes->forProperties(AttributedClass::class, Attribute::class);

        $this->assertCount(6, $attributes);
        $this->testsForStaticProperty($attributes[0], $attributes[1], $attributes[2]);
        $this->testsForProperty($attributes[3], $attributes[4], $attributes[5]);
    }

    /**
     * Test the forMethod() method.
     *
     * @throws ReflectionException
     */
    public function testForMethod(): void
    {
        $attributes = $this->attributes->forMethod(
            AttributedClass::class,
            self::STATIC_METHOD_NAME,
            Attribute::class
        );

        $this->testsForStaticMethod(...$attributes);

        $attributes = $this->attributes->forMethod(
            AttributedClass::class,
            self::METHOD_NAME,
            Attribute::class
        );

        $this->testsForMethod(...$attributes);
    }

    /**
     * Test the forMethods() method.
     *
     * @throws ReflectionException
     */
    public function testForMethods(): void
    {
        $attributes = $this->attributes->forMethods(AttributedClass::class, Attribute::class);

        $this->assertCount(6, $attributes);
        $this->testsForStaticMethod($attributes[0], $attributes[1], $attributes[2]);
        $this->testsForMethod($attributes[3], $attributes[4], $attributes[5]);
    }

    /**
     * Test the forFunction() method.
     *
     * @throws ReflectionException
     */
    public function testForFunction(): void
    {
        #[Attribute(AttributesTest::VALUE1)]
        #[Attribute(AttributesTest::VALUE2)]
        #[AttributeChild(AttributesTest::VALUE3, AttributesTest::THREE)]
        function testFunction(): void
        {
        }

        $attributes = $this->attributes->forFunction('\Valkyrja\Tests\Unit\Attributes\testFunction', Attribute::class);

        $this->baseTests(...$attributes);
        $this->valueTests(
            self::VALUE1,
            self::VALUE2,
            self::VALUE3,
            self::THREE,
            ...$attributes
        );
    }

    /**
     * Test the forFunction() method.
     *
     * @throws ReflectionException
     */
    public function testForClosure(): void
    {
        $attributes = $this->attributes->forClosure(
            #[Attribute(self::VALUE4)]
            #[Attribute(self::VALUE5)]
            #[AttributeChild(self::VALUE6, self::SIX)]
            function (): void {
            },
            Attribute::class
        );

        $this->baseTests(...$attributes);
        $this->valueTests(
            self::VALUE4,
            self::VALUE5,
            self::VALUE6,
            self::SIX,
            ...$attributes
        );

        $attributes = $this->attributes->forClosure(
            #[Attribute(self::VALUE7)]
            #[Attribute(self::VALUE8)]
            #[AttributeChild(self::VALUE9, self::NINE)]
            static function (): void {
            },
            Attribute::class
        );

        $this->baseTests(...$attributes);
        $this->valueTests(
            self::VALUE7,
            self::VALUE8,
            self::VALUE9,
            self::NINE,
            ...$attributes
        );
    }

    /**
     * Tests for the const member.
     *
     * @param Attribute|AttributeChild ...$attributes The attributes
     */
    protected function testsForConst(Attribute|AttributeChild ...$attributes): void
    {
        $this->baseTests(...$attributes);
        $this->valueTests(
            self::VALUE4,
            self::VALUE5,
            self::VALUE6,
            self::SIX,
            ...$attributes
        );
        $this->setTests($attributes[2], false, self::CONST_NAME);
    }

    /**
     * Tests for the protectedConst member.
     *
     * @param Attribute|AttributeChild ...$attributes The attributes
     */
    protected function testsForProtectedConst(Attribute|AttributeChild ...$attributes): void
    {
        $this->baseTests(...$attributes);
        $this->valueTests(
            self::VALUE7,
            self::VALUE8,
            self::VALUE9,
            self::NINE,
            ...$attributes
        );
        $this->setTests($attributes[2], false, self::PROTECTED_CONST_NAME);
    }

    /**
     * Tests for the staticProperty member.
     *
     * @param Attribute|AttributeChild ...$attributes The attributes
     */
    protected function testsForStaticProperty(Attribute|AttributeChild ...$attributes): void
    {
        $this->baseTests(...$attributes);
        $this->valueTests(
            self::VALUE10,
            self::VALUE11,
            self::VALUE12,
            self::TWELVE,
            ...$attributes
        );
        $this->setTests($attributes[2], true, self::STATIC_PROPERTY_NAME);
    }

    /**
     * Tests for the property member.
     *
     * @param Attribute|AttributeChild ...$attributes The attributes
     */
    protected function testsForProperty(Attribute|AttributeChild ...$attributes): void
    {
        $this->baseTests(...$attributes);
        $this->valueTests(
            self::VALUE13,
            self::VALUE14,
            self::VALUE15,
            self::FIFTEEN,
            ...$attributes
        );
        $this->setTests($attributes[2], false, self::PROPERTY_NAME);
    }

    /**
     * Tests for the staticMember() member.
     *
     * @param Attribute|AttributeChild ...$attributes The attributes
     */
    protected function testsForStaticMethod(Attribute|AttributeChild ...$attributes): void
    {
        $this->baseTests(...$attributes);
        $this->valueTests(
            self::VALUE16,
            self::VALUE17,
            self::VALUE18,
            self::EIGHTEEN,
            ...$attributes
        );
        $this->setTests($attributes[2], true, self::STATIC_METHOD_NAME);
    }

    /**
     * Tests for the method() member.
     *
     * @param Attribute|AttributeChild ...$attributes The attributes
     */
    protected function testsForMethod(Attribute|AttributeChild ...$attributes): void
    {
        $this->baseTests(...$attributes);
        $this->valueTests(
            self::VALUE19,
            self::VALUE20,
            self::VALUE21,
            self::TWENTY_ONE,
            ...$attributes
        );
        $this->setTests($attributes[2], false, self::METHOD_NAME);
    }

    /**
     * Base tests for all items.
     *
     * @param Attribute ...$attributes The attributes
     */
    protected function baseTests(Attribute ...$attributes): void
    {
        $this->assertCount(3, $attributes);
        $this->assertInstanceOf(Attribute::class, $attributes[0]);
        $this->assertInstanceOf(Attribute::class, $attributes[1]);
        $this->assertInstanceOf(AttributeChild::class, $attributes[2]);
    }

    /**
     * Test values.
     *
     * @param int            $value1     The value in the first attribute
     * @param int            $value2     The value in the second attribute
     * @param int            $value3     The first value in the third attribute
     * @param string         $value4     The second value in the third attribute
     * @param Attribute      $attribute1 The first attribute
     * @param Attribute      $attribute2 The second attribute
     * @param AttributeChild $attribute3 The third attribute
     */
    protected function valueTests(
        int $value1,
        int $value2,
        int $value3,
        string $value4,
        Attribute $attribute1,
        Attribute $attribute2,
        AttributeChild $attribute3
    ): void {
        $this->assertEquals($value1, $attribute1->counter);
        $this->assertEquals($value2, $attribute2->counter);
        $this->assertEquals($value3, $attribute3->counter);
        $this->assertEquals($value4, $attribute3->test);
    }

    /**
     * Tests related to setters.
     *
     * @param AttributeChild $attribute The attribute
     * @param bool           $isStatic  Whether the member is static
     * @param string         $name      The name of the member
     */
    protected function setTests(AttributeChild $attribute, bool $isStatic, string $name): void
    {
        $this->assertEquals(AttributedClass::class, $attribute->class);
        $this->assertEquals($isStatic, $attribute->static);

        match ($name) {
            self::CONST_NAME, self::PROTECTED_CONST_NAME => function () use ($name, $attribute): void {
                $this->assertEquals($name, $attribute->constant);
                $this->assertNull($attribute->property);
                $this->assertNull($attribute->method);
            },
            self::STATIC_PROPERTY_NAME, self::PROPERTY_NAME => function () use ($name, $attribute): void {
                $this->assertEquals($name, $attribute->property);
                $this->assertNull($attribute->constant);
                $this->assertNull($attribute->method);
            },
            self::STATIC_METHOD_NAME, self::METHOD_NAME => function () use ($name, $attribute): void {
                $this->assertEquals($name, $attribute->method);
                $this->assertNull($attribute->constant);
                $this->assertNull($attribute->property);
            },
        };
    }
}
