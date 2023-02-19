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

namespace Valkyrja\Type;

/**
 * Interface Arrayable.
 *
 * @author Melech Mizrachi
 */
interface Arrayable
{
    /**
     * Get the object's named properties as an array.
     */
    public static function names(): array;

    /**
     * Get the object's values as an array.
     */
    public static function values(): array;

    /**
     * Get the object as an array.
     */
    public static function asArray(): array;

    /**
     * Get the object as a reversed array.
     */
    public static function asReverseArray(): array;
}
