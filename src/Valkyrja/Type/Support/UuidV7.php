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
use Valkyrja\Type\Exceptions\InvalidUuidV7Exception;

/**
 * Class UuidV7.
 *
 * @author Melech Mizrachi
 */
abstract class UuidV7 extends Uuid
{
    public const REGEX = self::REGEX_PART . '{8}-'
    . self::REGEX_PART . '{4}-'
    . '[7]'
    . self::REGEX_PART . '{3}-'
    . self::REGEX_PART . '{4}-'
    . self::REGEX_PART . '{12}';

    public const VERSION = UuidVersion::V7;

    /**
     * Generate a v7 UUID.
     *
     * @throws Exception
     */
    abstract public static function generate(): string;

    /**
     * @inheritDoc
     */
    protected static function throwInvalidException(string $uid): never
    {
        throw new InvalidUuidV7Exception("Invalid UUID V7 $uid provided.");
    }
}
