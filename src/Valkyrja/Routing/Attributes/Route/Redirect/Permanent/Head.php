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

namespace Valkyrja\Routing\Attributes\Route\Redirect\Permanent;

use Attribute;
use Valkyrja\Http\Constants\StatusCode;
use Valkyrja\Routing\Attributes\Route\Redirect\Head as Model;

/**
 * Attribute Head.
 *
 * @author Melech Mizrachi
 */
#[Attribute(Attribute::TARGET_ALL | Attribute::IS_REPEATABLE)]
class Head extends Model
{
    public function __construct(
        string $path,
        string $to,
        string $name = null,
        bool $secure = null,
    ) {
        parent::__construct(
            path  : $path,
            to    : $to,
            code  : StatusCode::MOVED_PERMANENTLY,
            name  : $name,
            secure: $secure,
        );
    }
}
