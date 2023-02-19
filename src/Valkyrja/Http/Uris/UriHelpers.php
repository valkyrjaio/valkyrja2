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

use Valkyrja\Http\Constants\Port;
use Valkyrja\Http\Constants\Scheme;
use Valkyrja\Http\Exceptions\InvalidPath;
use Valkyrja\Http\Exceptions\InvalidPort;
use Valkyrja\Http\Exceptions\InvalidQuery;
use Valkyrja\Http\Exceptions\InvalidScheme;

use function ltrim;
use function preg_replace;
use function strtolower;

/**
 * Trait UriHelpers.
 *
 * @author Melech Mizrachi
 *
 * @property string   $host
 * @property int|null $port
 * @property string   $scheme
 */
trait UriHelpers
{
    /**
     * Validate a port.
     *
     * @param int|null $port The port
     *
     * @throws InvalidPort
     */
    protected function validatePort(int $port = null): void
    {
        if (! Port::isValid($port)) {
            throw new InvalidPort("Invalid port `%$port` specified; must be a valid TCP/UDP port");
        }
    }

    /**
     * Filter a scheme.
     *
     * @param string $scheme The scheme
     *
     * @throws InvalidScheme
     */
    protected function filterScheme(string $scheme): string
    {
        $scheme = strtolower($scheme);
        $scheme = (string) preg_replace('#:(//)?$#', '', $scheme);

        if (! $scheme) {
            return '';
        }

        $this->validateScheme($scheme);

        return $scheme;
    }

    /**
     * Validate a scheme.
     *
     * @param string $scheme The scheme
     *
     * @throws InvalidScheme
     */
    protected function validateScheme(string $scheme): void
    {
        if (! Scheme::isValid($scheme)) {
            throw new InvalidScheme("Invalid scheme `$scheme` specified; must be either `http` or `https`");
        }
    }

    /**
     * Filter a path.
     *
     * @param string $path The path
     *
     * @throws InvalidPath
     */
    protected function filterPath(string $path): string
    {
        $this->validatePath($path);

        // TODO: Filter path

        return '/' . ltrim($path, '/');
    }

    /**
     * Validate a path.
     *
     * @param string $path The path
     *
     * @throws InvalidPath
     */
    protected function validatePath(string $path): void
    {
        if (str_contains($path, '?')) {
            throw new InvalidPath("Invalid path of `$path` provided; must not contain a query string");
        }

        if (str_contains($path, '#')) {
            throw new InvalidPath("Invalid path of `$path` provided; must not contain a URI fragment");
        }
    }

    /**
     * Filter a query.
     *
     * @param string $query The query
     *
     * @throws InvalidQuery
     */
    protected function filterQuery(string $query): string
    {
        $this->validateQuery($query);

        // TODO: Filter query

        return $query;
    }

    /**
     * Validate a query.
     *
     * @param string $query The query
     *
     * @throws InvalidQuery
     */
    protected function validateQuery(string $query): void
    {
        if (str_contains($query, '#')) {
            throw new InvalidQuery("Invalid query string of `$query` provided; must not contain a URI fragment");
        }
    }

    /**
     * Filter a fragment.
     *
     * @param string $fragment The fragment
     */
    protected function filterFragment(string $fragment): string
    {
        $this->validateFragment($fragment);

        // TODO: Filter fragment

        return $fragment;
    }

    /**
     * Validate a fragment.
     *
     * @param string $fragment The fragment
     */
    protected function validateFragment(string $fragment): void
    {
    }

    /**
     * Determine whether this uri is on a standard port for the scheme.
     */
    protected function isStandardPort(): bool
    {
        if (! $this->scheme) {
            return $this->host && $this->port === null;
        }

        if (! $this->host || $this->port === null) {
            return true;
        }

        return $this->isStandardUnsecurePort() || $this->isStandardSecurePort();
    }

    /**
     * Is standard HTTP port.
     */
    protected function isStandardUnsecurePort(): bool
    {
        return $this->scheme === Scheme::HTTP && $this->port === Port::HTTP;
    }

    /**
     * Is standard HTTPS port.
     */
    protected function isStandardSecurePort(): bool
    {
        return $this->scheme === Scheme::HTTPS && $this->port === Port::HTTPS;
    }

    /**
     * Add scheme to uri.
     *
     * @param string $uri The uri
     */
    protected function addSchemeToUri(string $uri): string
    {
        if ($scheme = $this->scheme) {
            $uri .= $scheme . ':';
        }

        return $uri;
    }

    /**
     * Add authority to uri.
     *
     * @param string $uri The uri
     */
    protected function addAuthorityToUri(string $uri): string
    {
        if ($authority = $this->getAuthority()) {
            $uri .= '//' . $authority;
        }

        return $uri;
    }

    /**
     * Add path to uri.
     *
     * @param string $uri The uri
     */
    protected function addPathToUri(string $uri): string
    {
        if ($path = $this->path) {
            if ($path[0] !== '/') {
                $path = '/' . $path;
            }

            $uri .= $path;
        }

        return $uri;
    }

    /**
     * Add query to uri.
     *
     * @param string $uri The uri
     */
    protected function addQueryToUri(string $uri): string
    {
        if ($query = $this->query) {
            $uri .= '?' . $query;
        }

        return $uri;
    }

    /**
     * Add fragment to uri.
     *
     * @param string $uri The uri
     */
    protected function addFragmentToUri(string $uri): string
    {
        if ($fragment = $this->fragment) {
            $uri .= '#' . $fragment;
        }

        return $uri;
    }

    /**
     * Retrieve the authority component of the URI.
     * If no authority information is present, this method MUST return an empty
     * string.
     * The authority syntax of the URI is:
     * <pre>
     * [user-info@]host[:port]
     * </pre>
     * If the port component is not set or is the standard port for the current
     * scheme, it SHOULD NOT be included.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2
     *
     * @return string the URI authority, in "[user-info@]host[:port]" format
     */
    abstract public function getAuthority(): string;
}
