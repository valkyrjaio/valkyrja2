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

namespace Valkyrja\Auth\Middleware;

use Valkyrja\Http\Request;
use Valkyrja\Http\Response;

/**
 * Class AuthenticatedMiddleware.
 *
 * @author Melech Mizrachi
 */
class AuthenticatedMiddleware extends AuthMiddleware
{
    /**
     * The error message to use.
     */
    protected static string $errorMessage = 'Must be logged in.';

    /**
     * @inheritDoc
     */
    public static function before(Request $request): Request|Response
    {
        $repository = static::getRepository();

        if (! $repository->isAuthenticated()) {
            return static::getFailedResponse($request);
        }

        return $request;
    }
}
