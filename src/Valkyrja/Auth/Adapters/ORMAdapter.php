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
use Valkyrja\Auth\Config\Config;
use Valkyrja\Auth\Exceptions\InvalidRegistrationException;
use Valkyrja\Auth\ORMAdapter as Contract;
use Valkyrja\Auth\User;
use Valkyrja\Orm\Orm;
use Valkyrja\Orm\Repository;

/**
 * Class Adapter.
 *
 * @author Melech Mizrachi
 */
class ORMAdapter extends Adapter implements Contract
{
    /**
     * Adapter constructor.
     *
     * @param Orm          $orm    The orm
     * @param Config|array $config The config
     */
    public function __construct(
        protected Orm $orm,
        Config|array $config
    ) {
        parent::__construct($config);
    }

    /**
     * @inheritDoc
     */
    public function authenticate(User $user): bool
    {
        $dbUser = $this->retrieve($user);

        // If there is a user and the password matches
        if ($dbUser && $this->verifyPassword($dbUser, $user->__get($user::getPasswordField()))) {
            // Update the user model with all the properties from the database
            $user->updateProperties($dbUser->asStorableArray());

            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function retrieve(User $user): ?User
    {
        $loginFields = $user::getAuthenticationFields();
        $find        = $this->getUserRepository($user)->find();

        // Iterate through the login fields
        foreach ($loginFields as $loginField) {
            // Set a where clause for each field
            $find->where($loginField, null, $user->__get($loginField));
        }

        /** @var User|null $result */
        $result = $find->getOneOrNull();

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function retrieveByResetToken(User $user, string $resetToken): ?User
    {
        $resetTokenField = $user::getResetTokenField();

        /** @var User|null $result */
        $result = $this->getUserRepository($user)
            ->find()
            ->where($resetTokenField, null, $user->__get($resetTokenField))
            ->getOneOrNull();

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function retrieveById(User $user): User
    {
        /** @var User $result */
        $result = $this->getUserRepository($user)
            ->findOne($user->__get($user::getIdField()))
            ->getOneOrFail();

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function save(User $user): void
    {
        // Get the ORM repository
        $repository = $this->getUserRepository($user);

        $repository->save($user, false);
        $repository->persist();
    }

    /**
     * @inheritDoc
     */
    public function create(User $user): bool
    {
        $repository    = $this->getUserRepository($user);
        $passwordField = $user::getPasswordField();

        try {
            if ($this->retrieve($user)) {
                return false;
            }

            $user->__set($passwordField, $this->hashPassword($user->__get($passwordField)));

            $this->orm->ensureTransaction();
            $repository->create($user);
            $repository->persist();

            return true;
        } catch (Exception $exception) {
            throw new InvalidRegistrationException($exception->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function getOrm(): Orm
    {
        return $this->orm;
    }

    /**
     * Get an ORM repository for the user.
     *
     * @param User $user The user
     */
    protected function getUserRepository(User $user): Repository
    {
        return $this->orm->getRepositoryFromClass($user);
    }
}
