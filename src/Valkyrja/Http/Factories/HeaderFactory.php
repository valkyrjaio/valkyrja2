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

use function array_change_key_case;
use function array_key_exists;
use function implode;
use function is_array;
use function str_replace;
use function strtolower;
use function substr;

/**
 * Abstract Class HeadersFactory.
 *
 * @author Melech Mizrachi
 */
abstract class HeaderFactory
{
    /**
     * Marshal headers from $_SERVER.
     */
    public static function marshalHeaders(array $server): array
    {
        $headers = [];

        foreach ($server as $key => $value) {
            // Apache prefixes environment variables with REDIRECT_
            // if they are added by rewrite rules
            if (str_starts_with($key, 'REDIRECT_')) {
                $key = substr($key, 9);

                // We will not overwrite existing variables with the
                // prefixed versions, though
                if (array_key_exists($key, $server)) {
                    continue;
                }
            }

            if ($value && str_starts_with($key, 'HTTP_')) {
                $name           = str_replace('_', '-', strtolower(substr($key, 5)));
                $headers[$name] = $value;

                continue;
            }

            if ($value && str_starts_with($key, 'CONTENT_')) {
                $name           = 'content-' . strtolower(substr($key, 8));
                $headers[$name] = $value;
            }
        }

        return $headers;
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
}
