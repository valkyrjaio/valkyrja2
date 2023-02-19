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

namespace Valkyrja\Tests\Unit\Console\Input;

use PHPUnit\Framework\TestCase;
use Valkyrja\Console\Enums\OptionMode;
use Valkyrja\Console\Inputs\Option;

/**
 * Test the Option input class.
 *
 * @author Melech Mizrachi
 */
class OptionTest extends TestCase
{
    /**
     * The class to test with.
     */
    protected Option $class;

    /**
     * The name.
     */
    protected string $name = 'Test Name';

    /**
     * The description.
     */
    protected string $description = 'Test Description';

    /**
     * The shortcut.
     */
    protected string $shortcut = 'S';

    /**
     * The default value.
     */
    protected string $default = 'Default Value';

    /**
     * Get the class to test with.
     */
    protected function getClass(): Option
    {
        return $this->class ?? $this->class = new Option(
            $this->name,
            $this->description,
            $this->shortcut,
            null,
            $this->default
        );
    }

    /**
     * Test the construction of a new Option instance.
     */
    public function testConstruct(): void
    {
        self::assertEquals(true, $this->getClass() instanceof Option);
    }

    /**
     * Test the getName getter method.
     */
    public function testGetName(): void
    {
        self::assertEquals($this->name, $this->getClass()->getName());
    }

    /**
     * Test the getShortcut getter method.
     */
    public function testGetShortcut(): void
    {
        self::assertEquals($this->shortcut, $this->getClass()->getShortcut());
    }

    /**
     * Test the getDescription getter method.
     */
    public function testGetDescription(): void
    {
        self::assertEquals($this->description, $this->getClass()->getDescription());
    }

    /**
     * Test the getDefault getter method.
     */
    public function testGetDefault(): void
    {
        self::assertEquals($this->default, $this->getClass()->getDefault());
    }

    /**
     * Test the getMode getter method.
     */
    public function testGetMode(): void
    {
        self::assertEquals(OptionMode::NONE, $this->getClass()->getMode());
    }
}
