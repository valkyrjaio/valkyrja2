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

namespace Valkyrja\Tests\Unit\Events\Attributes\Classes;

use Valkyrja\Event\Attributes\Listener;
use Valkyrja\Tests\Unit\Events\Attributes\AttributesTest;

/**
 * Class with attributes used for unit testing.
 *
 * @author Melech Mizrachi
 */
// Testing valid class attributes that will be attached to the constructor
#[Listener(AttributesTest::VALUE1)]
#[Listener(AttributesTest::VALUE2)]
class AttributedClass2
{
    public function __construct()
    {
    }

    #[Listener(AttributesTest::VALUE1)]
    #[Listener(AttributesTest::VALUE2)]
    public static function staticMethod(): string
    {
        return 'Static Method';
    }

    #[Listener(AttributesTest::VALUE1)]
    #[Listener(AttributesTest::VALUE2)]
    public function method(): string
    {
        return 'Method';
    }
}
