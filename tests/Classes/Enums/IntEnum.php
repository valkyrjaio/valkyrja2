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

use Valkyrja\Type\Enum as Contract;

/**
 * Model class to use to test int BackedEnum.
 *
 * @author Melech Mizrachi
 */
enum IntEnum: int implements Contract
{
    use \Valkyrja\Type\Types\Enum;

    case first  = 1;
    case second = 2;
}
