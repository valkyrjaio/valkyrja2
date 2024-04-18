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

namespace Valkyrja\Tests\Classes\Enums;

use Valkyrja\Type\ArrayableEnum;
use Valkyrja\Type\JsonSerializableEnum;
use Valkyrja\Type\Types\Enum\JsonSerializable;

/**
 * Enum class to use to test Arrayable Enum.
 *
 * @author Melech Mizrachi
 */
enum Arrayable implements ArrayableEnum, JsonSerializableEnum
{
    use JsonSerializable;
    use \Valkyrja\Type\Types\Enum\Arrayable;

    case spade;
    case heart;
    case diamond;
    case club;
}
