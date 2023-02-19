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

namespace Valkyrja\Routing\Controllers;

use Throwable;
use Valkyrja\Api\Api;
use Valkyrja\Api\Constants\Status;
use Valkyrja\Http\Constants\StatusCode;
use Valkyrja\Http\JsonResponse;
use Valkyrja\Log\Logger;

/**
 * Abstract Class ApiController.
 *
 * @author Melech Mizrachi
 */
abstract class ApiController extends Controller
{
    /**
     * The Api service.
     */
    private static Api $api;

    /**
     * Create an Api JsonResponse.
     *
     * @param array         $data       The json data
     * @param string|null   $message    [optional] The message
     * @param string|null   $status     [optional] The json status
     * @param int|null      $statusCode [optional] The status code
     * @param string[]|null $errors     [optional] The errors
     * @param string[]|null $warnings   [optional] The warnings
     */
    public static function createApiJsonResponse(
        array $data = [],
        string $message = null,
        string $status = null,
        int $statusCode = null,
        array $errors = null,
        array $warnings = null
    ): JsonResponse {
        $json = self::getApi()->jsonFromArray($data);

        $json->setMessage($message);
        $json->setStatus($status ?? Status::SUCCESS);
        $json->setStatusCode($statusCode ?? StatusCode::OK);
        $json->setErrors($errors ?? []);
        $json->setWarnings($warnings ?? []);

        return self::getResponseFactory()->createJsonResponse($json->asArray(), $statusCode);
    }

    /**
     * Get an exception response.
     *
     * @param Throwable     $exception  The exception
     * @param string|null   $message    [optional] The message to override
     * @param string|null   $status     [optional] The status
     * @param int|null      $statusCode [optional] The status code
     * @param string[]|null $errors     [optional] The errors
     * @param string[]|null $warnings   [optional] The warnings
     */
    public static function getExceptionResponse(
        Throwable $exception,
        string $message = null,
        string $status = null,
        int $statusCode = null,
        array $errors = null,
        array $warnings = null
    ): JsonResponse {
        $url        = self::getRequest()->getUri()->getPath();
        $logMessage = "$message\nUrl: $url";
        $logger     = self::getContainer()->getSingleton(Logger::class);

        // Trace code and additional details get added in Logger::exception()
        $logger->exception($exception, $logMessage);

        return self::createApiJsonResponse(
            [
                'traceCode' => static::getExceptionTraceCode($exception),
            ],
            $message ?? $exception->getMessage(),
            $status ?? Status::ERROR,
            $statusCode ?? StatusCode::INTERNAL_SERVER_ERROR,
            $errors,
            $warnings
        );
    }

    /**
     * Get the Api service.
     */
    protected static function getApi(): Api
    {
        return self::$api ??= self::getContainer()->getSingleton(Api::class);
    }

    /**
     * Get exception trace code.
     *
     * @param Throwable $exception The exception
     */
    protected static function getExceptionTraceCode(Throwable $exception): string
    {
        return md5(serialize($exception));
    }
}
