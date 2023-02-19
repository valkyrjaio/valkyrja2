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

use Exception;
use Valkyrja\Auth\User;
use Valkyrja\Http\Request;
use Valkyrja\Http\Response;

/**
 * Abstract Class AuthorizedMiddleware.
 *
 * @author Melech Mizrachi
 */
abstract class AuthorizedMiddleware extends AuthMiddleware
{
    /**
     * @inheritDoc
     */
    public static function before(Request $request): Request|Response
    {
        try {
            // Check if the user is authorized
            if (static::checkAuthorized($request, static::getUser())) {
                // Only continue the request if the user is authorized
                return $request;
            }
        } catch (Exception $exception) {
            static::handleException($exception);
        }

        return static::getFailedResponse($request);
    }

    /**
     * Check if the authenticated user is authorized.
     *
     * @param Request $request The request
     * @param User    $user    The authenticated user
     */
    abstract protected static function checkAuthorized(Request $request, User $user): bool;
}
