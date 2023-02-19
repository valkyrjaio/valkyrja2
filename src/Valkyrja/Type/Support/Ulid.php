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

use DateTimeInterface;
use Exception;
use InvalidArgumentException;
use Valkyrja\Type\Exceptions\InvalidUlidException;

use function microtime;
use function strtr;
use function substr;

/**
 * Class Ulid.
 *
 * @see    https://github.com/ulid/spec
 *
 * @author Melech Mizrachi
 */
class Ulid extends Uid
{
    public const VALID_CHARACTERS = '0123456789ABCDEFGHJKMNPQRSTVWXYZabcdefghjkmnpqrstvwxyz';

    public const REGEX = '[0-7][' . self::VALID_CHARACTERS . ']{25}';

    public const MAX_PART = 0xFFFFF;

    protected const MAX_RANDOM_BYTES = 4;

    protected const FORMAT = '%010s%04s%04s%04s%04s';

    /**
     * The previously used time string.
     */
    protected static string $time = '';

    /**
     * The previously used random array item.
     */
    protected static array $randomBytes = [];

    /**
     * Generate a ULID.
     *
     * @param DateTimeInterface|null $dateTime  [optional] The date time to use when generating the ULID
     * @param bool                   $lowerCase [optional] Whether to return as lower case
     *
     * @throws Exception
     */
    public static function generate(DateTimeInterface $dateTime = null, bool $lowerCase = false): string
    {
        $time = static::getTime($dateTime);

        // If the time is greater than the previously used time, or the time is not what was previously used and a date
        // time was passed in
        if (static::doesTimeMatch($time, $dateTime)) {
            static::randomize($time);
        } // Otherwise if the entire array's worth of random bytes is at max (we've generated A LOT of ids)
        elseif (static::areAllRandomBytesMax()) {
            $time = static::increaseTime($time);

            static::randomize($time);
        }
        // Otherwise if the time matches and a date time wasn't passed in, or one was passed but the time ended up
        // matching anyway
        else {
            static::updateRandomBytes();

            // Set the time from the previous time
            $time = static::$time;
        }

        $time = base_convert($time, 10, 32);

        $ulid = strtr(
            static::formatTimeWithRandomBytes($time),
            'abcdefghijklmnopqrstuv',
            'ABCDEFGHJKMNPQRSTVWXYZ'
        );

        if ($lowerCase) {
            return strtolower($ulid);
        }

        return $ulid;
    }

    /**
     * Generate a ULID and return as lower case.
     *
     * @param DateTimeInterface|null $dateTime [optional] The date time to use when generating the ULID
     *
     * @throws Exception
     */
    public static function generateLowerCase(DateTimeInterface $dateTime = null): string
    {
        return static::generate($dateTime, lowerCase: true);
    }

    /**
     * Get a time to generate a ULID with.
     *
     * @param DateTimeInterface|null $dateTime [optional] The date time to use when generating the ULID
     */
    protected static function getTime(DateTimeInterface $dateTime = null): string
    {
        // If no date was passed in
        if ($dateTime === null) {
            return static::getTimeFromMicroTime(microtime());
        }

        if (($time = static::getTimeFromDateTime($dateTime)) < 0) {
            throw new InvalidArgumentException('The timestamp must be positive.');
        }

        return $time;
    }

    /**
     * Get the time from micro time.
     *
     * @param string $time The micro time
     */
    protected static function getTimeFromMicroTime(string $time): string
    {
        return substr($time, 11) . substr($time, 2, 3);
    }

    /**
     * Get the time from a datetime.
     *
     * @param DateTimeInterface $dateTime The date time to use when generating the ULID
     */
    protected static function getTimeFromDateTime(DateTimeInterface $dateTime): string
    {
        return $dateTime->format('Uv');
    }

    /**
     * Determine if the time matches the previously set time.
     *
     * @param string                 $time     The time to check
     * @param DateTimeInterface|null $dateTime [optional] The date time to use when generating the ULID
     */
    protected static function doesTimeMatch(string $time, DateTimeInterface $dateTime = null): bool
    {
        return $time > static::$time || ($dateTime !== null && $time !== static::$time);
    }

    /**
     * Determine if the entire array's worth of random bytes are at max.
     * - Can occur if a lot of ids were generated for the same time
     * - Or if somehow all the random bytes were at max, somehow...
     */
    protected static function areAllRandomBytesMax(): bool
    {
        return static::$randomBytes === [1 => self::MAX_PART, self::MAX_PART, self::MAX_PART, self::MAX_PART];
    }

    /**
     * Increase the time.
     *
     * @param string $time The time
     */
    protected static function increaseTime(string $time): string
    {
        return (string) (1 + (int) $time);
    }

    /**
     * Update the random bytes.
     */
    protected static function updateRandomBytes(): void
    {
        // Go from last to first and iterate over the random bytes to test if they're at max
        for ($i = static::MAX_RANDOM_BYTES; $i > 0 && static::$randomBytes[$i] === static::MAX_PART; $i--) {
            // Set to 0 if the byte is
            static::$randomBytes[$i] = 0;
        }

        // We've found the last byte part that isn't max so increment it
        static::$randomBytes[$i]++;
    }

    /**
     * Generate a randomized array.
     *
     * @param string $time The time to use
     *
     * @throws Exception
     */
    protected static function randomize(string $time): void
    {
        $randomBytes = static::generateRandomBytes();

        static::processRandomizedByteParts($randomBytes);

        // Set the random bytes for later reference
        self::$randomBytes = $randomBytes;
        // Set the time for later reference
        self::$time = $time;
    }

    /**
     * Generate a randomized bytes array.
     *
     * @throws Exception
     */
    protected static function generateRandomBytes(): array
    {
        return unpack('n*', random_bytes(10));
    }

    /**
     * Process the randomized byte parts.
     *
     * @param array $randomBytes The random byte parts
     */
    protected static function processRandomizedByteParts(array &$randomBytes): void
    {
        static::processRandomizedBytePart($randomBytes, 1);
        static::processRandomizedBytePart($randomBytes, 2);
        static::processRandomizedBytePart($randomBytes, 3);
        static::processRandomizedBytePart($randomBytes, 4);

        static::unsetRandomByteParts($randomBytes);
    }

    /**
     * Process the randomized byte part.
     *
     * @param array $randomBytes The random byte parts
     * @param int   $index       The index to process
     */
    protected static function processRandomizedBytePart(array &$randomBytes, int $index): void
    {
        $randomBytes[$index] |= ($randomBytes[5] <<= 4) & static::MAX_PART;
    }

    /**
     * Unset random byte parts.
     *
     * @param array $randomBytes The random byte parts
     */
    protected static function unsetRandomByteParts(array &$randomBytes): void
    {
        unset($randomBytes[5]);
    }

    /**
     * Format a time with random bytes.
     *
     * @param string $time The time
     */
    protected static function formatTimeWithRandomBytes(string $time): string
    {
        return sprintf(static::FORMAT, $time, ...static::getConvertedRandomBytesForFormat());
    }

    /**
     * @return string[]
     */
    protected static function getConvertedRandomBytesForFormat(): array
    {
        $convertedRandomBytes = [];

        for ($i = 1; $i <= static::MAX_RANDOM_BYTES; $i++) {
            $convertedRandomBytes[] = static::convertRandomBytesPart($i);
        }

        return $convertedRandomBytes;
    }

    /**
     * Convert a random byte part.
     *
     * @param int $index The index to process
     */
    protected static function convertRandomBytesPart(int $index): string
    {
        if ($index > static::MAX_RANDOM_BYTES) {
            return '';
        }

        return base_convert((string) static::$randomBytes[$index], 10, 32);
    }

    /**
     * @inheritDoc
     */
    protected static function throwInvalidException(string $uid): never
    {
        throw new InvalidUlidException("Invalid ULID $uid provided.");
    }
}
