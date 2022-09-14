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

namespace Valkyrja\Routing\Attributes\Route\Redirect;

use Attribute;
use Valkyrja\Routing\Attributes\Route\Post as Model;

/**
 * Attribute Post.
 *
 * @author Melech Mizrachi
 */
#[Attribute(Attribute::TARGET_ALL | Attribute::IS_REPEATABLE)]
class Post extends Model
{
    public function __construct(
        string $path,
        string $to,
        int $code = null,
        bool $secure = null,
    ) {
        parent::__construct(
            path  : $path,
            secure: $secure,
            to    : $to,
            code  : $code,
        );
    }
}
