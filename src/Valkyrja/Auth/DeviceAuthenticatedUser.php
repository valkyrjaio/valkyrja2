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
 * Interface DeviceAuthenticatedUser.
 *
 * @author Melech Mizrachi
 */
interface DeviceAuthenticatedUser extends User
{
    /**
     * Get the device id field.
     */
    public static function getDeviceIdField(): string;
}
