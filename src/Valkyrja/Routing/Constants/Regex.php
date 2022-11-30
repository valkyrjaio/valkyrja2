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

namespace Valkyrja\Routing\Constants;

use Valkyrja\Support\Type\Ulid;
use Valkyrja\Support\Type\Uuid;
use Valkyrja\Support\Type\UuidV1;
use Valkyrja\Support\Type\UuidV3;
use Valkyrja\Support\Type\UuidV4;
use Valkyrja\Support\Type\UuidV5;
use Valkyrja\Support\Type\UuidV6;
use Valkyrja\Support\Type\UuidV7;
use Valkyrja\Support\Type\UuidV8;
use Valkyrja\Support\Type\Vlid;
use Valkyrja\Support\Type\VlidV1;
use Valkyrja\Support\Type\VlidV2;
use Valkyrja\Support\Type\VlidV3;
use Valkyrja\Support\Type\VlidV4;

/**
 * Constant Regex.
 *
 * @author Melech Mizrachi
 */
final class Regex
{
    public const PATH = '\/';
    public const ANY  = '.*';
    public const NUM  = '\d+';

    public const ID = self::NUM;

    public const SLUG = '[a-zA-Z0-9-]+';

    public const UUID    = Uuid::REGEX;
    public const UUID_V1 = UuidV1::REGEX;
    public const UUID_V3 = UuidV3::REGEX;
    public const UUID_V4 = UuidV4::REGEX;
    public const UUID_V5 = UuidV5::REGEX;
    public const UUID_V6 = UuidV6::REGEX;
    public const UUID_V7 = UuidV7::REGEX;
    public const UUID_V8 = UuidV8::REGEX;

    public const ULID = Ulid::REGEX;

    public const VLID    = Vlid::REGEX;
    public const VLID_V1 = VlidV1::REGEX;
    public const VLID_V2 = VlidV2::REGEX;
    public const VLID_V3 = VlidV3::REGEX;
    public const VLID_V4 = VlidV4::REGEX;

    public const ALPHA                = '[a-zA-Z]+';
    public const ALPHA_LOWERCASE      = '[a-z]+';
    public const ALPHA_UPPERCASE      = '[A-Z]+';
    public const ALPHA_NUM            = '[a-zA-Z0-9]+';
    public const ALPHA_NUM_UNDERSCORE = '\w+';

    public const START = '/^';
    public const END   = '$/';

    public const START_CAPTURE_GROUP          = '(';
    public const START_NON_CAPTURE_GROUP      = '(?:';
    public const START_OPTIONAL_CAPTURE_GROUP = self::START_NON_CAPTURE_GROUP . self::PATH . self::END_OPTIONAL_CAPTURE_GROUP;
    public const END_CAPTURE_GROUP            = ')';
    public const END_OPTIONAL_CAPTURE_GROUP   = ')?';
}
