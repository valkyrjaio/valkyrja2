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
use Valkyrja\Type\Enums\UuidVersion;
use Valkyrja\Type\Exceptions\InvalidUuidV1Exception;

use function chr;
use function hexdec;
use function md5;
use function ord;
use function preg_match;
use function sprintf;
use function strlen;
use function substr;

/**
 * Class UuidV1.
 *
 * @author Melech Mizrachi
 */
class UuidV1 extends Uuid
{
    public const REGEX = self::REGEX_PART . '{8}-'
    . self::REGEX_PART . '{4}-'
    . '[1]'
    . self::REGEX_PART . '{3}-'
    . self::REGEX_PART . '{4}-'
    . self::REGEX_PART . '{12}';

    public const VERSION = UuidVersion::V1;

    /**
     * Generate v1 UUID.
     *
     * Version 1 UUIDs are time-based based. It can take an optional
     * node identifier based on mac address or a unique string id.
     *
     * @throws Exception
     */
    public static function generate(string $node = null): string
    {
        $node ??= random_bytes(16);
        // nano second time (only micro second precision) since start of UTC
        $time = microtime(true) * 10000000 + 0x01B21DD213814000;
        $time = pack('H*', sprintf('%016x', $time));

        $sequence    = random_bytes(2);
        $sequence[0] = chr(ord($sequence[0]) & 0x3F | 0x80);   // variant bits 10x
        $time[0]     = chr(ord($time[0]) & 0x0F | 0x10);       // version bits 0001

        if (! empty($node)) {
            // non hex string identifier
            if (preg_match('/[^a-f0-9]/is', $node)) {
                // base node off md5 hash for sequence
                $node = md5($node);
                // set multicast bit not IEEE 802 MAC
                $node = (hexdec(substr($node, 0, 2)) | 1) . substr($node, 2, 10);
            }

            if (is_numeric($node)) {
                $node = sprintf('%012x', $node);
            }

            $len = strlen($node);

            if ($len > 12) {
                $node = substr($node, 0, 12);
            } elseif ($len < 12) {
                $node .= str_repeat('0', 12 - $len);
            }
        } else {
            // base node off random sequence
            $node = random_bytes(6);
            // set multicast bit not IEEE 802 MAC
            $node[0] = chr(ord($node[0]) | 1);
            $node    = bin2hex($node);
        }

        // $hexTime = bin2hex($time);

        return bin2hex($time[4] . $time[5] . $time[6] . $time[7])    // time low
            . '-' . bin2hex($time[2] . $time[3])                     // time med
            . '-' . bin2hex($time[0] . $time[1])                     // time hi
            . '-' . bin2hex($sequence)                                      // seq
            . '-' . $node;                                                  // node
    }

    /**
     * @inheritDoc
     */
    protected static function throwInvalidException(string $uid): never
    {
        throw new InvalidUuidV1Exception("Invalid UUID V1 $uid provided.");
    }
}
