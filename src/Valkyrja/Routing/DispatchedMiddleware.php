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

namespace Valkyrja\Routing;

use Valkyrja\Http\Request\Contract\ServerRequest;
use Valkyrja\Http\Response\Contract\Response;

/**
 * Interface DispatchedMiddleware.
 *
 * @author Melech Mizrachi
 */
interface DispatchedMiddleware
{
    /**
     * Middleware handler for after a request is dispatched.
     *
     * @param ServerRequest $request  The request
     * @param Response      $response The response
     *
     * @return Response
     */
    public static function dispatched(ServerRequest $request, Response $response): Response;
}
