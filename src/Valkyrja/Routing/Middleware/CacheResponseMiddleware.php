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

namespace Valkyrja\Routing\Middleware;

use Valkyrja\Http\Constants\StatusCode;
use Valkyrja\Http\Request;
use Valkyrja\Http\Response;
use Valkyrja\Support\Directory;

use function base64_decode;
use function base64_encode;
use function file_get_contents;
use function file_put_contents;
use function filemtime;
use function is_file;
use function md5;
use function serialize;
use function time;
use function unlink;
use function unserialize;

/**
 * Class CacheResponseMiddleware.
 *
 * @author Melech Mizrachi
 */
class CacheResponseMiddleware extends Middleware
{
    /**
     * @inheritDoc
     */
    public static function before(Request $request): Request|Response
    {
        $filePath = Directory::cachePath('response/' . static::getHashedPath($request));

        if (is_file($filePath) && ! self::getRouter()->debug()) {
            if (time() - filemtime($filePath) > static::getTtl()) {
                unlink($filePath);

                return $request;
            }

            $cache = file_get_contents($filePath);
            /** @var Response $response */
            $response = unserialize(
                base64_decode($cache, true),
                [
                    'allowed_classes' => true,
                ]
            );

            if ($response->getStatusCode() >= StatusCode::INTERNAL_SERVER_ERROR) {
                return $request;
            }

            return $response;
        }

        return $request;
    }

    /**
     * @inheritDoc
     */
    public static function terminate(Request $request, Response $response): void
    {
        file_put_contents(
            Directory::cachePath('response/' . static::getHashedPath($request)),
            base64_encode(serialize($response))
        );
    }

    /**
     * Get the ttl.
     */
    protected static function getTtl(): int
    {
        return 1800;
    }

    /**
     * Get a hashed version of the request path.
     */
    protected static function getHashedPath(Request $request): string
    {
        return md5($request->getUri()->getPath());
    }
}
