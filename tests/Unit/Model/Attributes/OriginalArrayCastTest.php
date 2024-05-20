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

namespace Valkyrja\Tests\Unit\Model\Attributes;

use Valkyrja\Model\Attributes\Cast;
use Valkyrja\Model\Attributes\OriginalArrayCast;
use Valkyrja\Model\Enums\CastType;
use Valkyrja\Tests\Unit\TestCase;
use Valkyrja\Type\BuiltIn\StringT;

use function json_encode;

class OriginalArrayCastTest extends TestCase
{
    public function testInherits(): void
    {
        self::isA(Cast::class, OriginalArrayCast::class);
    }

    public function testStringType(): void
    {
        $value = StringT::class;
        $data  = new OriginalArrayCast($value);

        self::assertSame($value, $data->type);
        self::assertFalse($data->convert);
        self::assertTrue($data->isArray);
    }

    public function testCastType(): void
    {
        $value = CastType::string;
        $data  = new OriginalArrayCast($value);

        self::assertSame($value->value, $data->type);
        self::assertFalse($data->convert);
        self::assertTrue($data->isArray);
    }

    public function testJsonSerialize(): void
    {
        $value = StringT::class;
        $data  = new OriginalArrayCast($value);

        self::assertSame(
            json_encode([
                'type'    => $value,
                'convert' => false,
                'isArray' => true,
            ]),
            json_encode($data)
        );
    }
}
