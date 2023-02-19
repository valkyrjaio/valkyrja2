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

use Valkyrja\Dispatcher\Exceptions\InvalidClosureException;
use Valkyrja\Dispatcher\Exceptions\InvalidDispatchCapabilityException;
use Valkyrja\Dispatcher\Exceptions\InvalidFunctionException;
use Valkyrja\Dispatcher\Exceptions\InvalidMethodException;
use Valkyrja\Dispatcher\Exceptions\InvalidPropertyException;

/**
 * Interface Validator.
 *
 * @author Melech Mizrachi
 */
interface Validator
{
    /**
     * Validate the dispatch's dispatch capabilities.
     *
     * @param Dispatch $dispatch The dispatch
     *
     * @throws InvalidDispatchCapabilityException
     * @throws InvalidFunctionException
     * @throws InvalidMethodException
     * @throws InvalidPropertyException
     * @throws InvalidClosureException
     */
    public function dispatch(Dispatch $dispatch): void;

    /**
     * Validate the class and method of a dispatch.
     *
     * @param Dispatch $dispatch The dispatch
     *
     * @throws InvalidMethodException
     */
    public function classMethod(Dispatch $dispatch): void;

    /**
     * Validate the class and property of a dispatch.
     *
     * @param Dispatch $dispatch The dispatch
     *
     * @throws InvalidPropertyException
     */
    public function classProperty(Dispatch $dispatch): void;

    /**
     * Validate the function of a dispatch.
     *
     * @param Dispatch $dispatch The dispatch
     *
     * @throws InvalidFunctionException
     */
    public function func(Dispatch $dispatch): void;
}
