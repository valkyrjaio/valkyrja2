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

namespace Valkyrja\Http\Enums;

use Valkyrja\Support\Enum\Enum;

/**
 * Enum Port.
 *
 * @author Melech Mizrachi
 */
final class Port extends Enum
{
    public const HTTP  = 80;
    public const HTTPS = 443;
}
