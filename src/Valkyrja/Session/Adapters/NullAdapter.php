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

use Exception;
use Valkyrja\Session\Adapter as Contract;
use Valkyrja\Session\Exceptions\InvalidCsrfToken;
use Valkyrja\Session\Exceptions\InvalidSessionId;
use Valkyrja\Session\Exceptions\SessionStartFailure;

use function bin2hex;
use function hash_equals;
use function is_string;
use function preg_match;
use function random_bytes;

/**
 * Class NullAdapter.
 *
 * @author Melech Mizrachi
 */
class NullAdapter implements Contract
{
    /**
     * The config.
     */
    protected array $config;

    /**
     * The session id.
     */
    protected string $id;

    /**
     * The session name.
     */
    protected string $name;

    /**
     * The session data.
     */
    protected array $data = [];

    /**
     * NullAdapter constructor.
     *
     * @param array       $config      The config
     * @param string|null $sessionId   [optional] The session id
     * @param string|null $sessionName [optional] The session name
     *
     * @throws InvalidSessionId
     * @throws SessionStartFailure
     */
    public function __construct(array $config, string $sessionId = null, string $sessionName = null)
    {
        $this->config = $config;

        $sessionId   = $sessionId ?? $config['id'];
        $sessionName = $sessionName ?? $config['name'];

        // If a session id is provided
        if ($sessionId !== null) {
            // Set the id
            $this->setId($sessionId);
        }

        // If a session name is provided
        if ($sessionName !== null) {
            // Set the name
            $this->setName($sessionName);
        }

        // Start the session
        $this->start();
    }

    /**
     * @inheritDoc
     */
    public function start(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setId(string $id): void
    {
        if (! preg_match('/^[-,a-zA-Z0-9]{1,128}$/', $id)) {
            throw new InvalidSessionId(
                "The session id, '$id', is invalid! "
                . 'Session id can only contain alpha numeric characters, dashes, commas, '
                . 'and be at least 1 character in length but up to 128 characters long.'
            );
        }

        $this->id = $id;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @inheritDoc
     */
    public function isActive(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function has(string $id): bool
    {
        return isset($this->data[$id]);
    }

    /**
     * @inheritDoc
     */
    public function get(string $id, mixed $default = null): mixed
    {
        return $this->data[$id] ?? $default;
    }

    /**
     * @inheritDoc
     */
    public function set(string $id, $value): void
    {
        $this->data[$id] = $value;
    }

    /**
     * @inheritDoc
     */
    public function remove(string $id): bool
    {
        if (! $this->has($id)) {
            return false;
        }

        unset($this->data[$id]);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     *
     * @throws Exception
     */
    public function generateCsrfToken(string $id): string
    {
        $token = bin2hex(random_bytes(64));

        $this->set($id, $token);

        return $token;
    }

    /**
     * @inheritDoc
     */
    public function validateCsrfToken(string $id, string $token): void
    {
        if (! $this->isCsrfTokenValid($id, $token)) {
            throw new InvalidCsrfToken("CSRF token id: `$id` has invalid token of `$token` provided");
        }
    }

    /**
     * @inheritDoc
     */
    public function isCsrfTokenValid(string $id, string $token): bool
    {
        if (! $this->has($id)) {
            return false;
        }

        $sessionToken = $this->get($id);

        if (is_string($sessionToken) && hash_equals($token, $sessionToken)) {
            $this->remove($id);

            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function clear(): void
    {
        $this->data = [];
    }

    /**
     * @inheritDoc
     */
    public function destroy(): void
    {
        $this->data = [];
    }
}
