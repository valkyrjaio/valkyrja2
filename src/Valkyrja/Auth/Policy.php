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
 * Interface Policy.
 *
 * @author Melech Mizrachi
 */
interface Policy
{
    /**
     * Before authorization check.
     *
     * @param string $action The action to check if authorized for
     */
    public function before(string &$action): ?bool;

    /**
     * After authorization check.
     *
     * @param bool   $isAuthorized Whether the action is authorized
     * @param string $action       The action to check if authorized for
     */
    public function after(bool $isAuthorized, string $action): ?bool;

    /**
     * Check if the authenticated user is authorized.
     *
     * @param string $action The action to check if authorized for
     */
    public function isAuthorized(string $action): bool;
}
