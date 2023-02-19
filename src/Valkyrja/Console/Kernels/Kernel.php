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

namespace Valkyrja\Console\Kernels;

use Throwable;
use Valkyrja\Console\Console;
use Valkyrja\Console\Enums\ExitCode;
use Valkyrja\Console\Events\ConsoleKernelHandled;
use Valkyrja\Console\Events\ConsoleKernelTerminate;
use Valkyrja\Console\Input;
use Valkyrja\Console\Kernel as Contract;
use Valkyrja\Console\Output;
use Valkyrja\Container\Container;
use Valkyrja\Event\Events;
use Valkyrja\Log\Logger;

use function Valkyrja\dd;

/**
 * Class Kernel.
 *
 * @author Melech Mizrachi
 */
class Kernel implements Contract
{
    /**
     * The console.
     */
    protected Console $console;

    /**
     * The container.
     */
    protected Container $container;

    /**
     * The events manager.
     */
    protected Events $events;

    /**
     * Kernel constructor.
     *
     * @param Console   $console   The console
     * @param Container $container The container
     * @param Events    $events    The events manager
     */
    public function __construct(Console $console, Container $container, Events $events)
    {
        $this->console   = $console;
        $this->container = $container;
        $this->events    = $events;
    }

    /**
     * @inheritDoc
     */
    public function handle(Input $input, Output $output): int
    {
        try {
            $exitCode = $this->console->dispatch($input, $output);
        } catch (Throwable $exception) {
            // Log the error
            $this->logException($exception);

            // Show the exception
            // TODO: Implement
            dd($exception);

            $exitCode = ExitCode::FAILURE;
        }

        $this->events->trigger(ConsoleKernelHandled::class, [new ConsoleKernelHandled($input, $exitCode)]);

        return $exitCode;
    }

    /**
     * @inheritDoc
     */
    public function terminate(Input $input, int $exitCode): void
    {
        $this->events->trigger(ConsoleKernelTerminate::class, [new ConsoleKernelTerminate($input, $exitCode)]);
    }

    /**
     * @inheritDoc
     */
    public function run(Input $input = null, Output $output = null): int
    {
        $this->container->setSingleton(
            Input::class,
            $input ?? $input = $this->container->getSingleton(Input::class)
        );

        $this->container->setSingleton(
            Output::class,
            $output ?? $output = $this->container->getSingleton(Output::class)
        );

        // Handle the request and get the response
        $exitCode = $this->handle($input, $output);

        // Terminate the application
        $this->terminate($input, $exitCode);

        return $exitCode;
    }

    /**
     * Log an error.
     */
    protected function logException(Throwable $exception): void
    {
        /** @var Logger $logger */
        $logger = $this->container->getSingleton(Logger::class);

        $logger->exception($exception, 'Kernel Error');
    }
}
