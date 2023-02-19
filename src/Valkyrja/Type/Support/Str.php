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

namespace Valkyrja\Type\Support;

use Exception;

use function base64_encode;
use function bin2hex;
use function filter_var;
use function random_bytes;
use function str_replace;
use function strlen;
use function substr;
use function trim;
use function Valkyrja\Type\ctype_alpha;
use function Valkyrja\Type\ctype_lower;
use function Valkyrja\Type\ctype_upper;
use function Valkyrja\Type\str_contains;
use function Valkyrja\Type\str_ends_with;
use function Valkyrja\Type\str_starts_with;

use const FILTER_VALIDATE_EMAIL;

/**
 * Class Str.
 *
 * @author Melech Mizrachi
 */
class Str
{
    /**
     * Check if a string starts with a needle.
     *
     * @param string $subject The subject
     * @param string $needle  The needle
     */
    public static function startsWith(string $subject, string $needle): bool
    {
        return str_starts_with($subject, $needle);
    }

    /**
     * Check if a string starts with any needle.
     *
     * @param string $subject    The subject
     * @param string ...$needles The needles
     */
    public static function startsWithAny(string $subject, string ...$needles): bool
    {
        return static::withAny('startsWith', $subject, ...$needles);
    }

    /**
     * Check if a string ends with a needle.
     *
     * @param string $subject The subject
     * @param string $needle  The needle
     */
    public static function endsWith(string $subject, string $needle): bool
    {
        return str_ends_with($subject, $needle);
    }

    /**
     * Check if a string ends with any needle.
     *
     * @param string $subject    The subject
     * @param string ...$needles The needles
     */
    public static function endsWithAny(string $subject, string ...$needles): bool
    {
        return static::withAny('endsWith', $subject, ...$needles);
    }

    /**
     * Check if a string contains a needle.
     *
     * @param string $subject The subject
     * @param string $needle  The needle
     */
    public static function contains(string $subject, string $needle): bool
    {
        return str_contains($subject, $needle);
    }

    /**
     * Check if a string contains any needle.
     *
     * @param string $subject    The subject
     * @param string ...$needles The needles
     */
    public static function containsAny(string $subject, string ...$needles): bool
    {
        return static::withAny('contains', $subject, ...$needles);
    }

    /**
     * Check if a string's length is longer than a minimum length.
     *
     * @param string $subject The subject
     * @param int    $min     [optional] The minimum length
     */
    public static function min(string $subject, int $min = 0): bool
    {
        return strlen($subject) >= $min;
    }

    /**
     * Check if a string's length is not longer than a maximum length.
     *
     * @param string $subject The subject
     * @param int    $max     [optional] The max length
     */
    public static function max(string $subject, int $max = 256): bool
    {
        return strlen($subject) <= $max;
    }

    /**
     * Replace a portion of a string with a replacement.
     *
     * @param string $subject     The subject
     * @param string $replace     The needle to replace
     * @param string $replacement The replacement
     */
    public static function replace(string $subject, string $replace, string $replacement): string
    {
        return str_replace($replace, $replacement, $subject);
    }

    /**
     * Replace any portion of a string with any replacement.
     *
     * @param string   $subject     The subject
     * @param string[] $replace     The needles to replace
     * @param string[] $replacement The replacements
     */
    public static function replaceAll(string $subject, array $replace, array $replacement): string
    {
        static::validateStrings(...$replace);
        static::validateStrings(...$replacement);

        return str_replace($replace, $replacement, $subject);
    }

    /**
     * Replace all portions of a string with a replacement.
     *
     * @param string   $subject     The subject
     * @param string[] $replace     The needles to replace
     * @param string   $replacement The replacement
     */
    public static function replaceAllWith(string $subject, array $replace, string $replacement): string
    {
        static::validateStrings(...$replace);

        return str_replace($replace, $replacement, $subject);
    }

    /**
     * Get a substring from start position with a certain length.
     *
     * @param string   $subject The subject
     * @param int      $start   The start
     * @param int|null $length  [optional] The length
     */
    public static function substr(string $subject, int $start, int $length = null): string
    {
        return substr($subject, $start, $length);
    }

    /**
     * Get a random string.
     *
     * @param int<1, max> $length [optional] The length
     *
     * @throws Exception
     */
    public static function random(int $length = 20): string
    {
        return trim(bin2hex(random_bytes($length)), " \t\n\r\0\x0B/");
    }

    /**
     * Get a md5 random string.
     *
     * @param int<1, max> $length [optional] The length
     *
     * @throws Exception
     */
    public static function randomMd5(int $length = 20): string
    {
        return md5(static::random($length));
    }

    /**
     * Get a base64 random string.
     *
     * @param int<1, max> $length [optional] The length
     *
     * @throws Exception
     */
    public static function randomBase64(int $length = 20): string
    {
        return base64_encode(static::random($length));
    }

    /**
     * Validate multiple params to all be strings by using PHPs built in type hinting.
     *
     * @param mixed ...$subjects The subjects
     */
    public static function validateStrings(string ...$subjects): void
    {
        // Left empty on purpose. Validation happens due to PHP type hinting.
    }

    /**
     * Check if a string is a valid email.
     *
     * @param string $subject The subject
     */
    public static function isEmail(string $subject): bool
    {
        return (bool) filter_var($subject, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Check if a string is alphabetic.
     *
     * @param string $subject The subject
     */
    public static function isAlphabetic(string $subject): bool
    {
        return ctype_alpha($subject);
    }

    /**
     * Check if a string is alphabetic and lowercase.
     *
     * @param string $subject The subject
     */
    public static function isLowercase(string $subject): bool
    {
        return ctype_lower($subject);
    }

    /**
     * Check if a string is alphabetic and uppercase.
     *
     * @param string $subject The subject
     */
    public static function isUppercase(string $subject): bool
    {
        return ctype_upper($subject);
    }

    /**
     * Check if a string matches a method constraint with any needle.
     *
     * @param string $method     The method to check all needles against
     * @param string $subject    The subject
     * @param string ...$needles The needles
     */
    protected static function withAny(string $method, string $subject, string ...$needles): bool
    {
        foreach ($needles as $needle) {
            if (static::$$method($subject, $needle)) {
                return true;
            }
        }

        return false;
    }
}
