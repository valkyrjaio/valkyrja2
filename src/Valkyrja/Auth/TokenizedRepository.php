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
 * Interface TokenizedRepository.
 *
 * @author Melech Mizrachi
 */
interface TokenizedRepository extends Repository
{
    /**
     * Get the token.
     */
    public function getToken(): string;

    /**
     * Authenticate using a given token.
     *
     * @param string $token The token
     */
    public function authenticateFromToken(string $token): static;
}
