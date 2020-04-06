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

namespace Valkyrja\Auth\Authenticators;

use Exception;
use Valkyrja\Auth\Authenticator as Contract;
use Valkyrja\Auth\LockableUser;
use Valkyrja\Auth\User;
use Valkyrja\Crypt\Crypt;
use Valkyrja\Crypt\Exceptions\CryptException;
use Valkyrja\ORM\ORM;
use Valkyrja\Support\Type\Str;

use function password_hash;
use function password_verify;
use function serialize;
use function unserialize;

use const PASSWORD_DEFAULT;

/**
 * Class Authenticator.
 *
 * @author Melech Mizrachi
 */
class Authenticator implements Contract
{
    /**
     * The Crypt.
     *
     * @var Crypt
     */
    protected Crypt $crypt;

    /**
     * The ORM.
     *
     * @var ORM
     */
    protected ORM $orm;

    /**
     * Authenticator constructor.
     *
     * @param Crypt $crypt
     * @param ORM   $orm
     */
    public function __construct(Crypt $crypt, ORM $orm)
    {
        $this->crypt = $crypt;
        $this->orm   = $orm;
    }

    /**
     * Attempt to authenticate a user.
     *
     * @param User $user
     *
     * @return bool
     */
    public function authenticate(User $user): bool
    {
        $repository = $this->orm->getRepositoryFromClass($user);

        try {
            /** @var User $dbUser */
            $dbUser = $repository
                ->find()
                ->where($user::getUsernameField(), null, $user->getUsernameFieldValue())
                ->getOneOrFail();
        } catch (Exception $exception) {
            return false;
        }

        return $this->isPassword($dbUser, $user->getPasswordFieldValue());
    }

    /**
     * Get the user token.
     *
     * @param User $user
     *
     * @throws CryptException
     *
     * @return string
     */
    public function getToken(User $user): string
    {
        return $this->crypt->encrypt(serialize($user));
    }

    /**
     * Determine if a token is valid.
     *
     * @param string $token
     *
     * @return bool
     */
    public function isValidToken(string $token): bool
    {
        return $this->crypt->isValidEncryptedMessage($token);
    }

    /**
     * Get a user from token.
     *
     * @param string $user
     * @param string $token
     *
     * @return User|null
     */
    public function getUserFromToken(string $user, string $token): ?User
    {
        try {
            /** @var User $userModel */
            $userModel = unserialize(
                $this->crypt->decrypt($token),
                [
                    'allowed_classes' => [
                        $user,
                    ],
                ]
            );
        } catch (Exception $exception) {
            return null;
        }

        return $userModel;
    }

    /**
     * Refresh a user from the data store.
     *
     * @param User $user
     *
     * @return User
     */
    public function getFreshUser(User $user): User
    {
        $repository = $this->orm->getRepositoryFromClass($user);

        /** @var User $freshUser */
        $freshUser = $repository->findOne($user->getIdFieldValue())->getOneOrFail();

        return $freshUser;
    }

    /**
     * Determine if a password verifies against the user's password.
     *
     * @param User   $user
     * @param string $password
     *
     * @return bool
     */
    public function isPassword(User $user, string $password): bool
    {
        return password_verify($password, $user->getPasswordFieldValue());
    }

    /**
     * Update a user's password.
     *
     * @param User   $user
     * @param string $password
     *
     * @return void
     */
    public function updatePassword(User $user, string $password): void
    {
        $user->setPasswordFieldValue($this->hashPassword($password));

        $this->saveUser($user);
    }

    /**
     * Reset a user's password.
     *
     * @param User $user
     *
     * @throws Exception
     *
     * @return void
     */
    public function resetPassword(User $user): void
    {
        $user->setResetTokenFieldValue(Str::generateToken());

        $this->saveUser($user);
    }

    /**
     * Lock a user.
     *
     * @param LockableUser $user
     *
     * @return void
     */
    public function lock(LockableUser $user): void
    {
        $this->lockUnlock($user, true);
    }

    /**
     * Unlock a user.
     *
     * @param LockableUser $user
     *
     * @return void
     */
    public function unlock(LockableUser $user): void
    {
        $this->lockUnlock($user, false);
    }

    /**
     * Hash a plain password.
     *
     * @param string $password
     *
     * @return string
     */
    protected function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Lock or unlock a user.
     *
     * @param LockableUser $user
     * @param bool         $lock
     *
     * @return void
     */
    protected function lockUnlock(LockableUser $user, bool $lock): void
    {
        $user->setLockedFieldValue($lock);

        $this->saveUser($user);
    }

    /**
     * Save a user.
     *
     * @param User $user
     *
     * @return void
     */
    protected function saveUser(User $user): void
    {
        $repository = $this->orm->getRepositoryFromClass($user);

        $repository->save($user, false);
        $repository->persist();
    }
}
