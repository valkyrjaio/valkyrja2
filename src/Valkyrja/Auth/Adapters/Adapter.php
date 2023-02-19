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

namespace Valkyrja\Auth\Adapters;

use Exception;
use Valkyrja\Auth\Adapter as Contract;
use Valkyrja\Auth\Config\Config;
use Valkyrja\Auth\User;
use Valkyrja\Type\Support\Str;

use const PASSWORD_DEFAULT;

/**
 * Class Adapter.
 *
 * @author Melech Mizrachi
 */
abstract class Adapter implements Contract
{
    /**
     * Adapter constructor.
     *
     * @param Config|array $config The config
     */
    public function __construct(
        protected Config|array $config
    ) {
    }

    /**
     * Determine if a password verifies against the user's password.
     */
    public function verifyPassword(User $user, string $password): bool
    {
        return password_verify($password, $user->__get($user::getPasswordField()));
    }

    /**
     * Update a user's password.
     */
    public function updatePassword(User $user, string $password): void
    {
        $resetTokenField = $user::getResetTokenField();

        $user->__set($resetTokenField, null);
        $user->__set($user::getPasswordField(), $this->hashPassword($password));

        $this->save($user);
    }

    /**
     * Generate a reset token for a user.
     *
     * @throws Exception
     */
    public function updateResetToken(User $user): void
    {
        $user->__set($user::getResetTokenField(), Str::random());

        $this->save($user);
    }

    /**
     * Hash a plain password.
     */
    protected function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
