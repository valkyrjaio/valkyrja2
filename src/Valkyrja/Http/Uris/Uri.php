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

namespace Valkyrja\Http\Uris;

use Valkyrja\Http\Constants\Scheme;
use Valkyrja\Http\Exceptions\InvalidPath;
use Valkyrja\Http\Exceptions\InvalidPort;
use Valkyrja\Http\Exceptions\InvalidQuery;
use Valkyrja\Http\Exceptions\InvalidScheme;
use Valkyrja\Http\Uri as Contract;

/**
 * Class Uri.
 *
 * @author Melech Mizrachi
 */
class Uri implements Contract
{
    use UriHelpers;

    /**
     * The URI as a string.
     */
    private ?string $uriString = null;

    /**
     * UriImpl constructor.
     *
     * @param string   $scheme   [optional] The scheme
     * @param string   $userInfo [optional] The user info
     * @param string   $host     [optional] The host
     * @param int|null $port     [optional] The port
     * @param string   $path     [optional] The path
     * @param string   $query    [optional] The query
     * @param string   $fragment [optional] The fragment
     *
     * @throws InvalidPath
     * @throws InvalidPort
     * @throws InvalidQuery
     * @throws InvalidScheme
     */
    public function __construct(
        protected string $scheme = Scheme::EMPTY,
        protected string $userInfo = '',
        protected string $host = '',
        protected int|null $port = null,
        protected string $path = '',
        protected string $query = '',
        protected string $fragment = ''
    ) {
        $this->scheme   = $this->filterScheme($scheme);
        $this->path     = $this->filterPath($path);
        $this->query    = $this->filterQuery($query);
        $this->fragment = $this->filterFragment($fragment);

        $this->validatePort($this->port);
    }

    /**
     * @inheritDoc
     */
    public function isSecure(): bool
    {
        return $this->scheme === 'https';
    }

    /**
     * @inheritDoc
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @inheritDoc
     */
    public function getAuthority(): string
    {
        if (empty($this->host)) {
            return '';
        }

        $authority = $this->host;

        if (! empty($this->userInfo)) {
            $authority = $this->userInfo . '@' . $authority;
        }

        if (! $this->isStandardPort()) {
            $authority .= ':' . ((string) $this->port);
        }

        return $authority;
    }

    /**
     * @inheritDoc
     */
    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    /**
     * @inheritDoc
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @inheritDoc
     */
    public function getPort(): ?int
    {
        return $this->isStandardPort() ? null : $this->port;
    }

    /**
     * @inheritDoc
     */
    public function getHostPort(): string
    {
        $host = $this->host;

        if ($host && $port = $this->port) {
            $host .= ':' . $port;
        }

        return $host;
    }

    /**
     * @inheritDoc
     */
    public function getSchemeHostPort(): string
    {
        $hostPort = $this->getHostPort();
        $scheme   = $this->scheme;

        return $hostPort && $scheme ? $scheme . '://' . $hostPort : $hostPort;
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
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @inheritDoc
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * @inheritDoc
     */
    public function withScheme(string $scheme): static
    {
        $scheme = $this->filterScheme($scheme);

        $new = clone $this;

        $new->scheme = $scheme;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withUserInfo(string $user, string $password = null): static
    {
        $info = $user;

        if ($password) {
            $info .= ':' . $password;
        }

        $new = clone $this;

        $new->userInfo = $info;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withHost(string $host): static
    {
        $new = clone $this;

        $new->host = $host;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withPort(int $port = null): static
    {
        $this->validatePort($port);

        $new = clone $this;

        $new->port = $port;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withPath(string $path): static
    {
        $path = $this->filterPath($path);

        $new = clone $this;

        $new->path = $path;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withQuery(string $query): static
    {
        $query = $this->filterQuery($query);

        $new = clone $this;

        $new->query = $query;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withFragment(string $fragment): static
    {
        $fragment = $this->filterFragment($fragment);

        $new = clone $this;

        $new->fragment = $fragment;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        if ($this->uriString !== null) {
            return $this->uriString;
        }

        $uri = '';

        $uri = $this->addSchemeToUri($uri);
        $uri = $this->addAuthorityToUri($uri);
        $uri = $this->addPathToUri($uri);
        $uri = $this->addQueryToUri($uri);
        $uri = $this->addFragmentToUri($uri);

        return $this->uriString = $uri;
    }
}
