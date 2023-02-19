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

namespace Valkyrja\Tests\Unit\Enum;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Valkyrja\Type\Types\Enum;

use function array_diff;
use function get_class;
use function method_exists;

/**
 * Test the Enum abstract class.
 *
 * @author Melech Mizrachi
 */
class EnumTest extends TestCase
{
    /**
     * Ensure the set value method exists in the Enum abstract class.
     */
    public function testSetValueExists(): void
    {
        self::assertEquals(true, method_exists(Enum::class, 'setValue'));
    }

    /**
     * Test the set value method.
     */
    public function testSetValue(): void
    {
        $enum = $this->getEnum();
        $enum->setValue(EnumClass::FOO);

        self::assertEquals(EnumClass::FOO, $enum->getValue());
    }

    /**
     * Get an enum to test with.
     */
    protected function getEnum(): Enum
    {
        return new EnumClass(EnumClass::BAR);
    }

    /**
     * Test the set value method with an invalid value.
     */
    public function testSetValueInvalid(): void
    {
        try {
            new EnumClass('invalid value');
        } catch (Exception $exception) {
            self::assertEquals(InvalidArgumentException::class, get_class($exception));
        }
    }

    /**
     * Ensure the get value method exists in the Enum abstract class.
     */
    public function testGetValueExists(): void
    {
        self::assertEquals(true, method_exists(Enum::class, 'getValue'));
    }

    /**
     * Ensure the get value method functions correctly.
     */
    public function testGetValue(): void
    {
        self::assertEquals(EnumClass::BAR, $this->getEnum()->getValue());
    }

    /**
     * Ensure the is valid method exists in the Enum abstract class.
     */
    public function testIsValidExists(): void
    {
        self::assertEquals(true, method_exists(Enum::class, 'isValid'));
    }

    /**
     * Test the is valid method with a valid value.
     */
    public function testIsValid(): void
    {
        self::assertEquals(true, $this->getEnum()->isValid(EnumClass::FOO));
    }

    /**
     * Test the is valid method with an invalid enum value.
     */
    public function testNotValid(): void
    {
        self::assertEquals(false, $this->getEnum()->isValid('invalid value'));
    }

    /**
     * Ensure the valid values method exists in the Enum abstract class.
     */
    public function testValidValuesExists(): void
    {
        self::assertEquals(true, method_exists(Enum::class, 'getValidValues'));
    }

    /**
     * Test the valid values of an enum.
     */
    public function testValidValues(): void
    {
        self::assertEquals(
            [
                EnumClass::FOO => EnumClass::FOO,
                EnumClass::BAR => EnumClass::BAR,
            ],
            $this->getEnum()->getValidValues()
        );
    }

    /**
     * Test the valid values through use of the reflection class.
     */
    public function testValidValuesReflection(): void
    {
        self::assertEquals(
            [],
            array_diff(
                [
                    EnumClass::FOO => EnumClass::FOO,
                    EnumClass::BAR => EnumClass::BAR,
                ],
                $this->getEnumEmpty()->getValidValues()
            )
        );
    }

    /**
     * Get an enum with no default values set to test with.
     */
    protected function getEnumEmpty(): Enum
    {
        return new EnumClassEmpty(EnumClassEmpty::FOO);
    }

    /**
     * Test setting an invalid enum value after creating an enum with a valid value.
     */
    public function testSetInvalidEnumValue(): void
    {
        $enum = new EnumClass(EnumClass::FOO);

        try {
            $enum->setValue('invalid');
        } catch (Exception $exception) {
            self::assertEquals(true, $exception instanceof InvalidArgumentException);
        }
    }

    /**
     * Ensure the to string method exists in the Enum abstract class.
     */
    public function testToStringExists(): void
    {
        self::assertEquals(true, method_exists(Enum::class, '__toString'));
    }

    /**
     * Test the enum __toString capability.
     */
    public function testToString(): void
    {
        self::assertEquals('foo', (string) $this->getEnum());
    }
}
