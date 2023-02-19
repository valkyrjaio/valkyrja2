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

namespace Valkyrja\Session;

use Valkyrja\Manager\Manager;
use Valkyrja\Session\Exceptions\InvalidCsrfToken;

/**
 * Interface Session.
 *
 * @author Melech Mizrachi
 *
 * @extends Manager<Driver, Factory>
 */
interface Session extends Manager
{
    /**
     * @inheritDoc
     */
    public function use(string $name = null): Driver;

    /**
     * Start the session.
     */
    public function start(): void;

    /**
     * Get the session id.
     */
    public function getId(): string;

    /**
     * Set the session id.
     *
     * @param string $id The session id
     */
    public function setId(string $id): void;

    /**
     * Get the session name.
     */
    public function getName(): string;

    /**
     * Set the session name.
     *
     * @param string $name The session name
     */
    public function setName(string $name): void;

    /**
     * Is a session active?
     */
    public function isActive(): bool;

    /**
     * Determine whether the session has an item.
     *
     * @param string $id The item id
     */
    public function has(string $id): bool;

    /**
     * Get an item from the session.
     *
     * @param string     $id      The item id
     * @param mixed|null $default The default value
     */
    public function get(string $id, mixed $default = null): mixed;

    /**
     * Set an item into the session.
     *
     * @param string $id    The id
     * @param mixed  $value The value
     */
    public function set(string $id, mixed $value): void;

    /**
     * Remove a session item.
     *
     * @param string $id The item id
     */
    public function remove(string $id): bool;

    /**
     * Get all items in the session.
     */
    public function all(): array;

    /**
     * Generate a csrf token for a unique token id.
     *
     * @param string $id The csrf unique token id
     */
    public function generateCsrfToken(string $id): string;

    /**
     * Validate a csrf token.
     *
     * @param string $id    The csrf unique token id
     * @param string $token The token to validate
     *
     * @throws InvalidCsrfToken
     */
    public function validateCsrfToken(string $id, string $token): void;

    /**
     * Determine if a csrf token is valid.
     *
     * @param string $id    The csrf unique token id
     * @param string $token The token to validate
     */
    public function isCsrfTokenValid(string $id, string $token): bool;

    /**
     * Clear the local session.
     */
    public function clear(): void;

    /**
     * Destroy the session.
     */
    public function destroy(): void;
}
