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

namespace Valkyrja\HttpKernel\Events;

use Valkyrja\Http\Request;
use Valkyrja\Http\Response;

/**
 * Class RequestTerminating.
 *
 * @author Melech Mizrachi
 */
class RequestTerminating
{
    /**
     * The request.
     */
    public Request $request;

    /**
     * The response.
     */
    public Response $response;

    /**
     * RequestTerminating constructor.
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request  = $request;
        $this->response = $response;
    }
}
