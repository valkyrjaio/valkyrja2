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
use Valkyrja\Config\Constants\ConfigKeyPart;
use Valkyrja\Http\Constants\StatusCode;
use Valkyrja\Http\Request;
use Valkyrja\Http\Response;
use Valkyrja\Routing\Url;

/**
 * Class NotAuthenticatedMiddleware.
 *
 * @author Melech Mizrachi
 */
class NotAuthenticatedMiddleware extends AuthMiddleware
{
    /**
     * The error message to use.
     */
    protected static string $errorMessage = 'Must not be logged in.';

    /**
     * @inheritDoc
     */
    public static function before(Request $request): Request|Response
    {
        $repository = static::getRepository();

        if ($repository->isAuthenticated()) {
            return static::getFailedResponse($request);
        }

        return $request;
    }

    /**
     * @inheritDoc
     */
    protected static function getFailedRegularResponse(): Response
    {
        if ($authenticateUrl = static::getConfig(ConfigKeyPart::NOT_AUTHENTICATE_URL)) {
            return self::getResponseFactory()->createRedirectResponse(
                $authenticateUrl,
                StatusCode::FOUND
            );
        }

        /** @var Url $url */
        $url = self::getContainer()->getSingleton(Url::class);

        return self::getResponseFactory()->createRedirectResponse(
            $url->getUrl((string) static::getConfig(ConfigKeyPart::NOT_AUTHENTICATE_ROUTE, RouteName::DASHBOARD)),
            StatusCode::FOUND
        );
    }
}
