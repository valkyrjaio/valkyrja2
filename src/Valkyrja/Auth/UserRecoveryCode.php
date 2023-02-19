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

use Valkyrja\Orm\Entity;

/**
 * Interface UserRecoveryCode.
 *
 * @author Melech Mizrachi
 */
interface UserRecoveryCode extends Entity
{
    /**
     * Get the user id field.
     */
    public static function getUserIdField(): string;

    /**
     * Get the code field.
     */
    public static function getCodeField(): string;
}
