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

namespace Valkyrja\Http;

use Valkyrja\Http\Exceptions\InvalidSameSiteTypeException;
use Valkyrja\Model\Model;

/**
 * Interface Cookie.
 *
 * @author Melech Mizrachi
 */
interface Cookie extends Model
{
    /**
     * Gets the max age of the cookie.
     */
    public function getMaxAge(): int;

    /**
     * Get the cookie's name.
     */
    public function getName(): string;

    /**
     * Set the cookie's name.
     *
     * @param string $name The name
     */
    public function setName(string $name): static;

    /**
     * Get the cookie's value.
     */
    public function getValue(): ?string;

    /**
     * Set the cookie's value.
     *
     * @param string|null $value The value
     */
    public function setValue(string $value = null): static;

    /**
     * Get expire time for the cookie.
     */
    public function getExpire(): int;

    /**
     * Set expire time for the cookie.
     *
     * @param int $expire The expire time
     */
    public function setExpire(int $expire): static;

    /**
     * Get the path the cookie is available to.
     */
    public function getPath(): string;

    /**
     * Set the path the cookie is available to.
     *
     * @param string $path The path
     */
    public function setPath(string $path): static;

    /**
     * Get the domain the cookie is available to.
     */
    public function getDomain(): ?string;

    /**
     * Set the domain the cookie is available to.
     *
     * @param string|null $domain The domain
     */
    public function setDomain(string $domain = null): static;

    /**
     * Whether the cookie should only be transmitted over a secure HTTPS
     * connection.
     */
    public function isSecure(): bool;

    /**
     * Set whether the cookie should only be transmitted over a secure HTTPS
     * connection.
     */
    public function setSecure(bool $secure): static;

    /**
     * Whether the cookie will be made accessible only through the HTTP
     * protocol.
     */
    public function isHttpOnly(): bool;

    /**
     * Set whether the cookie will be made accessible only through the HTTP protocol.
     *
     * @param bool $httpOnly [optional] The flag
     */
    public function setHttpOnly(bool $httpOnly = false): static;

    /**
     * Whether the cookie value should be sent with no url encoding.
     */
    public function isRaw(): bool;

    /**
     * Set whether the cookie value should be sent with no url encoding.
     */
    public function setRaw(bool $raw): static;

    /**
     * Get whether the cookie will be available for cross-site requests.
     */
    public function getSameSite(): ?string;

    /**
     * Set whether the cookie will be available for cross-site requests.
     *
     * @throws InvalidSameSiteTypeException
     */
    public function setSameSite(string $sameSite = null): static;
}
