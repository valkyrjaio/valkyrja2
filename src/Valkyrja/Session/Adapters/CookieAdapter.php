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

namespace Valkyrja\Session\Adapters;

use Valkyrja\Crypt\Crypt;
use Valkyrja\Crypt\Exceptions\CryptException;
use Valkyrja\Http\Request;
use Valkyrja\Session\Exceptions\SessionStartFailure;

use function headers_sent;
use function session_start;

/**
 * Class CookieAdapter.
 *
 * @author Melech Mizrachi
 */
class CookieAdapter extends PHPAdapter
{
    /**
     * The crypt.
     */
    protected Crypt $crypt;

    /**
     * The request.
     */
    protected Request $request;

    /**
     * CookieAdapter constructor.
     *
     * @param Crypt       $crypt       The crypt
     * @param Request     $request     The request
     * @param array       $config      The config
     * @param string|null $sessionId   [optional] The session id
     * @param string|null $sessionName [optional] The session name
     */
    public function __construct(
        Crypt $crypt,
        Request $request,
        array $config,
        string $sessionId = null,
        string $sessionName = null
    ) {
        parent::__construct($config, $sessionId, $sessionName);

        $this->crypt   = $crypt;
        $this->request = $request;
    }

    /**
     * @inheritDoc
     *
     * @throws CryptException
     */
    public function start(): void
    {
        // If the session is already active
        if ($this->isActive() || headers_sent()) {
            // No need to reactivate
            return;
        }

        // If the session failed to start
        if (! session_start()) {
            // Throw a new exception
            throw new SessionStartFailure('The session failed to start!');
        }

        $dataString = $this->request->getCookieParam($this->getId());

        // Set the data
        $this->data = $dataString ? $this->crypt->decryptArray($dataString) : [];
    }

    /**
     * @inheritDoc
     *
     * @throws CryptException
     */
    public function set(string $id, $value): void
    {
        $this->data[$id] = $value;

        $this->updateCookieSession();
    }

    /**
     * @inheritDoc
     *
     * @throws CryptException
     */
    public function remove(string $id): bool
    {
        if (! $this->has($id)) {
            return false;
        }

        unset($this->data[$id]);

        $this->updateCookieSession();

        return true;
    }

    /**
     * @inheritDoc
     *
     * @throws CryptException
     */
    public function clear(): void
    {
        parent::clear();

        $this->updateCookieSession();
    }

    /**
     * @inheritDoc
     *
     * @throws CryptException
     */
    public function destroy(): void
    {
        parent::destroy();

        $this->updateCookieSession();
    }

    /**
     * Update the cache session.
     *
     * @throws CryptException
     */
    protected function updateCookieSession(): void
    {
        setcookie(
            $this->getId(),
            $this->crypt->encryptArray($this->data),
            0,
            '/',
            '',
            false,
            true
        );
    }
}
