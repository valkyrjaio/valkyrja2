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

namespace Valkyrja\Type\Support;

use function mb_convert_case;
use function mb_strtolower;
use function mb_strtoupper;
use function mb_substr;

/**
 * Class MbStr.
 *
 * @author Melech Mizrachi
 */
class MbStr extends Str
{
    /**
     * The charset to use.
     */
    protected static string $charset = 'UTF-8';

    /**
     * Get a substring from start position with a certain length.
     */
    public static function substr(string $subject, int $start, int $length = null): string
    {
        return mb_substr($subject, $start, $length, static::$charset);
    }

    /**
     * Convert a string to title case.
     */
    public static function toTitleCase(string $subject): string
    {
        return mb_convert_case($subject, MB_CASE_TITLE, static::$charset);
    }

    /**
     * Convert a string to lower case.
     */
    public static function toLowerCase(string $subject): string
    {
        return mb_strtolower($subject, static::$charset);
    }

    /**
     * Convert a string to upper case.
     */
    public static function toUpperCase(string $subject): string
    {
        return mb_strtoupper($subject, static::$charset);
    }
}
