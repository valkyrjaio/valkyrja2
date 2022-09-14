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

namespace Valkyrja\Routing\Attributes\Route\Secure;

use Attribute;
use Valkyrja\Routing\Attributes\Route\Delete as Model;

/**
 * Attribute Delete.
 *
 * @author Melech Mizrachi
 */
#[Attribute(Attribute::TARGET_ALL | Attribute::IS_REPEATABLE)]
class Delete extends Model
{
    public function __construct(
        string $path,
        array $parameters = null,
        array $middleware = null,
    ) {
        parent::__construct(
            path      : $path,
            parameters: $parameters,
            middleware: $middleware,
            secure    : true,
        );
    }
}
