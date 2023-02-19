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

namespace Valkyrja\Http\Constants;

/**
 * Constant Port.
 *
 * @author Melech Mizrachi
 */
final class Port
{
    public const MIN = 1;
    public const MAX = 65535;

    public const HTTP  = 80;
    public const HTTPS = 443;

    /**
     * Check if a port is valid.
     *
     * @param int|null $port [optional] The port
     */
    public static function isValid(int $port = null): bool
    {
        return $port === null || ($port >= self::MIN && $port <= self::MAX);
    }
}
