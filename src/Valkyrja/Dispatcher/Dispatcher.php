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

namespace Valkyrja\Dispatcher;

/**
 * Interface Dispatcher.
 *
 * @author Melech Mizrachi
 */
interface Dispatcher
{
    /**
     * Dispatch a class method.
     *
     * @param Dispatch   $dispatch  The dispatch
     * @param array|null $arguments The arguments
     */
    public function dispatchClassMethod(Dispatch $dispatch, array $arguments = null): mixed;

    /**
     * Dispatch a class property.
     *
     * @param Dispatch $dispatch The dispatch
     */
    public function dispatchClassProperty(Dispatch $dispatch): mixed;

    /**
     * Dispatch a class.
     *
     * @param Dispatch   $dispatch  The dispatch
     * @param array|null $arguments The arguments
     */
    public function dispatchClass(Dispatch $dispatch, array $arguments = null): mixed;

    /**
     * Dispatch a function.
     *
     * @param Dispatch   $dispatch  The dispatch
     * @param array|null $arguments The arguments
     */
    public function dispatchFunction(Dispatch $dispatch, array $arguments = null): mixed;

    /**
     * Dispatch a closure.
     *
     * @param Dispatch   $dispatch  The dispatch
     * @param array|null $arguments The arguments
     */
    public function dispatchClosure(Dispatch $dispatch, array $arguments = null): mixed;

    /**
     * Dispatch a callable.
     *
     * @param Dispatch   $dispatch  The dispatch
     * @param array|null $arguments The arguments
     */
    public function dispatch(Dispatch $dispatch, array $arguments = null): mixed;
}
