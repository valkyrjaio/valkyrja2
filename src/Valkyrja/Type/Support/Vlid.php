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
use Valkyrja\Type\Enums\VlidVersion;
use Valkyrja\Type\Exceptions\InvalidVlidException;

/**
 * Class Vlid.
 *
 * Valkyrja Universally Unique Lexicographically Sortable Identifier (VLID)
 * A more precise version of a ULID where time must be down to the microsecond, and can sacrifice on randomness a
 * little while remaining 128 bit.
 *
 * @author Melech Mizrachi
 */
class Vlid extends Ulid
{
    public const REGEX = '[0-7]'
    . '[' . self::VALID_CHARACTERS . ']{12}'
    . '[1-4]'
    . '[' . self::VALID_CHARACTERS . ']{4}'
    . '([' . self::VALID_CHARACTERS . ']{4})?'
    . '([' . self::VALID_CHARACTERS . ']{4})?'
    . '([' . self::VALID_CHARACTERS . ']{4})?';

    /** @var VlidVersion */
    public const VERSION = VlidVersion::V1;

    protected const FORMAT = '%013s%01s%04s%04s%04s';

    protected const MAX_RANDOM_BYTES = 3;

    /**
     * Generate a VLID v1.
     *
     * @param DateTimeInterface|null $dateTime  [optional] The date time to use when generating the ULID
     * @param bool                   $lowerCase [optional] Whether to return as lower case
     *
     * @throws Exception
     */
    final public static function v1(DateTimeInterface $dateTime = null, bool $lowerCase = false): string
    {
        return VlidV1::generate($dateTime, $lowerCase);
    }

    /**
     * Generate a VLID v2.
     *
     * @param DateTimeInterface|null $dateTime  [optional] The date time to use when generating the ULID
     * @param bool                   $lowerCase [optional] Whether to return as lower case
     *
     * @throws Exception
     */
    final public static function v2(DateTimeInterface $dateTime = null, bool $lowerCase = false): string
    {
        return VlidV2::generate($dateTime, $lowerCase);
    }

    /**
     * Generate a VLID v3.
     *
     * @param DateTimeInterface|null $dateTime  [optional] The date time to use when generating the ULID
     * @param bool                   $lowerCase [optional] Whether to return as lower case
     *
     * @throws Exception
     */
    final public static function v3(DateTimeInterface $dateTime = null, bool $lowerCase = false): string
    {
        return VlidV3::generate($dateTime, $lowerCase);
    }

    /**
     * Generate a VLID v4.
     *
     * @param DateTimeInterface|null $dateTime  [optional] The date time to use when generating the ULID
     * @param bool                   $lowerCase [optional] Whether to return as lower case
     *
     * @throws Exception
     */
    final public static function v4(DateTimeInterface $dateTime = null, bool $lowerCase = false): string
    {
        return VlidV4::generate($dateTime, $lowerCase);
    }

    /**
     * @inheritDoc
     */
    protected static function getTimeFromMicroTime(string $time): string
    {
        return substr($time, 11) . substr($time, 2, 8);
    }

    /**
     * @inheritDoc
     */
    protected static function getTimeFromDateTime(DateTimeInterface $dateTime): string
    {
        return $dateTime->format('Uu');
    }

    /**
     * Format a time with random bytes.
     *
     * @param string $time The time
     */
    protected static function formatTimeWithRandomBytes(string $time): string
    {
        return sprintf(static::FORMAT, $time, static::VERSION->value, ...static::getConvertedRandomBytesForFormat());
    }

    /**
     * @inheritDoc
     */
    protected static function areAllRandomBytesMax(): bool
    {
        return static::$randomBytes === [1 => self::MAX_PART, self::MAX_PART, self::MAX_PART];
    }

    /**
     * @inheritDoc
     */
    protected static function unsetRandomByteParts(array &$randomBytes): void
    {
        unset($randomBytes[4], $randomBytes[5]);
    }

    /**
     * @inheritDoc
     */
    protected static function throwInvalidException(string $uid): never
    {
        throw new InvalidVlidException("Invalid VLID $uid provided.");
    }
}
