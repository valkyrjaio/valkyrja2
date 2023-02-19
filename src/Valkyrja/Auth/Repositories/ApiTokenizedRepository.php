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

use Valkyrja\Auth\TokenizableUser;

/**
 * Class ApiTokenizedRepository.
 *
 * Use this repository in case an api provides a token you need to use for requests,
 *  but you cannot decrypt it in your application. Note that this will still require a user to
 *  be set via setUser() to work properly.
 *
 * @author Melech Mizrachi
 *
 * @property TokenizableUser $user
 */
class ApiTokenizedRepository extends Repository implements \Valkyrja\Auth\TokenizedRepository
{
    /**
     * The token.
     */
    protected string $token;

    /**
     * @inheritDoc
     */
    public function authenticateFromSession(): static
    {
        parent::authenticateFromSession();

        $this->user::setTokenized($this->getToken());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSession(): static
    {
        parent::setSession();

        $this->session->set($this->user::getTokenSessionId(), $this->getToken());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getToken(): string
    {
        return $this->token ??= $this->session->get($this->user::getTokenSessionId());
    }

    /**
     * @inheritDoc
     */
    public function authenticateFromToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }
}
