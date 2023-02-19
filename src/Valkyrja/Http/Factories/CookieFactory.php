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

use function preg_match_all;
use function urldecode;

use const PREG_SET_ORDER;

/**
 * Abstract Class CookieFactory.
 *
 * @author Melech Mizrachi
 */
abstract class CookieFactory
{
    /**
     * Parse a cookie header according to RFC 6265.
     * PHP will replace special characters in cookie names, which results in other cookies not being available due to
     * overwriting. Thus, the server request should take the cookies from the request header instead.
     */
    public static function parseCookieHeader(string $cookieHeader): array
    {
        preg_match_all(
            '(
            (?:^\\n?[ \t]*|;[ ])
            (?P<name>[!#$%&\'*+\-.0-9A-Z^_`a-z|~]+)
            =
            (?P<DQUOTE>"?)
                (?P<value>[\x21\x23-\x2b\x2d-\x3a\x3c-\x5b\x5d-\x7e]*)
            (?P=DQUOTE)
            (?=\\n?[ \t]*$|;[ ])
        )x',
            $cookieHeader,
            $matches,
            PREG_SET_ORDER
        );

        $cookies = [];

        /** @var array $matches */
        foreach ($matches as $match) {
            $cookies[$match['name']] = urldecode($match['value']);
        }

        return $cookies;
    }
}
