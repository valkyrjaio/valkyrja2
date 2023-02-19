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

namespace Valkyrja\Http\Models;

use DateTimeInterface;
use Valkyrja\Http\Constants\SameSite;
use Valkyrja\Http\Cookie as Contract;
use Valkyrja\Http\Exceptions\InvalidSameSiteTypeException;
use Valkyrja\Model\Models\Model;

use function gmdate;
use function in_array;
use function time;
use function urlencode;

/**
 * Class Cookie.
 *
 * @author Melech Mizrachi
 */
class Cookie extends Model implements Contract
{
    protected const DELETED = 'deleted';

    /**
     * The cookie name.
     */
    public string $name;

    /**
     * The cookie value.
     */
    public ?string $value = null;

    /**
     * The cookie expire time.
     */
    public int $expire;

    /**
     * The cookie path.
     */
    public string $path;

    /**
     * The cookie domain.
     */
    public ?string $domain = null;

    /**
     * Whether the cookie is secure.
     */
    public bool $secure;

    /**
     * Whether the cookie is http only.
     */
    public bool $httpOnly;

    /**
     * Whether the cookie is raw.
     */
    public bool $raw;

    /**
     * Whether the cookie will be available for cross-site requests.
     */
    public ?string $sameSite = null;

    /**
     * Cookie constructor.
     *
     * @param string      $name     The cookie's name
     * @param string|null $value    [optional] The cookie's value
     * @param int|null    $expire   [optional] The time the cookie should expire
     * @param string|null $path     [optional] The path the cookie is available to
     * @param string|null $domain   [optional] The domain the cookie is available to
     * @param bool|null   $secure   [optional] Whether the cookie should only be
     *                              transmitted over a secure HTTPS connection
     * @param bool|null   $httpOnly [optional] Whether the cookie will be made
     *                              accessible only through the HTTP protocol
     * @param bool|null   $raw      [optional] Whether the cookie value should be
     *                              sent with no url encoding
     * @param string|null $sameSite [optional] Whether the cookie will be available
     *                              for cross-site requests
     *
     * @throws InvalidSameSiteTypeException
     */
    public function __construct(
        string $name,
        string $value = null,
        int $expire = null,
        string $path = null,
        string $domain = null,
        bool $secure = null,
        bool $httpOnly = null,
        bool $raw = null,
        string $sameSite = null
    ) {
        $this->name     = $name;
        $this->value    = $value;
        $this->expire   = $expire ?? 0;
        $this->path     = $path ?? '/';
        $this->domain   = $domain;
        $this->secure   = $secure ?? false;
        $this->raw      = $raw ?? false;
        $this->httpOnly = $httpOnly ?? true;

        $this->setSameSite($sameSite);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        $str    = urlencode($this->name) . '=';
        $value  = $this->value ?? static::DELETED;
        $expire = $this->expire ?? 0;
        $maxAge = $this->getMaxAge();

        if ($value === static::DELETED) {
            $expire = time() - 31536001;
            $maxAge = -31536001;
        }

        $str .= urlencode($value);

        if ($expire !== 0) {
            $str .= '; expires='
                . gmdate(
                    DateTimeInterface::COOKIE,
                    $expire
                )
                . '; max-age=' . $maxAge;
        }

        if ($this->path) {
            $str .= '; path=' . $this->path;
        }

        if ($this->domain) {
            $str .= '; domain=' . $this->domain;
        }

        if ($this->secure) {
            $str .= '; secure';
        }

        if ($this->httpOnly) {
            $str .= '; httponly';
        }

        if ($this->sameSite !== null) {
            $str .= '; samesite=' . $this->sameSite;
        }

        return $str;
    }

    /**
     * @inheritDoc
     */
    public function getMaxAge(): int
    {
        return $this->expire > 0
            ? $this->expire - time()
            : 0;
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
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function setValue(string $value = null): static
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExpire(): int
    {
        return $this->expire;
    }

    /**
     * @inheritDoc
     */
    public function setExpire(int $expire): static
    {
        $this->expire = $expire;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDomain(): ?string
    {
        return $this->domain;
    }

    /**
     * @inheritDoc
     */
    public function setDomain(string $domain = null): static
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isSecure(): bool
    {
        return $this->secure;
    }

    /**
     * @inheritDoc
     */
    public function setSecure(bool $secure): static
    {
        $this->secure = $secure;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isHttpOnly(): bool
    {
        return $this->httpOnly;
    }

    /**
     * @inheritDoc
     */
    public function setHttpOnly(bool $httpOnly = false): static
    {
        $this->httpOnly = $httpOnly;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isRaw(): bool
    {
        return $this->raw;
    }

    /**
     * @inheritDoc
     */
    public function setRaw(bool $raw): static
    {
        $this->raw = $raw;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSameSite(): ?string
    {
        return $this->sameSite;
    }

    /**
     * @inheritDoc
     */
    public function setSameSite(string $sameSite = null): static
    {
        if (! in_array($sameSite, [SameSite::LAX, SameSite::STRICT, null], true)) {
            throw new InvalidSameSiteTypeException(
                'The "sameSite" parameter value is not valid.'
            );
        }

        $this->sameSite = $sameSite;

        return $this;
    }
}
