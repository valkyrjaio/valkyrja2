<?php

/*
 * This file is part of the Valkyrja framework.
 *
 * (c) Melech Mizrachi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valkyrja\Container;

use Valkyrja\Container\Exceptions\InvalidContextException;
use Valkyrja\Container\Exceptions\InvalidServiceIdException;
use Valkyrja\Contracts\Application;
use Valkyrja\Contracts\Container\Annotations\ContainerAnnotations;
use Valkyrja\Contracts\Container\Container as ContainerContract;
use Valkyrja\Dispatcher\Dispatcher;

/**
 * Class Container
 *
 * @package Valkyrja\Container
 *
 * @author  Melech Mizrachi
 */
class Container implements ContainerContract
{
    use Dispatcher;

    /**
     * The aliases.
     *
     * @var string[]
     */
    protected static $aliases = [];

    /**
     * The services.
     *
     * @var \Valkyrja\Container\Service[]
     */
    protected static $services = [];

    /**
     * The singletons.
     *
     * @var array
     */
    protected static $singletons = [];

    /**
     * The services provided by service providers that are deferred.
     *
     * @var array[]
     */
    protected static $provided = [];

    /**
     * Set an alias to the container.
     *
     * @param string $alias     The alias
     * @param string $serviceId The service to return
     *
     * @return void
     */
    public function alias(string $alias, string $serviceId): void
    {
        self::$aliases[$alias] = $serviceId;
    }

    /**
     * Bind a service to the container.
     *
     * @param \Valkyrja\Container\Service $service The service model
     *
     * @return void
     *
     * @throws \Valkyrja\Container\Exceptions\InvalidServiceIdException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidClosureException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidDispatchCapabilityException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidFunctionException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidMethodException
     */
    public function bind(Service $service): void
    {
        // If there is no id
        if (null === $service->getId()) {
            // Throw a new exception
            throw new InvalidServiceIdException();
        }

        $this->verifyDispatch($service);

        self::$services[$service->getId()] = $service;
    }

    /**
     * Bind a context to the container.
     *
     * @param \Valkyrja\Container\ServiceContext $contextService The context service
     *
     * @return void
     *
     * @throws \Valkyrja\Container\Exceptions\InvalidContextException
     * @throws \Valkyrja\Container\Exceptions\InvalidServiceIdException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidClosureException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidDispatchCapabilityException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidFunctionException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidMethodException
     */
    public function context(ServiceContext $contextService): void
    {
        $context = $contextService->getClass() ?? $contextService->getFunction();
        $member = $contextService->getMethod() ?? $contextService->getProperty();

        // If the context index is null then there's no context
        if (null === $context || null === $contextService->getId()) {
            throw new InvalidContextException();
        }

        $this->bind(
            (new Service())
                ->setId($this->contextServiceId($contextService->getId(), $context, $member))
                ->setName($contextService->getName())
                ->setClass($contextService->getContextClass())
                ->setProperty($contextService->getContextProperty())
                ->setMethod($contextService->getContextMethod())
                ->setFunction($contextService->getContextFunction())
                ->setClosure($contextService->getContextClosure())
                ->setDefaults($contextService->getDefaults())
                ->setArguments($contextService->getArguments())
                ->setDependencies($contextService->getDependencies())
                ->setSingleton($contextService->isSingleton())
                ->setStatic($contextService->isStatic())
        );
    }

    /**
     * Bind a singleton to the container.
     *
     * @param string $serviceId The service
     * @param mixed  $singleton The singleton
     */
    public function singleton(string $serviceId, $singleton): void
    {
        self::$singletons[$serviceId] = $singleton;
    }

    /**
     * Register a service provider.
     *
     * @param string $serviceProvider The service provider
     *
     * @return void
     */
    public function register(string $serviceProvider): void
    {
        /** @var \Valkyrja\Support\ServiceProvider $serviceProvider */
        $deferred = $serviceProvider::$deferred;
        $provides = $serviceProvider::$provides;

        // If the service provider is deferred
        // and its defined what services it provides
        if ($deferred && $provides) {
            // Add the services to the service providers list
            foreach ($provides as $provided) {
                self::$provided[$provided] = $serviceProvider;
            }

            return;
        }

        // Create a new instance of the service provider
        $this->singleton($serviceProvider, new $serviceProvider($this->app()));
    }

    /**
     * Check whether a given service exists.
     *
     * @param string $serviceId The service
     *
     * @return bool
     */
    public function has(string $serviceId): bool
    {
        return isset(self::$services[$serviceId]) || isset(self::$aliases[$serviceId]);
    }

    /**
     * Check whether a given service has context.
     *
     * @param string $serviceId The service
     * @param string $context   The context
     *                          class name || function name || variable name
     * @param string $member    [optional] The context member
     *                          method name || property name
     *
     * @return bool
     */
    public function hasContext(string $serviceId, string $context, string $member = null): bool
    {
        // If no class or method were passed then the index will be null so return false
        if (null === $contextIndex = $this->contextServiceId($serviceId, $context, $member)) {
            return false;
        }

        return isset(self::$services[$contextIndex]);
    }

    /**
     * Check whether a given service is an alias.
     *
     * @param string $serviceId The service
     *
     * @return bool
     */
    public function isAlias(string $serviceId): bool
    {
        return isset(self::$aliases[$serviceId]);
    }

    /**
     * Check whether a given service is a singleton.
     *
     * @param string $serviceId The service
     *
     * @return bool
     */
    public function isSingleton(string $serviceId): bool
    {
        return isset(self::$singletons[$serviceId]);
    }

    /**
     * Check whether a given service is provided by a deferred service provider.
     *
     * @param string $serviceId The service
     *
     * @return bool
     */
    public function isProvided(string $serviceId): bool
    {
        return isset(self::$provided[$serviceId]);
    }

    /**
     * Get a service from the container.
     *
     * @param string $serviceId The service
     * @param array  $arguments [optional] The arguments
     * @param string $context   [optional] The context
     *                          class name || function name || variable name
     * @param string $member    [optional] The context member
     *                          method name || property name
     *
     * @return mixed
     */
    public function get(string $serviceId, array $arguments = null, string $context = null, string $member = null)
    {
        // If there is a context set for this context and member combination
        if (null !== $context && $this->hasContext($serviceId, $context, $member)) {
            // Return that context
            return $this->get($this->contextServiceId($serviceId, $context, $member), $arguments);
        }

        // If there is a context set for this context only
        if (null !== $context && $this->hasContext($serviceId, $context)) {
            // Return that context
            return $this->get($this->contextServiceId($serviceId, $context), $arguments);
        }

        // If the service is a singleton
        if ($this->isSingleton($serviceId)) {
            // Return the singleton
            return self::$singletons[$serviceId];
        }

        // If this service is an alias
        if ($this->isAlias($serviceId)) {
            // Return the appropriate service
            return $this->get(self::$aliases[$serviceId], $arguments, $context, $member);
        }

        // If the service is in the container
        if ($this->has($serviceId)) {
            // Return the made service
            return $this->make($serviceId, $arguments);
        }

        // Check if the service id is provided by a deferred service provider
        if ($this->isProvided($serviceId)) {
            /** @var \Valkyrja\Support\ServiceProvider $serviceProvider */
            $serviceProvider = self::$provided[$serviceId];
            $serviceProvider::$deferred = false;
            // Register the service provider
            $this->register($serviceProvider);

            return $this->get($serviceId, $arguments);
        }

        // If there are no argument return a new object
        if (null === $arguments) {
            return new $serviceId;
        }

        // Return a new object with the arguments
        return new $serviceId(...$arguments);
    }

    /**
     * Make a service.
     *
     * @param string     $serviceId The service id
     * @param array|null $arguments [optional] The arguments
     *
     * @return mixed
     */
    public function make(string $serviceId, array $arguments = null)
    {
        $service = self::$services[$serviceId];
        $arguments = $service->getDefaults() ?? $arguments;

        // Dispatch before make event
        $this->app()->events()->trigger('service.make', [$serviceId, $service, $arguments]);
        $this->app()->events()->trigger("service.make.{$serviceId}", [$service, $arguments]);

        // Make the object by dispatching the service
        $made = $this->dispatchCallable($service, $arguments);

        // Dispatch after make event
        $this->app()->events()->trigger('service.made', [$serviceId, $made]);
        $this->app()->events()->trigger("service.made.{$serviceId}", [$made]);

        // If the service is a singleton
        if ($service->isSingleton()) {
            $this->app()->events()->trigger('service.made.singleton', [$serviceId, $made]);
            $this->app()->events()->trigger("service.made.singleton.{$serviceId}", [$made]);
            // Set singleton
            $this->singleton($serviceId, $made);
        }

        return $made;
    }

    /**
     * Get the context service id.
     *
     * @param string $serviceId The service
     * @param string $context   The context
     *                          class name || function name || variable name
     * @param string $member    [optional] The context member
     *                          method name || property name
     *
     * @return string
     */
    public function contextServiceId(string $serviceId, string $context, string $member = null): string
    {
        $index = $serviceId . '@' . ($context ?? '');

        // If there is a method
        if (null !== $member) {
            // If there is a class
            if (null !== $context) {
                // Add the double colon to separate the method name and class
                $index .= '::';
            }

            // Append the method/function to the string
            $index .= $member;
        }

        // service@class
        // service@method
        // service@class::method
        return $index;
    }

    /**
     * Setup the container.
     *
     * @return void
     *
     * @throws \Valkyrja\Container\Exceptions\InvalidServiceIdException
     *
     * @throws \Valkyrja\Container\Exceptions\InvalidContextException
     * @throws \Valkyrja\Container\Exceptions\InvalidServiceIdException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidClosureException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidDispatchCapabilityException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidFunctionException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidMethodException
     */
    public function setup(): void
    {
        // If the application should use the container cache files
        if ($this->app()->config()->container->useCacheFile) {
            // Set the application routes with said file
            $cache = require $this->app()->config()->container->cacheFilePath;

            self::$services = $cache['services'];
            self::$aliases = $cache['aliases'];

            // Then return out of routes setup
            return;
        }

        // Setup the bootstrap
        $this->setupBootstrap();

        // If annotations are enabled and the container should use annotations
        if ($this->app()->config()->container->useAnnotations && $this->app()->config()->annotations->enabled) {
            // Setup annotated services, contexts, and aliases
            $this->setupAnnotations();

            // If only annotations should be used
            if ($this->app()->config()->container->useAnnotationsExclusively) {
                // Finally setup service providers
                $this->setupServiceProviders();

                // Return to avoid loading container file
                return;
            }
        }

        // Include the container file
        // NOTE: Included if annotations are set or not due to possibility of container items being defined
        // within the classes as well as within the container file
        require $this->app()->config()->container->filePath;

        // Finally setup service providers
        $this->setupServiceProviders();
    }

    /**
     * Setup container bootstrapping.
     *
     * @return void
     */
    protected function setupBootstrap(): void
    {
        // Bootstrap the container
        new BootstrapContainer($this);
    }

    /**
     * Setup annotations.
     *
     * @return void
     *
     * @throws \Valkyrja\Container\Exceptions\InvalidContextException
     * @throws \Valkyrja\Container\Exceptions\InvalidServiceIdException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidClosureException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidDispatchCapabilityException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidFunctionException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidMethodException
     */
    protected function setupAnnotations(): void
    {
        /** @var ContainerAnnotations $containerAnnotations */
        $containerAnnotations = $this->get(ContainerAnnotations::class);

        // Get all the annotated services from the list of controllers
        $services = $containerAnnotations->getServices(...$this->app()->config()->routing->controllers);

        // Iterate through the services
        foreach ($services as $service) {
            // Set the service
            $this->bind($service);
        }

        // Get all the annotated services from the list of controllers
        $contextServices = $containerAnnotations->getContextServices(...$this->app()->config()->routing->controllers);

        // Iterate through the services
        foreach ($contextServices as $context) {
            // Set the service
            $this->context($context);
        }

        // Get all the annotated services from the list of controllers
        $aliasServices = $containerAnnotations->getContextServices(...$this->app()->config()->routing->controllers);

        // Iterate through the services
        foreach ($aliasServices as $alias) {
            // Set the service
            $this->alias($alias->getName(), $alias->getId());
        }
    }

    /**
     * Setup service providers.
     *
     * @return void
     */
    protected function setupServiceProviders(): void
    {
        // Iterate through all the providers
        foreach ($this->app()->config()->container->providers as $provider) {
            $this->register($provider);
        }

        // If this is not a dev environment
        if (! $this->app()->config()->app->debug) {
            return;
        }

        // Iterate through all the providers
        foreach ($this->app()->config()->container->devProviders as $provider) {
            $this->register($provider);
        }
    }

    /**
     * Get a cacheable representation of the service container.
     *
     * @return array
     */
    public function getCacheable(): array
    {
        return [
            'services' => self::$services,
            'aliases'  => self::$aliases,
        ];
    }

    /**
     * Get the app.
     *
     * @return \Valkyrja\Contracts\Application
     */
    public function app(): Application
    {
        return $this->get(Application::class);
    }
}
