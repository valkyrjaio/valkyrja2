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
 * Interface VerifiableUser.
 *
 * @author Melech Mizrachi
 */
interface VerifiableUser extends MailableUser
{
    /**
     * Get the verified flag field.
     */
    public static function getIsVerifiedField(): string;
}
