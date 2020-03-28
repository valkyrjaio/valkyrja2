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

namespace Valkyrja\Auth\Enums;

use Valkyrja\Enum\Enums\Enum;

/**
 * Enum RouteName.
 *
 * @author Melech Mizrachi
 */
final class RouteName extends Enum
{
    public const AUTHENTICATE     = 'auth.authenticate';
    public const PASSWORD_CONFIRM = 'auth.password.confirm';
    public const PASSWORD_FORGOT  = 'auth.password.forgot';
    public const PASSWORD_REST    = 'auth.password.reset';
    public const REGISTER         = 'auth.register';
    public const DASHBOARD        = 'dashboard';
}
