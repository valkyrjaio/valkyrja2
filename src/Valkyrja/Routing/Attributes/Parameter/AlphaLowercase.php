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

namespace Valkyrja\Routing\Attributes\Parameter;

use Attribute;
use Valkyrja\Model\Data\Cast;
use Valkyrja\Routing\Attributes\Parameter;
use Valkyrja\Routing\Constants\ParameterName;
use Valkyrja\Routing\Constants\Regex;

/**
 * Attribute AlphaLowercase.
 *
 * @author Melech Mizrachi
 */
#[Attribute(Attribute::TARGET_ALL | Attribute::IS_REPEATABLE)]
class AlphaLowercase extends Parameter
{
    public function __construct(
        string|null $name = null,
        Cast|null $cast = null,
        bool $isOptional = false,
        bool $shouldCapture = true,
        mixed $default = null,
    ) {
        parent::__construct(
            name         : $name ?? ParameterName::ALPHA_LOWERCASE,
            regex        : Regex::ALPHA_LOWERCASE,
            cast         : $cast,
            isOptional   : $isOptional,
            shouldCapture: $shouldCapture,
            default      : $default,
        );
    }
}
