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

namespace Valkyrja\Dispatcher\Validators;

use Valkyrja\Dispatcher\Dispatch;
use Valkyrja\Dispatcher\Exceptions\InvalidDispatchCapabilityException;
use Valkyrja\Dispatcher\Exceptions\InvalidFunctionException;
use Valkyrja\Dispatcher\Exceptions\InvalidMethodException;
use Valkyrja\Dispatcher\Exceptions\InvalidPropertyException;
use Valkyrja\Dispatcher\Validator as Contract;

use function is_callable;

/**
 * Class Validator.
 *
 * @author Melech Mizrachi
 */
class Validator implements Contract
{
    /**
     * @inheritDoc
     */
    public function dispatch(Dispatch $dispatch): void
    {
        $this->validateNotEmpty($dispatch);
        $this->classMethod($dispatch);
        $this->classProperty($dispatch);
        $this->func($dispatch);
    }

    /**
     * @inheritDoc
     */
    public function classMethod(Dispatch $dispatch): void
    {
        if ($this->isInvalidClassMethod($dispatch)) {
            throw new InvalidMethodException(
                'Method does not exist in class : '
                . ($dispatch->getName() ?? '') . ' '
                . ($dispatch->getClass() ?? '')
                . '@'
                . ($dispatch->getMethod() ?? '')
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function classProperty(Dispatch $dispatch): void
    {
        if ($this->isInvalidClassProperty($dispatch)) {
            throw new InvalidPropertyException(
                'Property does not exist in class : '
                . ($dispatch->getName() ?? '') . ' '
                . ($dispatch->getClass() ?? '')
                . '@'
                . ($dispatch->getProperty() ?? '')
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function func(Dispatch $dispatch): void
    {
        // If a function is set and is not callable
        if ($this->isInvalidFunction($dispatch)) {
            // Throw a new invalid function exception
            throw new InvalidFunctionException(
                'Function is not callable for : '
                . ($dispatch->getName() ?? '') . ' '
                . ($dispatch->getFunction() ?? '')
            );
        }
    }

    /**
     * Determine if a dispatch's class/method combination is invalid.
     *
     * @param Dispatch $dispatch The dispatch
     */
    protected function isInvalidClassMethod(Dispatch $dispatch): bool
    {
        return $dispatch->isMethod()
            && ($class  = $dispatch->getClass())
            && ($method = $dispatch->getMethod())
            && ! method_exists($class, $method);
    }

    /**
     * Determine if a dispatch's class/property combination is invalid.
     *
     * @param Dispatch $dispatch The dispatch
     */
    protected function isInvalidClassProperty(Dispatch $dispatch): bool
    {
        return $dispatch->isProperty()
            && ($class    = $dispatch->getClass())
            && ($property = $dispatch->getProperty())
            && ! property_exists($class, $property);
    }

    /**
     * Determine if a dispatch's function is invalid.
     *
     * @param Dispatch $dispatch The dispatch
     */
    protected function isInvalidFunction(Dispatch $dispatch): bool
    {
        return $dispatch->isFunction() && ! is_callable($dispatch->getFunction());
    }

    /**
     * Verify that a dispatch has something to dispatch.
     *
     * @param Dispatch $dispatch The dispatch
     *
     * @throws InvalidDispatchCapabilityException
     */
    protected function validateNotEmpty(Dispatch $dispatch): void
    {
        // If a function, closure, and class or method are not set
        if ($this->isEmptyDispatch($dispatch)) {
            // Throw a new invalid dispatch capability exception
            throw new InvalidDispatchCapabilityException(
                'Dispatch capability is not valid for : '
                . ($dispatch->getName() ?? '')
            );
        }
    }

    /**
     * Check if a dispatch is empty.
     *
     * @param Dispatch $dispatch The dispatch
     */
    protected function isEmptyDispatch(Dispatch $dispatch): bool
    {
        return ! $dispatch->getFunction()
            && ! $dispatch->getClosure()
            && ! $dispatch->getClass()
            && ! $dispatch->getMethod()
            && ! $dispatch->getProperty();
    }
}
