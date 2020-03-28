<?php

declare(strict_types=1);

/*
 * This file is part of the Valkyrja framework.
 *
 * (c) Melech Mizrachi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valkyrja\Auth\Entities;

/**
 * Trait LockableUserFields.
 *
 * @author Melech Mizrachi
 */
trait LockableUserFields
{
    use LockableUserTrait;

    /**
     * The number of login attempts.
     *
     * @var int
     */
    public int $login_attempts = 0;

    /**
     * The flag to determine whether a user is locked.
     *
     * @var bool
     */
    public bool $locked = false;
}
