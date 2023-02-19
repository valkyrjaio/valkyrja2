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

namespace Valkyrja\Dispatcher\Dispatchers;

use InvalidArgumentException;
use Valkyrja\Container\Container;
use Valkyrja\Container\ContextAwareContainer;
use Valkyrja\Dispatcher\Constants\Constant;
use Valkyrja\Dispatcher\Dispatch;
use Valkyrja\Dispatcher\Dispatcher as Contract;
use Valkyrja\Dispatcher\Exceptions\InvalidClosureException;
use Valkyrja\Dispatcher\Exceptions\InvalidFunctionException;
use Valkyrja\Dispatcher\Exceptions\InvalidMethodException;
use Valkyrja\Dispatcher\Exceptions\InvalidPropertyException;
use Valkyrja\Type\Exceptions\InvalidClassProvidedException;

use function is_string;

/**
 * Class Dispatcher.
 *
 * @author Melech Mizrachi
 */
class Dispatcher implements Contract
{
    /**
     * Dispatcher constructor.
     *
     * @param Container $container The container
     */
    public function __construct(
        protected Container $container
    ) {
    }

    /**
     * @inheritDoc
     */
    public function dispatch(Dispatch $dispatch, array $arguments = null): mixed
    {
        // Get the arguments with dependencies
        $arguments = $this->getArguments($dispatch, $arguments);

        // Attempt to dispatch the dispatch
        $response = $this->dispatchClassMethod($dispatch, $arguments)
            ?? $this->dispatchClassProperty($dispatch)
            ?? $this->dispatchClass($dispatch, $arguments)
            ?? $this->dispatchFunction($dispatch, $arguments)
            ?? $this->dispatchClosure($dispatch, $arguments);
        // TODO: Add Constant and Variable ability

        // If the response was initially null and we added the dispatched text to avoid calling each subsequent
        // dispatcher thereafter so let's reset it to null
        return $response !== Constant::DISPATCHED ? $response : null;
    }

    /**
     * @inheritDoc
     */
    public function dispatchClassMethod(Dispatch $dispatch, array $arguments = null): mixed
    {
        // Ensure a class and method exist before continuing
        if (! $dispatch->isMethod()) {
            return null;
        }

        if (! $method = $dispatch->getMethod()) {
            throw new InvalidMethodException("Expecting a valid method: $method provided");
        }

        $class     = $this->getClassFromDispatch($dispatch);
        $arguments = $arguments ?? [];
        /** @var mixed $response */
        $response = is_string($class)
            ? $class::$method(...$arguments)
            : (/** @var object $class */
                $class->$method(...$arguments)
            );

        return $response ?? Constant::DISPATCHED;
    }

    /**
     * @inheritDoc
     */
    public function dispatchClassProperty(Dispatch $dispatch): mixed
    {
        // Ensure a class and property exist before continuing
        if (! $dispatch->isProperty()) {
            return null;
        }

        if (! $property = $dispatch->getProperty()) {
            throw new InvalidPropertyException("Expecting a valid property: $property provided");
        }

        $class = $this->getClassFromDispatch($dispatch);
        /** @var mixed $response */
        $response = is_string($class) ? $class::$$property : $class->{$property};

        return $response ?? Constant::DISPATCHED;
    }

    /**
     * @inheritDoc
     */
    public function dispatchClass(Dispatch $dispatch, array $arguments = null): mixed
    {
        // Ensure a class exists before continuing
        if (! $dispatch->isClass()) {
            return null;
        }

        if (! $className = $dispatch->getClass()) {
            throw new InvalidClassProvidedException("Expecting a valid class: $className provided");
        }

        // If the class is the id then this item is not yet set in the
        // service container so it needs a new instance returned
        if ($className === $dispatch->getId()) {
            $arguments = $arguments ?? [];
            $class     = new $className(...$arguments);
        } else {
            // Get the class through the container
            $class = $this->container->get($className, $arguments ?? []);
        }

        return $class ?? Constant::DISPATCHED;
    }

    /**
     * @inheritDoc
     */
    public function dispatchFunction(Dispatch $dispatch, array $arguments = null): mixed
    {
        // Ensure a function exists before continuing
        if (! $dispatch->isFunction()) {
            return null;
        }

        if (! $function = $dispatch->getFunction()) {
            throw new InvalidFunctionException("Expecting a valid callable: $function provided");
        }

        $arguments = $arguments ?? [];
        $response  = $function(...$arguments);

        return $response ?? Constant::DISPATCHED;
    }

    /**
     * @inheritDoc
     */
    public function dispatchClosure(Dispatch $dispatch, array $arguments = null): mixed
    {
        // Ensure a closure exists before continuing
        if (! $dispatch->isClosure()) {
            return null;
        }

        if (! $closure = $dispatch->getClosure()) {
            throw new InvalidClosureException('Expecting a valid closure: Null provided');
        }

        $arguments = $arguments ?? [];
        $response  = $closure(...$arguments);

        return $response ?? Constant::DISPATCHED;
    }

    /**
     * Get class from dispatch.
     *
     * @param Dispatch $dispatch The dispatch
     *
     * @throws InvalidArgumentException
     *
     * @return object|class-string
     */
    protected function getClassFromDispatch(Dispatch $dispatch): mixed
    {
        if (! $class = $dispatch->getClass()) {
            throw new InvalidArgumentException('Invalid class defined in dispatch model.');
        }

        return $dispatch->isStatic() ? $class : $this->container->get($class);
    }

    /**
     * Get a dispatch's arguments.
     *
     * @param Dispatch   $dispatch  The dispatch
     * @param array|null $arguments [optional] The arguments
     */
    protected function getArguments(Dispatch $dispatch, array $arguments = null): ?array
    {
        // Get either the arguments passed or from the dispatch model
        $arguments = $arguments ?? $dispatch->getArguments();

        // Set the listener arguments to a new blank array
        $dependencies = $this->getDependencies($dispatch);

        // If there are no arguments
        if ($arguments === null) {
            // Return the dependencies only
            return $dependencies;
        }

        // Iterate through the arguments
        foreach ($arguments as $argument) {
            // Append the argument to the arguments list
            $dependencies[] = $this->getArgumentValue($argument);
        }

        return $dependencies;
    }

    /**
     * Get a dispatch's dependencies.
     *
     * @param Dispatch $dispatch The dispatch
     */
    protected function getDependencies(Dispatch $dispatch): ?array
    {
        $dependenciesInstances = [];

        // If there are dependencies
        if (($dependencies = $dispatch->getDependencies()) === null) {
            return $dependenciesInstances;
        }

        $context = $dispatch->getClass() ?? $dispatch->getFunction() ?? null;
        $member  = $dispatch->getMethod() ?? $dispatch->getProperty();

        $containerContext = null;

        $container  = $this->container;
        $hasContext = $context !== null && $container instanceof ContextAwareContainer;

        if ($hasContext) {
            /** @var ContextAwareContainer $container */
            $containerContext = $container->withContext($context, $member);
        }

        // Iterate through all the dependencies
        foreach ($dependencies as $dependency) {
            // Set the dependency from the container
            $dependenciesInstances[] = $hasContext && $containerContext?->has($dependency)
                ? $containerContext->get($dependency)
                : $container->get($dependency);
        }

        return $dependenciesInstances;
    }

    /**
     * Get argument value.
     *
     * @param mixed $argument The argument
     */
    protected function getArgumentValue(mixed $argument): mixed
    {
        if ($argument instanceof Dispatch) {
            // Dispatch the argument and set the results to the argument
            $argument = $this->dispatch($argument);
        }

        return $argument;
    }
}
