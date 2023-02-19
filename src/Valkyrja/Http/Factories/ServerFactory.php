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

use function is_callable;

/**
 * Abstract Class ServerFactory.
 *
 * @author Melech Mizrachi
 */
abstract class ServerFactory
{
    /**
     * Function to use to get apache request headers; present only to simplify mocking.
     *
     * @var callable
     */
    private static $apacheRequestHeaders = 'apache_request_headers';

    /**
     * Marshal the $_SERVER array.
     * Pre-processes and returns the $_SERVER superglobal.
     */
    public static function normalizeServer(array $server): array
    {
        // This seems to be the only way to get the Authorization header on Apache
        $apacheRequestHeaders = self::$apacheRequestHeaders;

        if (isset($server['HTTP_AUTHORIZATION']) || ! is_callable($apacheRequestHeaders)) {
            return $server;
        }

        $apacheRequestHeaders = $apacheRequestHeaders();

        if (isset($apacheRequestHeaders['Authorization'])) {
            $server['HTTP_AUTHORIZATION'] = $apacheRequestHeaders['Authorization'];

            return $server;
        }

        if (isset($apacheRequestHeaders['authorization'])) {
            $server['HTTP_AUTHORIZATION'] = $apacheRequestHeaders['authorization'];

            return $server;
        }

        return $server;
    }
}
