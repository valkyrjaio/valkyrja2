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

/**
 * Class Integer.
 *
 * @author Melech Mizrachi
 */
class Integer
{
    /**
     * Check if an integer is greater than a minimum value.
     *
     * @param int $subject The subject
     * @param int $min     [optional] The minimum value
     */
    public static function greaterThan(int $subject, int $min): bool
    {
        return $subject > $min;
    }

    /**
     * Check if an integer is greater than a maximum value.
     *
     * @param int $subject The subject
     * @param int $max     [optional] The minimum value
     */
    public static function lessThan(int $subject, int $max): bool
    {
        return $subject < $max;
    }

    /**
     * Check if an integer is divisible by a value.
     *
     * @param int $subject The subject
     * @param int $value   The value the subject should be divisible by
     */
    public static function divisible(int $subject, int $value): bool
    {
        return $subject % $value === 0;
    }
}
