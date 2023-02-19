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
 * Interface TokenizableUser.
 *
 * @author Melech Mizrachi
 */
interface TokenizableUser extends User
{
    /**
     * Get the session id.
     */
    public static function getTokenSessionId(): string;

    /**
     * Set the tokenized user.
     *
     * @param string $token The tokenized user
     */
    public static function setTokenized(string $token): void;

    /**
     * Get the user as a token.
     */
    public static function asTokenized(): ?string;

    /**
     * Get user as an array for storing as a token.
     */
    public function asTokenizableArray(): array;
}
