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

use function preg_replace;
use function strtolower;
use function strtoupper;
use function ucwords;
use function Valkyrja\Type\ctype_lower;

/**
 * Class StrCase.
 *
 * @author Melech Mizrachi
 */
class StrCase
{
    /**
     * Studly case conversion cache.
     *
     * @var array<string, string>
     */
    protected static array $studlyCache = [];

    /**
     * Snake case conversion cache.
     *
     * @var array<string, string>
     */
    protected static array $snakeCache = [];

    /**
     * Convert a string to title case.
     *
     * @param string $subject The subject
     */
    public static function toTitleCase(string $subject): string
    {
        return ucwords($subject);
    }

    /**
     * Convert all strings to title case.
     *
     * @param string ...$subjects The subjects
     */
    public static function allToTitleCase(string ...$subjects): array
    {
        return static::allTo('toTitleCase', ...$subjects);
    }

    /**
     * Convert a string to lower case.
     *
     * @param string $subject The subject
     */
    public static function toLowerCase(string $subject): string
    {
        return strtolower($subject);
    }

    /**
     * Convert all strings to lower case.
     *
     * @param string ...$subjects The subjects
     */
    public static function allToLowerCase(string ...$subjects): array
    {
        return static::allTo('toLowerCase', ...$subjects);
    }

    /**
     * Convert a string to upper case.
     *
     * @param string $subject The subject
     */
    public static function toUpperCase(string $subject): string
    {
        return strtoupper($subject);
    }

    /**
     * Convert all strings to upper case.
     *
     * @param string ...$subjects The subjects
     */
    public static function allToUpperCase(string ...$subjects): array
    {
        return static::allTo('toUpperCase', ...$subjects);
    }

    /**
     * Convert a string to capitalized.
     *
     * @param string      $subject   The subject
     * @param string|null $delimiter [optional] The delimiter
     */
    public static function toCapitalized(string $subject, string $delimiter = null): string
    {
        if ($delimiter) {
            return ucwords($subject, $delimiter);
        }

        return ucwords($subject);
    }

    /**
     * Convert all strings to capitalized.
     *
     * @param string ...$subjects The subjects
     */
    public static function allToCapitalized(string ...$subjects): array
    {
        return static::allTo('toCapitalized', ...$subjects);
    }

    /**
     * Convert a string to capitalized.
     *
     * @param string      $subject   The subject
     * @param string|null $delimiter [optional] The delimiter
     */
    public static function toCapitalizedWords(string $subject, string $delimiter = null): string
    {
        return static::toCapitalized(Str::replaceAllWith($subject, ['-', '_'], ' '), $delimiter);
    }

    /**
     * Convert all strings to capitalized.
     *
     * @param string ...$subjects The subjects
     */
    public static function allToCapitalizedWords(string ...$subjects): array
    {
        return static::allTo('toCapitalizedWords', ...$subjects);
    }

    /**
     * Convert a string to snake case.
     *
     * @param string $subject The subject
     */
    public static function toSnakeCase(string $subject): string
    {
        $key = $subject;

        if (isset(static::$snakeCache[$key])) {
            return static::$snakeCache[$key];
        }

        if (! ctype_lower($subject)) {
            $subject = preg_replace('/\s+/u', '', ucwords($subject));

            $subject = static::toLowerCase(preg_replace('/(.)(?=[A-Z])/u', '$1_', $subject));
        }

        return static::$snakeCache[$key] = $subject;
    }

    /**
     * Convert all string to snake case.
     *
     * @param string ...$subjects The subjects
     */
    public static function allToSnakeCase(string ...$subjects): array
    {
        return static::allTo('toSnakeCase', ...$subjects);
    }

    /**
     * Convert a string to slug.
     *
     * @param string $subject The subject
     */
    public static function toSlug(string $subject): string
    {
        $key = $subject;

        if (isset(static::$snakeCache[$key])) {
            return static::$snakeCache[$key];
        }

        if (! ctype_lower($subject)) {
            $subject = preg_replace('/\s+/u', '', ucwords($subject));

            $subject = static::toLowerCase(preg_replace('/(.)(?=[A-Z])/u', '$1-', $subject));
        }

        return static::$snakeCache[$key] = $subject;
    }

    /**
     * Convert all string to slug.
     *
     * @param string ...$subjects The subjects
     */
    public static function allToSlug(string ...$subjects): array
    {
        return static::allTo('toSlug', ...$subjects);
    }

    /**
     * Convert a string to studly case.
     *
     * @param string $subject The subject
     */
    public static function toStudlyCase(string $subject): string
    {
        return self::$studlyCache[$subject]
            ?? (self::$studlyCache[$subject] = Str::replace(static::toCapitalizedWords($subject), ' ', ''));
    }

    /**
     * Convert all string to studly case.
     *
     * @param string ...$subjects The subjects
     */
    public static function allToStudlyCase(string ...$subjects): array
    {
        return static::allTo('toStudlyCase', ...$subjects);
    }

    /**
     * Convert a string's first character to upper case.
     *
     * @param string $subject The subject
     */
    public static function ucFirstLetter(string $subject): string
    {
        return static::toUpperCase(Str::substr($subject, 0, 1)) . Str::substr($subject, 1);
    }

    /**
     * Convert all strings' first characters to upper case.
     *
     * @param string ...$subjects The subjects
     */
    public static function allUcFirstLetter(string ...$subjects): array
    {
        return static::allTo('ucFirstLetter', ...$subjects);
    }

    /**
     * Convert all strings to a method output.
     *
     * @param string $method      The method to call for each subject
     * @param string ...$subjects The subjects
     */
    protected static function allTo(string $method, string ...$subjects): array
    {
        foreach ($subjects as $key => $string) {
            $subjects[$key] = static::$$method($string);
        }

        return $subjects;
    }
}
