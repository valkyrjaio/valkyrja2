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

use ArrayAccess;
use JsonException;
use Valkyrja\Config\Constants\ConfigKeyPart;

use function explode;
use function is_array;
use function json_decode;
use function json_encode;

use const JSON_THROW_ON_ERROR;

/**
 * Class Arr.
 *
 * @author Melech Mizrachi
 */
class Arr
{
    /**
     * Get a subject value by dot notation key.
     *
     * @param ArrayAccess|iterable $subject      The subject to search
     * @param string               $key          The dot notation to search for
     * @param mixed|null           $defaultValue The default value
     */
    public static function getValueDotNotation(ArrayAccess|iterable $subject, string $key, mixed $defaultValue = null): mixed
    {
        $value = $subject;

        // Explode the keys on period and iterate through the keys
        foreach (explode(ConfigKeyPart::SEP, $key) as $item) {
            // Trying to get the item from the current value or set the default
            $value = $value[$item] ?? null;

            // If the value is ull then the dot notation doesn't exist in this array so return the default
            if ($value === null) {
                return $defaultValue;
            }
        }

        return $value;
    }

    /**
     * Convert an array to a string.
     *
     * @param array $subject The subject array
     *
     * @throws JsonException
     */
    public static function toString(array $subject): string
    {
        return json_encode($subject, JSON_THROW_ON_ERROR);
    }

    /**
     * Un-convert an array from a string.
     *
     * @param string $subject The subject array as a string
     *
     * @throws JsonException
     */
    public static function fromString(string $subject): array
    {
        return json_decode($subject, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Strip out null valued properties in an array.
     *
     * @param array $subject The subject array
     */
    public static function newWithoutNull(array $subject): array
    {
        $new = $subject;

        return static::withoutNull($new);
    }

    /**
     * Strip out null valued properties in an array.
     *
     * @param array $subject The subject array
     */
    public static function withoutNull(array &$subject): array
    {
        foreach ($subject as $key => &$value) {
            if (is_array($value)) {
                static::withoutNull($value);
            }

            if ($value === null) {
                unset($subject[$key]);
            }
        }

        return $subject;
    }
}
