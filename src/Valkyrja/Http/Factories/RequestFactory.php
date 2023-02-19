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

use UnexpectedValueException;
use Valkyrja\Http\Constants\RequestMethod;
use Valkyrja\Http\Constants\StreamType;
use Valkyrja\Http\Requests\JsonRequest;
use Valkyrja\Http\Requests\Request;
use Valkyrja\Http\Streams\Stream;

use function array_key_exists;
use function preg_match;
use function sprintf;

/**
 * Abstract Class RequestFactory.
 *
 * @author Melech Mizrachi
 */
abstract class RequestFactory
{
    /**
     * Create a request from the supplied superglobal values.
     *
     * @param array|null            $server  [optional] $_SERVER superglobal
     * @param array|null            $query   [optional] $_GET superglobal
     * @param array|null            $body    [optional] $_POST superglobal
     * @param array|null            $cookies [optional] $_COOKIE superglobal
     * @param array|null            $files   [optional] $_FILES superglobal
     * @param class-string<Request> $class   [optional] The request class to return
     */
    public static function fromGlobals(
        array $server = null,
        array $query = null,
        array $body = null,
        array $cookies = null,
        array $files = null,
        string $class = Request::class
    ): Request {
        $files ??= $_FILES;

        $server  = ServerFactory::normalizeServer($server ?? $_SERVER);
        $headers = HeaderFactory::marshalHeaders($server);

        if (! empty($files)) {
            $files = FileFactory::normalizeFiles($files);
        }

        if ($cookies === null && array_key_exists('cookie', $headers)) {
            $cookies = CookieFactory::parseCookieHeader($headers['cookie']);
        }

        return new $class(
            UriFactory::marshalUriFromServer($server, $headers),
            $server['REQUEST_METHOD'] ?? RequestMethod::GET,
            new Stream(StreamType::INPUT),
            $headers,
            $server,
            $cookies ?? $_COOKIE,
            $query ?? $_GET,
            $body ?? $_POST,
            static::getProtocolVersionFromServer($server),
            ...$files,
        );
    }

    /**
     * Create a json request.
     *
     * @param array|null $server  [optional] $_SERVER superglobal
     * @param array|null $query   [optional] $_GET superglobal
     * @param array|null $body    [optional] $_POST superglobal
     * @param array|null $cookies [optional] $_COOKIE superglobal
     * @param array|null $files   [optional] $_FILES superglobal
     */
    public static function jsonFromGlobals(
        array $server = null,
        array $query = null,
        array $body = null,
        array $cookies = null,
        array $files = null
    ): Request {
        return self::fromGlobals($server, $query, $body, $cookies, $files, JsonRequest::class);
    }

    /**
     * Return HTTP protocol version (X.Y).
     *
     * @throws UnexpectedValueException
     */
    protected static function getProtocolVersionFromServer(array $server): string
    {
        $serverProtocol = $server['SERVER_PROTOCOL'] ?? null;

        if ($serverProtocol === null) {
            return '1.1';
        }

        if (! preg_match('#^(HTTP/)?(?P<version>[1-9]\d*(?:\.\d)?)$#', $serverProtocol, $matches)) {
            throw new UnexpectedValueException(
                sprintf(
                    'Unrecognized protocol version (%s)',
                    $serverProtocol
                )
            );
        }

        return $matches['version'];
    }
}
