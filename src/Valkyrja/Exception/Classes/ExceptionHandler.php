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

namespace Valkyrja\Exception\Classes;

use Throwable;
use Valkyrja\Exception\ExceptionHandler as Contract;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Whoops\Util\Misc;

use const E_ALL;

/**
 * Class ExceptionHandler.
 *
 * @author Melech Mizrachi
 */
class ExceptionHandler implements Contract
{
    /**
     * Whether debug is enabled or not.
     */
    public static bool $enabled = false;

    /**
     * Enable debug mode.
     *
     * @param int  $errorReportingLevel [optional] The error reporting level
     * @param bool $displayErrors       [optional] Whether to display errors
     */
    public static function enable(int $errorReportingLevel = E_ALL, bool $displayErrors = false): void
    {
        // If debug is already enabled
        if (static::$enabled) {
            // Don't do things twice
            return;
        }

        // Debug is enabled
        static::$enabled = true;

        $run = new Run();

        // We want the error page to be shown by default, if this is a
        // regular request, so that's the first thing to go into the stack:
        $run->pushHandler(new PrettyPageHandler());

        // Now, we want a second handler that will run before the error page,
        // and immediately return an error message in JSON format, if something
        // goes awry.
        if (Misc::isAjaxRequest()) {
            $jsonHandler = new JsonResponseHandler();

            // You can also tell JsonResponseHandler to give you a full stack trace:
            // $jsonHandler->addTraceToOutput(true);

            // You can also return a result compliant to the json:api spec
            // re: http://jsonapi.org/examples/#error-objects
            // tl;dr: error[] becomes errors[[]]
            $jsonHandler->setJsonApi(true);

            // And push it into the stack:
            $run->pushHandler($jsonHandler);
        }

        // That's it! Register Whoops and throw a dummy exception:
        $run->register();
    }

    /**
     * Get trace code for a throwable/exception.
     *
     * @param Throwable $exception The exception/throwable
     */
    public static function getTraceCode(Throwable $exception): string
    {
        return md5(serialize($exception));
    }
}
