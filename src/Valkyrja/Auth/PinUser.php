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

namespace Valkyrja\Auth;

/**
 * Interface PinUser.
 *
 * @author Melech Mizrachi
 */
interface PinUser extends User
{
    /**
     * Get the pin field.
     */
    public static function getPinField(): string;

    /**
     * Get the date pin was modified field.
     */
    public static function getDatePinModifiedField(): string;
}
