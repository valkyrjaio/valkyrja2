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

use Exception;
use Valkyrja\Auth\Exceptions\InvalidRegistrationException;

/**
 * Interface Adapter.
 *
 * @author Melech Mizrachi
 */
interface Adapter
{
    /**
     * Attempt to authenticate a user.
     */
    public function authenticate(User $user): bool;

    /**
     * Get a user via login fields.
     */
    public function retrieve(User $user): ?User;

    /**
     * Get a user from token.
     */
    public function retrieveByResetToken(User $user, string $resetToken): ?User;

    /**
     * Refresh a user from the data store.
     */
    public function retrieveById(User $user): User;

    /**
     * Save a user.
     */
    public function save(User $user): void;

    /**
     * Determine if a password verifies against the user's password.
     */
    public function verifyPassword(User $user, string $password): bool;

    /**
     * Update a user's password.
     *
     * @throws Exception
     */
    public function updatePassword(User $user, string $password): void;

    /**
     * Generate a reset token for a user.
     *
     * @throws Exception
     */
    public function updateResetToken(User $user): void;

    /**
     * Register a new user.
     *
     * @throws InvalidRegistrationException
     */
    public function create(User $user): bool;
}
