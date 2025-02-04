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

namespace Valkyrja\Http\Message\Header\Security;

use Valkyrja\Http\Message\Exception\InvalidArgumentException;
use Valkyrja\Http\Message\Header\Exception\InvalidNameException;
use Valkyrja\Http\Message\Header\Exception\InvalidValueException;

use function in_array;
use function ord;
use function preg_match;
use function sprintf;
use function strlen;

/**
 * Class HeaderSecurity.
 *
 * @author Melech Mizrachi
 */
final class HeaderSecurity
{
    /**
     * Private constructor; non-instantiable.
     *
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * Filter a header value.
     * Ensures CRLF header injection vectors are filtered.
     * Per RFC 7230, only VISIBLE ASCII characters, spaces, and horizontal
     * tabs are allowed in values; header continuations MUST consist of
     * a single CRLF sequence followed by a space or horizontal tab.
     * This method filters any values not allowed from the string, and is
     * lossy.
     *
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     *
     * @param string $value
     *
     * @return string
     */
    public static function filter(string $value): string
    {
        $length = strlen($value);
        $string = '';

        for ($i = 0; $i < $length; $i++) {
            $ascii = ord($value[$i]);

            // Detect continuation sequences
            if ($ascii === 13) {
                $lf = ord($value[$i + 1]);
                $ws = ord($value[$i + 2]);

                if ($lf === 10 && in_array($ws, [9, 32], true)) {
                    $string .= $value[$i] . $value[$i + 1];
                    $i++;
                }

                continue;
            }

            // Non-visible, non-whitespace characters
            // 9 === horizontal tab
            // 32-126, 128-254 === visible
            // 127 === DEL
            // 255 === null byte
            if (($ascii < 32 && $ascii !== 9)
                || $ascii === 127
                || $ascii > 254
            ) {
                continue;
            }

            $string .= $value[$i];
        }

        return $string;
    }

    /**
     * Assert a header value is valid.
     *
     * @param string $value
     *
     * @throws InvalidArgumentException for invalid values
     *
     * @return void
     */
    public static function assertValid(string $value): void
    {
        if (! self::isValid($value)) {
            throw new InvalidValueException(sprintf('"%s" is not valid header value', $value));
        }
    }

    /**
     * Validate a header value.
     * Per RFC 7230, only VISIBLE ASCII characters, spaces, and horizontal
     * tabs are allowed in values; header continuations MUST consist of
     * a single CRLF sequence followed by a space or horizontal tab.
     *
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     *
     * @param string $value
     *
     * @return bool
     */
    public static function isValid(string $value): bool
    {
        // Look for:
        // \n not preceded by \r, OR
        // \r not followed by \n, OR
        // \r\n not followed by space or horizontal tab; these are all CRLF attacks
        if (preg_match("#(?:(?:(?<!\r)\n)|(?:\r(?!\n))|(?:\r\n(?![ \t])))#", $value)) {
            return false;
        }

        // Non-visible, non-whitespace characters
        // 9 === horizontal tab
        // 10 === line feed
        // 13 === carriage return
        // 32-126, 128-254 === visible
        // 127 === DEL (disallowed)
        // 255 === null byte (disallowed)
        if (preg_match('/[^\x09\x0a\x0d\x20-\x7E\x80-\xFE]/', $value)) {
            return false;
        }

        return true;
    }

    /**
     * Assert whether or not a header name is valid.
     *
     * @see http://tools.ietf.org/html/rfc7230#section-3.2
     *
     * @param string $name
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public static function assertValidName(string $name): void
    {
        if (! self::isValidName($name)) {
            throw new InvalidNameException(sprintf('"%s" is not valid header name', $name));
        }
    }

    public static function isValidName(string $name): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9\'`#$%&*+.^_|~!-]+$/D', $name);
    }
}
