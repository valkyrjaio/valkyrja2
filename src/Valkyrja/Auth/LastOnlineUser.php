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
 * Interface LastOnlineUser.
 *
 * @author Melech Mizrachi
 */
interface LastOnlineUser extends User
{
    /**
     * Get the date last online field.
     */
    public static function getDateLastOnlineField(): string;
}
