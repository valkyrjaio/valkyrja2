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

use Valkyrja\Auth\Constants\RouteName;
use Valkyrja\Http\Constants\StatusCode;
use Valkyrja\Http\Request;
use Valkyrja\Http\Response;
use Valkyrja\Routing\Url;

/**
 * Class ReAuthenticatedMiddleware.
 *
 * @author Melech Mizrachi
 */
class ReAuthenticatedMiddleware extends AuthMiddleware
{
    /**
     * Middleware handler for before a request is dispatched.
     *
     * @param Request $request The request
     */
    public static function before(Request $request): Request|Response
    {
        if (static::getRepository()->isReAuthenticationRequired()) {
            return static::getFailedAuthenticationResponse($request);
        }

        return $request;
    }

    /**
     * Get the failed authentication response.
     *
     * @param Request $request The request
     */
    protected static function getFailedAuthenticationResponse(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            return self::getResponseFactory()->createJsonResponse([], StatusCode::LOCKED);
        }

        /** @var Url $url */
        $url = self::getContainer()->getSingleton(Url::class);

        return self::getResponseFactory()->createRedirectResponse(
            $url->getUrl((string) static::getConfig('passwordConfirmRoute', RouteName::PASSWORD_CONFIRM))
        );
    }
}
