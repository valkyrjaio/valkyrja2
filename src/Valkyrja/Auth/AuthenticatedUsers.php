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

use Valkyrja\Model\CastableModel;

/**
 * Interface AuthenticatedUsers.
 *
 * @author   Melech Mizrachi
 *
 * @template User
 */
interface AuthenticatedUsers extends CastableModel
{
    /**
     * Determine whether there is a current user in the collection.
     */
    public function hasCurrent(): bool;

    /**
     * Get the current user.
     */
    public function getCurrent(): ?User;

    /**
     * Set the current user.
     *
     * @param User $user The user
     */
    public function setCurrent(User $user): static;

    /**
     * Check if a user is authenticated.
     *
     * @param User $user The user to check
     */
    public function isAuthenticated(User $user): bool;

    /**
     * Add a user to the collection.
     *
     * @param User $user The user
     */
    public function add(User $user): static;

    /**
     * Remove a user from the collection.
     *
     * @param User $user The user
     */
    public function remove(User $user): static;

    /**
     * Get all the users in the collection.
     *
     * @return array<int|string, User>
     */
    public function all(): array;
}
