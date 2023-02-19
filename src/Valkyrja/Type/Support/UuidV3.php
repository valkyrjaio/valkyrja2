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

use RuntimeException;
use Valkyrja\Type\Enums\UuidVersion;
use Valkyrja\Type\Exceptions\InvalidUuidV3Exception;

use function hexdec;
use function md5;
use function sprintf;
use function substr;

/**
 * Class Uuid.
 *
 * @author Melech Mizrachi
 */
class UuidV3 extends Uuid
{
    public const REGEX = self::REGEX_PART . '{8}-'
    . self::REGEX_PART . '{4}-'
    . '[3]'
    . self::REGEX_PART . '{3}-'
    . self::REGEX_PART . '{4}-'
    . self::REGEX_PART . '{12}';

    public const VERSION = UuidVersion::V3;

    /**
     * Generate a v3 UUID.
     *
     * @throws RuntimeException
     */
    public static function generate(string $namespace, string $name): string
    {
        // Calculate hash value
        $hash = md5(static::convertToBits($namespace) . $name);

        return sprintf(
            '%08s-%04s-%04x-%04x-%12s',

            // 32 bits for "time_low"
            substr($hash, 0, 8),

            // 16 bits for "time_mid"
            substr($hash, 8, 4),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 3
            (hexdec(substr($hash, 12, 4)) & 0x0FFF) | 0x3000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            (hexdec(substr($hash, 16, 4)) & 0x3FFF) | 0x8000,

            // 48 bits for "node"
            substr($hash, 20, 12)
        );
    }

    /**
     * @inheritDoc
     */
    protected static function throwInvalidException(string $uid): never
    {
        throw new InvalidUuidV3Exception("Invalid UUID V3 $uid provided.");
    }
}
