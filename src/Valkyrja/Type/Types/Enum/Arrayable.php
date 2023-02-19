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

namespace Valkyrja\Type\Types\Enum;

use Valkyrja\Type\Support\Enum;

/**
 * Trait Arrayable.
 *
 * @author Melech Mizrachi
 */
trait Arrayable
{
    /**
     * Get enum case names.
     */
    public static function names(): array
    {
        return Enum::names(self::class);
    }

    /**
     * Get enum case values.
     */
    public static function values(): array
    {
        return Enum::values(self::class);
    }

    /**
     * Get enum as an array with name as index and value as value.
     */
    public static function asArray(): array
    {
        return Enum::asArray(self::class);
    }

    /**
     * Get enum as an array with value as index and name as value.
     */
    public static function asReverseArray(): array
    {
        return Enum::asReverseArray(self::class);
    }
}
