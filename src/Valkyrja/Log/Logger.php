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

namespace Valkyrja\Log;

use Throwable;
use Valkyrja\Manager\Manager;

/**
 * Interface Logger.
 *
 * @author Melech Mizrachi
 *
 * @extends Manager<Driver, Factory>
 */
interface Logger extends Manager
{
    /**
     * @inheritDoc
     */
    public function use(string $name = null): Driver;

    /**
     * Log a debug message.
     *
     * @param string $message The message
     * @param array  $context [optional] The context
     */
    public function debug(string $message, array $context = []): void;

    /**
     * Log an info message.
     *
     * @param string $message The message
     * @param array  $context [optional] The context
     */
    public function info(string $message, array $context = []): void;

    /**
     * Log a notice message.
     *
     * @param string $message The message
     * @param array  $context [optional] The context
     */
    public function notice(string $message, array $context = []): void;

    /**
     * Log a warning message.
     *
     * @param string $message The message
     * @param array  $context [optional] The context
     */
    public function warning(string $message, array $context = []): void;

    /**
     * Log a error message.
     *
     * @param string $message The message
     * @param array  $context [optional] The context
     */
    public function error(string $message, array $context = []): void;

    /**
     * Log a critical message.
     *
     * @param string $message The message
     * @param array  $context [optional] The context
     */
    public function critical(string $message, array $context = []): void;

    /**
     * Log a alert message.
     *
     * @param string $message The message
     * @param array  $context [optional] The context
     */
    public function alert(string $message, array $context = []): void;

    /**
     * Log a emergency message.
     *
     * @param string $message The message
     * @param array  $context [optional] The context
     */
    public function emergency(string $message, array $context = []): void;

    /**
     * Log a message.
     *
     * @param string $level   The log level
     * @param string $message The message
     * @param array  $context [optional] The context
     */
    public function log(string $level, string $message, array $context = []): void;

    /**
     * Log an exception or throwable.
     *
     * @param Throwable $exception The exception
     * @param string    $message   The message
     * @param array     $context   [optional] The context
     */
    public function exception(Throwable $exception, string $message, array $context = []): void;
}
