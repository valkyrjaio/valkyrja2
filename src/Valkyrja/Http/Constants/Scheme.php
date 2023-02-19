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
 * Constant Scheme.
 *
 * @author Melech Mizrachi
 */
final class Scheme
{
    public const EMPTY = '';
    public const HTTP  = 'http';
    public const HTTPS = 'https';

    /**
     * Determine if a scheme is valid.
     *
     * @param string $scheme The scheme
     */
    public static function isValid(string $scheme): bool
    {
        return $scheme === self::EMPTY || $scheme === self::HTTP || $scheme === self::HTTPS;
    }
}
