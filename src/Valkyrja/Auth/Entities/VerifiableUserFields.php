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

namespace Valkyrja\Auth\Entities;

/**
 * Trait VerifiableUserFields.
 *
 * @author Melech Mizrachi
 */
trait VerifiableUserFields
{
    /**
     * The flag to determine whether a user is verified.
     */
    public bool $verified = false;
}
