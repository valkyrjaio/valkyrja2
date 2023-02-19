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
 * Interface LockableUser.
 *
 * @author Melech Mizrachi
 */
interface LockableUser extends User
{
    /**
     * Get the max number login attempts before locking.
     */
    public static function getMaxLoginAttempts(): int;

    /**
     * Get the login attempts field.
     */
    public static function getLoginAttemptsField(): string;

    /**
     * Get the locked flag field.
     */
    public static function getIsLockedField(): string;
}
