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
use Valkyrja\Api\Api;
use Valkyrja\Api\Constants\Status;
use Valkyrja\Auth\Adapter;
use Valkyrja\Auth\Auth;
use Valkyrja\Auth\Config\Config;
use Valkyrja\Auth\Constants\RouteName;
use Valkyrja\Auth\Repository;
use Valkyrja\Auth\User;
use Valkyrja\Config\Constants\ConfigKeyPart;
use Valkyrja\Http\Constants\StatusCode;
use Valkyrja\Http\JsonResponse;
use Valkyrja\Http\Request;
use Valkyrja\Http\Response;
use Valkyrja\Log\Facades\Logger;
use Valkyrja\Routing\Middleware\Middleware;
use Valkyrja\Routing\Url;
use Valkyrja\Type\Support\Arr;

/**
 * Abstract Class AuthenticatedMiddleware.
 *
 * @author Melech Mizrachi
 */
abstract class AuthMiddleware extends Middleware
{
    /**
     * The auth service.
     */
    protected static Auth $auth;

    /**
     * The repository.
     */
    protected static Repository $repository;

    /**
     * The config.
     */
    protected static Config|array $config;

    /**
     * The adapter to use.
     *
     * @var class-string<Adapter>|null
     */
    protected static ?string $adapterName = null;

    /**
     * The user class to use.
     *
     * @var class-string<User>|null
     */
    protected static ?string $userEntity = null;

    /**
     * The error message to use.
     */
    protected static string $errorMessage = 'Unauthorized';

    /**
     * Whether to force a JSON response on failure.
     */
    protected static bool $forceJson = false;

    /**
     * Get auth.
     */
    protected static function getAuth(): Auth
    {
        return self::$auth ??= self::getContainer()->getSingleton(Auth::class);
    }

    /**
     * Get the config or a config item.
     *
     * @param string|null $key     [optional]
     * @param mixed|null  $default [optional]
     *
     * @return mixed|null
     */
    protected static function getConfig(string $key = null, mixed $default = null): mixed
    {
        $config = self::$config ??= static::getAuth()->getConfig();

        if ($key !== null) {
            return Arr::getValueDotNotation($config, $key, $default);
        }

        return $config;
    }

    /**
     * Get the auth repository for the user entity specified (or default to the application default).
     */
    protected static function getRepository(): Repository
    {
        return self::$repository ??= static::getAuth()->getRepository(static::$userEntity, static::$adapterName);
    }

    /**
     * Get the authenticated user.
     */
    protected static function getUser(): User
    {
        return static::getRepository()->getUser();
    }

    /**
     * Get the failed response.
     *
     * @param Request $request The request
     */
    protected static function getFailedResponse(Request $request): Response
    {
        if (static::$forceJson || $request->isXmlHttpRequest()) {
            return static::getFailedJsonResponse();
        }

        return static::getFailedRegularResponse();
    }

    /**
     * Get the failed JSON response.
     */
    protected static function getFailedJsonResponse(): JsonResponse
    {
        /** @var Api $api */
        $api  = static::getContainer()->getSingleton(Api::class);
        $json = $api->jsonFromArray([]);
        $json->setData();
        $json->setMessage(static::$errorMessage);
        $json->setStatusCode(StatusCode::UNAUTHORIZED);
        $json->setStatus(Status::ERROR);

        return self::getResponseFactory()->createJsonResponse(
            $json->asArray(),
            StatusCode::UNAUTHORIZED
        );
    }

    /**
     * Get the failed non-JSON response.
     */
    protected static function getFailedRegularResponse(): Response
    {
        if ($authenticateUrl = static::getConfig(ConfigKeyPart::AUTHENTICATE_URL)) {
            return self::getResponseFactory()->createRedirectResponse(
                $authenticateUrl,
                StatusCode::UNAUTHORIZED
            );
        }

        /** @var Url $url */
        $url = self::getContainer()->getSingleton(Url::class);

        return self::getResponseFactory()->createRedirectResponse(
            $url->getUrl((string) static::getConfig(ConfigKeyPart::AUTHENTICATE_ROUTE, RouteName::AUTHENTICATE)),
            StatusCode::UNAUTHORIZED
        );
    }

    /**
     * Handle an exception.
     *
     * @param Exception $exception  The exception
     * @param string    $logMessage [optional] The log message
     */
    protected static function handleException(Exception $exception, string $logMessage = ''): void
    {
        Logger::exception($exception, $logMessage);
    }
}
