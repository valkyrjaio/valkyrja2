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

namespace Valkyrja\Auth\Enums;

use Valkyrja\Support\Enum\Enum;

/**
 * Enum SessionId.
 *
 * @author Melech Mizrachi
 */
final class SessionId extends Enum
{
    public const USER                         = 'auth.user';
    public const PASSWORD_CONFIRMED_TIMESTAMP = 'auth.passwordConfirmedTimestamp';
}
