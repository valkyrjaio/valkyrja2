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
use Valkyrja\Auth\CryptTokenizedRepository as Contract;
use Valkyrja\Auth\User;
use Valkyrja\Crypt\Crypt as CryptManager;
use Valkyrja\Crypt\Driver as Crypt;
use Valkyrja\Crypt\Exceptions\CryptException;
use Valkyrja\Session\Session;

/**
 * Class CryptTokenizedRepository.
 *
 * @author Melech Mizrachi
 */
class CryptTokenizedRepository extends TokenizedRepository implements Contract
{
    /**
     * The crypt.
     */
    protected Crypt $crypt;

    /**
     * CryptTokenizedRepository constructor.
     *
     * @param Adapter            $adapter The adapter
     * @param CryptManager       $crypt   The crypt service
     * @param Session            $session The session
     * @param Config|array       $config  The config
     * @param class-string<User> $user    The user class
     */
    public function __construct(Adapter $adapter, CryptManager $crypt, Session $session, Config|array $config, string $user)
    {
        parent::__construct($adapter, $session, $config, $user);

        $this->crypt = $crypt->use();
    }

    /**
     * @inheritDoc
     *
     * @throws CryptException
     */
    protected function tokenizeUsers(AuthenticatedUsers $users): string
    {
        return $this->crypt->encryptArray($users->asArray());
    }

    /**
     * @inheritDoc
     *
     * @throws CryptException
     */
    protected function unTokenizeUsers(string $token): AuthenticatedUsers
    {
        return $this->usersModel::fromArray($this->crypt->decryptArray($token));
    }
}
