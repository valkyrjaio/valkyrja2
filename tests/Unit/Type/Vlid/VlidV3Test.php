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

namespace Valkyrja\Tests\Unit\Type\Vlid;

use Valkyrja\Tests\Unit\TestCase;
use Valkyrja\Type\Support\VlidV3 as Helper;
use Valkyrja\Type\Types\VlidV3;

class VlidV3Test extends TestCase
{
    public function testGenerate(): void
    {
        $vlid = new VlidV3();

        self::assertTrue(Helper::isValid($vlid->asValue()));
    }
}
