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

use Valkyrja\Auth\Exceptions\InvalidAuthenticationException;
use Valkyrja\Auth\Exceptions\InvalidPasswordConfirmationException;
use Valkyrja\Auth\Exceptions\InvalidRegistrationException;
use Valkyrja\Http\Request;

/**
 * Interface Repository.
 *
 * @author Melech Mizrachi
 */
interface Repository
{
    /**
     * Determine if a user is authenticated.
     */
    public function isAuthenticated(): bool;

    /**
     * Get the authenticated user.
     */
    public function getUser(): User;

    /**
     * Set the authenticated user.
     *
     * @param User $user The user
     */
    public function setUser(User $user): static;

    /**
     * Get the authenticated users.
     */
    public function getUsers(): AuthenticatedUsers;

    /**
     * Set the authenticated users.
     *
     * @param AuthenticatedUsers $users The users
     */
    public function setUsers(AuthenticatedUsers $users): static;

    /**
     * Authenticate a user with credentials.
     *
     * @param User $user The user
     *
     * @throws InvalidAuthenticationException
     */
    public function authenticate(User $user): static;

    /**
     * Authenticate a user from an active session.
     *
     * @throws InvalidAuthenticationException
     */
    public function authenticateFromSession(): static;

    /**
     * Authenticate a user from a request.
     *
     * @param Request $request The request
     *
     * @throws InvalidAuthenticationException
     */
    public function authenticateFromRequest(Request $request): static;

    /**
     * Un-authenticate any active users.
     *
     * @param User|null $user [optional] The user to un-authenticate
     */
    public function unAuthenticate(User $user = null): static;

    /**
     * Set the authenticated user in the session.
     */
    public function setSession(): static;

    /**
     * Unset the authenticated user from the session.
     */
    public function unsetSession(): static;

    /**
     * Register a new user.
     *
     * @param User $user The user
     *
     * @throws InvalidRegistrationException
     */
    public function register(User $user): static;

    /**
     * Forgot password.
     *
     * @param User $user The user
     */
    public function forgot(User $user): static;

    /**
     * Reset a user's password.
     *
     * @param string $resetToken The reset token
     * @param string $password   The password
     */
    public function reset(string $resetToken, string $password): static;

    /**
     * Lock a user.
     *
     * @param LockableUser $user The user
     */
    public function lock(LockableUser $user): static;

    /**
     * Unlock a user.
     *
     * @param LockableUser $user The user
     */
    public function unlock(LockableUser $user): static;

    /**
     * Confirm the current user's password.
     *
     * @param string $password The password
     *
     * @throws InvalidPasswordConfirmationException
     */
    public function confirmPassword(string $password): static;

    /**
     * Determine if a re-authentication needs to occur.
     */
    public function isReAuthenticationRequired(): bool;

    /**
     * Get the adapter.
     */
    public function getAdapter(): Adapter;
}
