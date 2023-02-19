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

namespace Valkyrja\Auth\Repositories;

use Throwable;
use Valkyrja\Auth\AuthenticatedUsers;
use Valkyrja\Auth\Constants\HeaderValue;
use Valkyrja\Auth\Exceptions\AuthRuntimeException;
use Valkyrja\Auth\Exceptions\InvalidAuthenticationException;
use Valkyrja\Auth\Exceptions\InvalidCurrentAuthenticationException;
use Valkyrja\Auth\Exceptions\MissingTokenizableUserRequiredFieldsException;
use Valkyrja\Auth\Exceptions\TokenizationException;
use Valkyrja\Auth\TokenizableUser;
use Valkyrja\Auth\TokenizedRepository as Contract;
use Valkyrja\Auth\User;
use Valkyrja\Http\Constants\Header;
use Valkyrja\Http\Request;

/**
 * Abstract Class TokenizedRepository.
 *
 * @author Melech Mizrachi
 *
 * @property TokenizableUser        $user
 * @property TokenizableUser|string $userEntityName
 */
abstract class TokenizedRepository extends Repository implements Contract
{
    /**
     * The token.
     */
    protected string $token;

    /**
     * @inheritDoc
     *
     * @throws TokenizationException
     */
    public function setUser(User $user): static
    {
        parent::setUser($user);

        $this->resetToken();

        return $this;
    }

    /**
     * @inheritDoc
     *
     * @throws TokenizationException
     */
    public function setUsers(AuthenticatedUsers $users): static
    {
        parent::setUsers($users);

        $this->resetToken();

        return $this;
    }

    /**
     * @inheritDoc
     *
     * @throws TokenizationException
     */
    public function authenticate(User $user): static
    {
        parent::authenticate($user);

        $this->resetToken();

        return $this;
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable
     */
    public function authenticateFromSession(): static
    {
        $token = $this->getTokenFromSession();

        if ($token === null) {
            throw new AuthRuntimeException('Invalid token provided.');
        }

        return $this->authenticateFromToken($token);
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable
     */
    public function authenticateFromRequest(Request $request): static
    {
        $token = $this->getTokenFromRequest($request);

        return $this->authenticateFromToken($token);
    }

    /**
     * @inheritDoc
     *
     * @throws TokenizationException
     */
    public function setSession(): static
    {
        $this->session->set($this->user::getTokenSessionId(), $this->getToken());

        return $this;
    }

    /**
     * Get the user token.
     *
     * @throws TokenizationException
     */
    public function getToken(): string
    {
        return $this->token ??= $this->getFreshToken();
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable
     */
    public function authenticateFromToken(string $token): static
    {
        $user = $this->getUserFromToken($token);

        return $this->authenticateWithUser($user);
    }

    /**
     * Try tokenizing the users.
     *
     * @param AuthenticatedUsers $users The users
     *
     * @throws TokenizationException
     */
    protected function tryTokenizingUsers(AuthenticatedUsers $users): string
    {
        return $this->tokenizeUsers($users);
    }

    /**
     * Get the required fields.
     *
     * @return string[]
     */
    protected function getRequiredFields(): array
    {
        $requiredFields = [
            $this->userEntityName::getIdField(),
        ];

        // If the always authenticate flag is on in the config we need the password and authentication fields to be part
        // of the tokenized user
        if ($this->config['alwaysAuthenticate']) {
            $requiredFields[] = $this->userEntityName::getPasswordField();

            $requiredFields = array_merge($requiredFields, $this->user::getAuthenticationFields());
        }

        return $requiredFields;
    }

    /**
     * Ensure required fields for tokenization.
     *
     * @param AuthenticatedUsers $users The users
     */
    protected function ensureRequiredFieldsForTokenization(AuthenticatedUsers $users): void
    {
        // Required fields that should exist within the tokenized user
        $requiredFields = $this->getRequiredFields();

        /** @var TokenizableUser $userFromCollection */
        foreach ($users->all() as $userFromCollection) {
            $userAsTokenizableArray = $userFromCollection->asTokenizableArray();

            foreach ($requiredFields as $requiredField) {
                if (! isset($userAsTokenizableArray[$requiredField])) {
                    $entityName = $this->userEntityName;

                    throw new MissingTokenizableUserRequiredFieldsException("Required field `$requiredField` is not being returned in $entityName::asTokenizableArray()");
                }
            }
        }
    }

    /**
     * Get the user token from session.
     */
    protected function getTokenFromSession(): ?string
    {
        return $this->session->get($this->userEntityName::getTokenSessionId());
    }

    /**
     * Get the token from a request.
     *
     * @param Request $request The request
     */
    protected function getTokenFromRequest(Request $request): string
    {
        [$bearer, $token] = explode(' ', $request->getHeaderLine(Header::AUTHORIZATION));

        if ($bearer !== HeaderValue::BEARER || ! $token) {
            throw new InvalidAuthenticationException('Invalid token structure.');
        }

        return $token;
    }

    /**
     * Store the user token in session.
     *
     * @param string|null $token [optional] The token to store
     *
     * @throws TokenizationException
     */
    protected function storeToken(string $token = null): static
    {
        $this->session->set($this->user::getTokenSessionId(), $token ?? $this->getToken());

        return $this;
    }

    /**
     * Get a user from a token.
     *
     * @param string $token The token
     *
     * @throws Throwable
     */
    protected function getUserFromToken(string $token): User
    {
        try {
            $users = $this->tryUnTokenizingUsers($token);
        } catch (Throwable $exception) {
            $this->resetAfterUnAuthentication();

            throw $exception;
        }

        $this->users = $users;

        $current = $this->users->getCurrent();

        if (! $current) {
            throw new InvalidCurrentAuthenticationException('No current authenticated user.');
        }

        return $current;
    }

    /**
     * Attempt to get users from token.
     *
     * @param string $token The token
     *
     * @throws TokenizationException
     */
    protected function tryUnTokenizingUsers(string $token): AuthenticatedUsers
    {
        try {
            return $this->unTokenizeUsers($token);
        } catch (Throwable $exception) {
            throw new TokenizationException($exception->getMessage(), (int) $exception->getCode(), $exception);
        }
    }

    /**
     * Get a fresh token.
     */
    protected function getFreshToken(): string
    {
        $user  = $this->user;
        $users = $this->users;

        $this->ensureRequiredFieldsForTokenization($users);
        $token = $this->tryTokenizingUsers($users);

        $user::setTokenized($token);

        return $token;
    }

    /**
     * Set the user token.
     *
     * @throws TokenizationException
     */
    protected function resetToken(): void
    {
        $this->user::setTokenized($this->token = $this->getFreshToken());
    }

    /**
     * Tokenize the users.
     *
     * @param AuthenticatedUsers $users The users
     */
    abstract protected function tokenizeUsers(AuthenticatedUsers $users): string;

    /**
     * Un-tokenize users.
     *
     * @param string $token The token
     */
    abstract protected function unTokenizeUsers(string $token): AuthenticatedUsers;
}
