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

namespace Valkyrja\Exception;

use Throwable;

use const E_ALL;

/**
 * Interface ExceptionHandler.
 *
 * @author Melech Mizrachi
 */
interface ExceptionHandler
{
    /**
     * Enable exception handler.
     *
     * @param int  $errorReportingLevel [optional] The error reporting level
     * @param bool $displayErrors       [optional] Whether to display errors
     */
    public static function enable(int $errorReportingLevel = E_ALL, bool $displayErrors = false): void;

    /**
     * Get trace code for a throwable/exception.
     *
     * @param Throwable $exception The exception/throwable
     */
    public static function getTraceCode(Throwable $exception): string;
}
