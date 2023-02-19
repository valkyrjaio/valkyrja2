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

namespace Valkyrja\Http\Factories;

use stdClass;
use Valkyrja\Http\Exceptions\InvalidPath;
use Valkyrja\Http\Exceptions\InvalidPort;
use Valkyrja\Http\Exceptions\InvalidQuery;
use Valkyrja\Http\Exceptions\InvalidScheme;
use Valkyrja\Http\Uri;
use Valkyrja\Http\Uris\Uri as HttpUri;

use function array_change_key_case;
use function array_key_exists;
use function explode;
use function implode;
use function is_array;
use function ltrim;
use function preg_match;
use function preg_replace;
use function strlen;
use function strpos;
use function strrpos;
use function strtolower;
use function substr;

/**
 * Abstract Class UriFactory.
 *
 * @author Melech Mizrachi
 */
abstract class UriFactory
{
    /**
     * Marshal the URI from the $_SERVER array and headers.
     *
     * @throws InvalidQuery
     * @throws InvalidPort
     * @throws InvalidPath
     * @throws InvalidScheme
     */
    public static function marshalUriFromServer(array $server, array $headers): Uri
    {
        $uri = new HttpUri();

        // URI scheme
        $scheme = 'http';
        $https  = $server['HTTPS'] ?? null;

        if (($https && $https !== 'off')
            || self::getHeader('x-forwarded-proto', $headers, false) === 'https'
        ) {
            $scheme = 'https';
        }

        $uri = $uri->withScheme($scheme);

        // Set the host
        /** @var stdClass $accumulator */
        $accumulator = (object) ['host' => '', 'port' => null];

        self::marshalHostAndPortFromHeaders($accumulator, $server, $headers);

        /** @var string $host */
        $host = $accumulator->host;
        /** @var int|null $port */
        $port = $accumulator->port;

        if (! empty($host)) {
            $uri = $uri->withHost($host);

            if ($port !== null) {
                $uri = $uri->withPort($port);
            }
        }

        // URI path
        $path = self::marshalRequestUri($server);
        $path = self::stripQueryString($path);

        // URI query
        $query = '';

        if (isset($server['QUERY_STRING'])) {
            $query = ltrim($server['QUERY_STRING'], '?');
        }

        // URI fragment
        $fragment = '';

        if (str_contains($path, '#')) {
            [$path, $fragment] = explode('#', $path);
        }

        return $uri->withPath($path)->withFragment($fragment)->withQuery($query);
    }

    /**
     * Search for a header value.
     * Does a case-insensitive search for a matching header.
     * If found, it is returned as a string, using comma concatenation.
     * If not, the $default is returned.
     */
    public static function getHeader(string $header, array $headers, mixed $default = null): string
    {
        $header  = strtolower($header);
        $headers = array_change_key_case($headers);

        if (array_key_exists($header, $headers)) {
            return is_array($headers[$header]) ? implode(', ', $headers[$header]) : $headers[$header];
        }

        return (string) ($default ?? '');
    }

    /**
     * Marshal the host and port from HTTP headers and/or the PHP environment.
     */
    public static function marshalHostAndPortFromHeaders(stdClass $accumulator, array $server, array $headers): void
    {
        if (self::getHeader('host', $headers, false)) {
            self::marshalHostAndPortFromHeader($accumulator, self::getHeader('host', $headers));

            return;
        }

        if (! isset($server['SERVER_NAME'])) {
            return;
        }

        $accumulator->host = $server['SERVER_NAME'];

        if (isset($server['SERVER_PORT'])) {
            $accumulator->port = (int) $server['SERVER_PORT'];
        }

        if (! isset($server['SERVER_ADDR']) || ! preg_match('/^\[[0-9a-fA-F\:]+\]$/', $accumulator->host)) {
            return;
        }

        // Misinterpreted IPv6-Address
        // Reported for Safari on Windows
        self::marshalIpv6HostAndPort($accumulator, $server);
    }

    /**
     * Detect the base URI for the request.
     * Looks at a variety of criteria in order to attempt to autodetect a base
     * URI, including rewrite URIs, proxy URIs, etc.
     * From ZF2's Zend\Http\PhpEnvironment\Request class.
     *
     * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
     * @license   http://framework.zend.com/license/new-bsd New BSD License
     */
    public static function marshalRequestUri(array $server): string
    {
        // IIS7 with URL Rewrite: make sure we get the unencoded url
        // (double slash problem).
        $iisUrlRewritten = $server['IIS_WasUrlRewritten'] ?? null;
        $unencodedUrl    = $server['UNENCODED_URL'] ?? '';

        if ($iisUrlRewritten === '1' && ! empty($unencodedUrl)) {
            return $unencodedUrl;
        }

        $requestUri = $server['REQUEST_URI'] ?? null;

        // Check this first so IIS will catch.
        $httpXRewriteUrl = $server['HTTP_X_REWRITE_URL'] ?? null;

        if ($httpXRewriteUrl !== null) {
            $requestUri = $httpXRewriteUrl;
        }

        // Check for IIS 7.0 or later with ISAPI_Rewrite
        $httpXOriginalUrl = $server['HTTP_X_ORIGINAL_URL'] ?? null;

        if ($httpXOriginalUrl !== null) {
            $requestUri = $httpXOriginalUrl;
        }

        if ($requestUri !== null) {
            return preg_replace('#^[^/:]+://[^/]+#', '', $requestUri);
        }

        $origPathInfo = $server['ORIG_PATH_INFO'] ?? null;

        if (empty($origPathInfo)) {
            return '/';
        }

        return $origPathInfo;
    }

    /**
     * Strip the query string from a path.
     */
    public static function stripQueryString(string $path): string
    {
        if (($queryPos = strpos($path, '?')) !== false) {
            return substr($path, 0, $queryPos);
        }

        return $path;
    }

    /**
     * Marshal the host and port from the request header.
     */
    private static function marshalHostAndPortFromHeader(stdClass $accumulator, string|array $host): void
    {
        if (is_array($host)) {
            $host = implode(', ', $host);
        }

        $accumulator->host = $host;
        $accumulator->port = null;

        // Works for regname, IPv4 & IPv6
        if (preg_match('|\:(\d+)$|', $accumulator->host, $matches)) {
            $accumulator->host = substr($accumulator->host, 0, -1 * (strlen($matches[1]) + 1));
            $accumulator->port = (int) $matches[1];
        }
    }

    /**
     * Marshal host/port from misinterpreted IPv6 address.
     */
    private static function marshalIpv6HostAndPort(stdClass $accumulator, array $server): void
    {
        $accumulator->host = '[' . $server['SERVER_ADDR'] . ']';
        $accumulator->port = $accumulator->port ?: 80;

        $portOffset = strrpos($accumulator->host, ':');

        if ($portOffset !== false && $accumulator->port . ']' === substr($accumulator->host, $portOffset + 1)) {
            // The last digit of the IPv6-Address has been taken as port
            // Unset the port so the default port can be used
            $accumulator->port = null;
        }
    }
}
