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

namespace Valkyrja\Routing\Annotations\Route\Redirect\Permanent;

use Valkyrja\Http\Constant\StatusCode;
use Valkyrja\Routing\Annotations\Route\Redirect\Trace as ParentClass;

/**
 * Class Trace.
 *
 * @author Melech Mizrachi
 */
class Trace extends ParentClass
{
    /**
     * Trace constructor.
     */
    public function __construct()
    {
        $this->code = StatusCode::MOVED_PERMANENTLY;

        parent::__construct();
    }
}
