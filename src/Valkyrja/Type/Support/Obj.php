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

use JsonException;

use function count;
use function explode;
use function get_object_vars;
use function json_decode;
use function json_encode;
use function Valkyrja\Type\str_contains;

use const JSON_THROW_ON_ERROR;

/**
 * Class Obj.
 *
 * @author Melech Mizrachi
 */
class Obj
{
    /**
     * Convert an object to a string.
     *
     * @param object $subject The subject object
     *
     * @throws JsonException
     */
    public static function toString(object $subject): string
    {
        return json_encode($subject, JSON_THROW_ON_ERROR);
    }

    /**
     * Un-convert an object from a string.
     *
     * @param string $subject The subject object as a string
     *
     * @throws JsonException
     */
    public static function fromString(string $subject): object
    {
        return json_decode($subject, false, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Get the object's publicly accessible properties.
     *
     * @param object $subject The subject object
     *
     * @return array<string, mixed>
     */
    public static function getProperties(object $subject): array
    {
        return get_object_vars($subject);
    }

    /**
     * Get all object's properties regardless of visibility.
     *
     * @param object $subject          The subject object
     * @param bool   $includeProtected [optional] Whether to include protected members
     * @param bool   $includePrivate   [optional] Whether to include private members
     */
    public static function getAllProperties(
        object $subject,
        bool $includeProtected = true,
        bool $includePrivate = true
    ): array {
        /** @var array<string, mixed> $castSubject */
        // The subject cast as an array
        $castSubject = (array) $subject;
        // The array to return
        $array = [];

        // Iterate through each subject
        foreach ($castSubject as $key => $value) {
            // Explode the key on the \0 character
            /*
             * Public members: member_name
             * Protected members: \0*\0member_name
             * Private members: \0Class_name\0member_name
             */
            $isProtected = str_contains($key, "\0*");
            $keyParts    = explode("\0", $key);

            if (count($keyParts) > 1) {
                if (! $includeProtected && $isProtected) {
                    continue;
                }

                if (! $includePrivate && ! $isProtected) {
                    continue;
                }
            }

            // Set the property and value
            $array[end($keyParts)] = $value;
        }

        return $array;
    }

    /**
     * Convert an object to a deep array.
     *
     * @param object $subject The subject object
     *
     * @throws JsonException
     */
    public static function toDeepArray(object $subject): array
    {
        return Arr::fromString(static::toString($subject));
    }
}
