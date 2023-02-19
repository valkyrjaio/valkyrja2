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

use Valkyrja\Auth\Adapter;
use Valkyrja\Auth\AuthenticatedUsers;
use Valkyrja\Auth\Config\Config;
use Valkyrja\Auth\JWTCryptRepository as Contract;
use Valkyrja\Auth\User;
use Valkyrja\Crypt\Crypt;
use Valkyrja\Jwt\Driver as Jwt;
use Valkyrja\Jwt\Jwt as JwtManager;
use Valkyrja\Session\Session;

/**
 * Class JwtCryptRepository.
 *
 * @author Melech Mizrachi
 */
class JwtCryptRepository extends CryptTokenizedRepository implements Contract
{
    /**
     * The Jwt service.
     */
    protected Jwt $jwt;

    /**
     * JWTCryptRepository constructor.
     *
     * @param Adapter            $adapter The adapter
     * @param JWTManager         $jwt     The JWT service
     * @param Crypt              $crypt   The crypt service
     * @param Session            $session The session service
     * @param Config|array       $config  The config
     * @param class-string<User> $user    The user class
     */
    public function __construct(Adapter $adapter, JwtManager $jwt, Crypt $crypt, Session $session, Config|array $config, string $user)
    {
        parent::__construct($adapter, $crypt, $session, $config, $user);

        $this->jwt = $jwt->use();
    }

    /**
     * @inheritDoc
     */
    protected function tokenizeUsers(AuthenticatedUsers $users): string
    {
        return $this->jwt->encode(['token' => parent::tokenizeUsers($users)]);
    }

    /**
     * @inheritDoc
     */
    protected function unTokenizeUsers(string $token): AuthenticatedUsers
    {
        return parent::unTokenizeUsers($this->jwt->decode($token)['token']);
    }
}
