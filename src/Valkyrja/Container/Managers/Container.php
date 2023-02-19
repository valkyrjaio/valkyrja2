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

namespace Valkyrja\Container\Managers;

use Closure;
use RuntimeException;
use Valkyrja\Container\Config\Config;
use Valkyrja\Container\Container as Contract;
use Valkyrja\Container\Service;
use Valkyrja\Support\Provider\Traits\ProvidersAwareTrait;

use function assert;

/**
 * Class Container.
 *
 * @author Melech Mizrachi
 */
class Container implements Contract
{
    use ProvidersAwareTrait;

    /**
     * The aliases.
     *
     * @var array<class-string<Service>|string, class-string<Service>|string>
     */
    protected static array $aliases = [];

    /**
     * The instances.
     *
     * @var array<class-string<Service>|string, mixed>
     */
    protected static array $instances = [];

    /**
     * The services.
     *
     * @var array<class-string<Service>|string, class-string<Service>>
     */
    protected static array $services = [];

    /**
     * The service closures.
     *
     * @var array<class-string<Service>|string, Closure>
     */
    protected static array $closures = [];

    /**
     * The singletons.
     *
     * @var array<class-string<Service>|string, class-string<Service>|string>
     */
    protected static array $singletons = [];

    /**
     * The context class or function name.
     *
     * @var class-string|string|null
     */
    protected ?string $context = null;

    /**
     * The context id.
     */
    protected ?string $contextId = null;

    /**
     * Container constructor.
     */
    public function __construct(
        protected Config|array $config,
        protected bool $debug = false
    ) {
    }

    /**
     * @inheritDoc
     */
    public function has(string $serviceId): bool
    {
        $serviceId = $this->getServiceIdInternal($serviceId);

        return $this->isProvided($serviceId)
            || $this->isSingletonInternal($serviceId)
            || $this->isServiceInternal($serviceId)
            || $this->isClosureInternal($serviceId)
            || $this->isAliasInternal($serviceId);
    }

    /**
     * @inheritDoc
     */
    public function bind(string $serviceId, string $service): static
    {
        assert(is_a($service, Service::class, true));

        $serviceId = $this->getServiceIdInternal($serviceId);

        self::$services[$serviceId] = $service;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function bindAlias(string $alias, string $serviceId): static
    {
        $serviceId = $this->getServiceIdInternal($serviceId);

        self::$aliases[$alias] = $serviceId;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function bindSingleton(string $serviceId, string $singleton): static
    {
        $serviceId = $this->getServiceIdInternal($serviceId);

        self::$singletons[$serviceId] = $singleton;

        $this->bind($singleton, $singleton);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setClosure(string $serviceId, Closure $closure): static
    {
        $serviceId = $this->getServiceIdInternal($serviceId);

        self::$closures[$serviceId]  = $closure;
        self::$published[$serviceId] = true;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSingleton(string $serviceId, mixed $singleton): static
    {
        $serviceId = $this->getServiceIdInternal($serviceId);

        self::$singletons[$serviceId] = $serviceId;
        self::$instances[$serviceId]  = $singleton;
        self::$published[$serviceId]  = true;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isAlias(string $serviceId): bool
    {
        return $this->isAliasInternal($serviceId);
    }

    /**
     * @inheritDoc
     */
    public function isClosure(string $serviceId): bool
    {
        $serviceId = $this->getServiceIdInternal($serviceId);

        return $this->isClosureInternal($serviceId);
    }

    /**
     * @inheritDoc
     */
    public function isService(string $serviceId): bool
    {
        $serviceId = $this->getServiceIdInternal($serviceId);

        return $this->isServiceInternal($serviceId);
    }

    /**
     * @inheritDoc
     */
    public function isSingleton(string $serviceId): bool
    {
        $serviceId = $this->getServiceIdInternal($serviceId);

        return $this->isSingletonInternal($serviceId);
    }

    /**
     * @inheritDoc
     */
    public function get(string $serviceId, array $arguments = []): mixed
    {
        $serviceId = $this->getServiceIdAndEnsurePublished($serviceId);

        // If the service is a singleton
        if ($this->isSingletonInternal($serviceId)) {
            // Return the singleton
            return $this->getSingletonWithoutChecks($serviceId);
        }

        // If the service is a singleton
        if ($this->isClosureInternal($serviceId)) {
            // Return the closure
            return $this->getClosureWithoutChecks($serviceId, $arguments);
        }

        // If the service is in the container
        if ($this->isServiceInternal($serviceId)) {
            // Return the made service
            return $this->getServiceWithoutChecks($serviceId, $arguments);
        }

        // Return a new object with the arguments
        return new $serviceId(...$arguments);
    }

    /**
     * @inheritDoc
     */
    public function getClosure(string $serviceId, array $arguments = []): mixed
    {
        $serviceId = $this->getServiceIdAndEnsurePublished($serviceId);

        return $this->getClosureWithoutChecks($serviceId, $arguments);
    }

    /**
     * @inheritDoc
     */
    public function getService(string $serviceId, array $arguments = []): Service
    {
        $serviceId = $this->getServiceIdAndEnsurePublished($serviceId);

        return $this->getServiceWithoutChecks($serviceId, $arguments);
    }

    /**
     * @inheritDoc
     */
    public function getSingleton(string $serviceId): mixed
    {
        $serviceId = $this->getServiceIdAndEnsurePublished($serviceId);

        return $this->getSingletonWithoutChecks($serviceId);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        $this->bind($offset, $value);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        throw new RuntimeException("Cannot remove service with name $offset from the container.");
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * Get an aliased service id if it exists.
     *
     * @param class-string|string $serviceId The service id
     */
    protected function getAliasedServiceId(string $serviceId): string
    {
        return self::$aliases[$serviceId] ?? $serviceId;
    }

    /**
     * Get a service id and ensure that it is published if it is provided.
     *
     * @param class-string|string $serviceId The service id
     */
    protected function getServiceIdAndEnsurePublished(string $serviceId): string
    {
        // Get an aliased service id if it exists
        $serviceId = $this->getServiceIdInternal($serviceId);

        $this->publishUnpublishedProvided($serviceId);

        return $serviceId;
    }

    /**
     * Get the context service id.
     *
     * @param class-string|string $serviceId The service id
     */
    protected function getServiceIdInternal(string $serviceId): string
    {
        $serviceId = $this->getAliasedServiceId($serviceId);

        if ($this->context === null) {
            return $serviceId;
        }

        // serviceId@context
        // serviceId@context::method
        return $serviceId . ($this->contextId ?? '');
    }

    /**
     * Check whether a given service is an alias.
     *
     * @param class-string|string $serviceId The service id
     */
    protected function isAliasInternal(string $serviceId): bool
    {
        return isset(self::$aliases[$serviceId]);
    }

    /**
     * Check whether a given service is bound to a closure.
     *
     * @param class-string|string $serviceId The service id
     */
    protected function isClosureInternal(string $serviceId): bool
    {
        return isset(self::$closures[$serviceId]);
    }

    /**
     * Check whether a given service is a singleton.
     *
     * @param class-string|string $serviceId The service id
     */
    protected function isSingletonInternal(string $serviceId): bool
    {
        return isset(self::$singletons[$serviceId]);
    }

    /**
     * Check whether a given service exists.
     *
     * @param class-string|string $serviceId The service id
     */
    protected function isServiceInternal(string $serviceId): bool
    {
        return isset(self::$services[$serviceId]);
    }

    /**
     * Get a service bound to a closure from the container without trying to get an alias or ensuring published.
     *
     * @param class-string|string $serviceId The service id
     * @param array               $arguments [optional] The arguments
     */
    protected function getClosureWithoutChecks(string $serviceId, array $arguments = []): mixed
    {
        $closure = self::$closures[$serviceId];

        return $closure(...$arguments);
    }

    /**
     * Get a singleton from the container without trying to get an alias or ensuring published.
     *
     * @param class-string|string $serviceId The service id
     */
    protected function getSingletonWithoutChecks(string $serviceId): mixed
    {
        /** @var mixed $instance */
        $instance = self::$instances[$serviceId] ??= $this->getServiceWithoutChecks($serviceId);

        return $instance;
    }

    /**
     * Get a service from the container without trying to get an alias or ensuring published.
     *
     * @param class-string<Service>|string $serviceId The service id
     * @param array                        $arguments [optional] The arguments
     */
    protected function getServiceWithoutChecks(string $serviceId, array $arguments = []): Service
    {
        /** @var Service $service */
        $service = self::$services[$serviceId];
        // Make the object by dispatching the service
        $made = $service::make($this, $arguments);

        // If the service is a singleton
        if ($this->isSingleton($serviceId)) {
            // Set singleton
            $this->setSingleton($serviceId, $made);
        }

        return $made;
    }
}
