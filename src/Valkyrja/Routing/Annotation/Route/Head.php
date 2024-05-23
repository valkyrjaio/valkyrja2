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

namespace Valkyrja\Routing\Annotation\Route;

use Valkyrja\Http\Message\Constant\RequestMethod;
use Valkyrja\Routing\Annotation\Route;

/**
 * Class Head.
 *
 * @author Melech Mizrachi
 */
class Head extends Route
{
    /**
     * Head constructor.
     */
    public function __construct()
    {
        $this->methods = [
            RequestMethod::HEAD,
        ];
    }
}
