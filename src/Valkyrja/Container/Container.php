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

namespace Valkyrja\Container;

use ArrayAccess;
use Closure;
use Valkyrja\Support\Provider\ProvidersAware;

/**
 * Interface Container.
 *
 * @author Melech Mizrachi
 *
 * @extends ArrayAccess<string, mixed>
 */
interface Container extends ArrayAccess, ProvidersAware
{
    /**
     * Check whether a given service exists.
     *
     * @param class-string|string $serviceId The service id
     */
    public function has(string $serviceId): bool;

    /**
     * Bind a service to the container.
     *
     * @param class-string|string   $serviceId The service id
     * @param class-string<Service> $service   The service
     */
    public function bind(string $serviceId, string $service): static;

    /**
     * Bind an alias to the container.
     *
     * @param string              $alias     The alias
     * @param class-string|string $serviceId The service id to alias
     */
    public function bindAlias(string $alias, string $serviceId): static;

    /**
     * Bind a singleton to the container.
     *
     * @param class-string|string   $serviceId The service id
     * @param class-string<Service> $singleton The singleton service
     */
    public function bindSingleton(string $serviceId, string $singleton): static;

    /**
     * Set a closure in the container.
     *
     * @param class-string|string $serviceId The service id
     * @param Closure             $closure   The closure
     */
    public function setClosure(string $serviceId, Closure $closure): static;

    /**
     * Set a singleton in the container.
     *
     * @param class-string|string $serviceId The service id
     * @param mixed               $singleton The singleton
     */
    public function setSingleton(string $serviceId, mixed $singleton): static;

    /**
     * Check whether a given service is an alias.
     *
     * @param class-string|string $serviceId The service id
     */
    public function isAlias(string $serviceId): bool;

    /**
     * Check whether a given service is bound to a closure.
     *
     * @param class-string|string $serviceId The service id
     */
    public function isClosure(string $serviceId): bool;

    /**
     * Check whether a given service exists.
     *
     * @param class-string|string $serviceId The service id
     */
    public function isService(string $serviceId): bool;

    /**
     * Check whether a given service is a singleton.
     *
     * @param class-string|string $serviceId The service id
     */
    public function isSingleton(string $serviceId): bool;

    /**
     * Get a service from the container.
     *
     * @template T
     *
     * @param class-string<T>|string $serviceId The service id
     * @param array                  $arguments [optional] The arguments
     *
     * @return T|mixed
     */
    public function get(string $serviceId, array $arguments = []): mixed;

    /**
     * Get a service bound to a closure from the container.
     *
     * @template T
     *
     * @param class-string<T>|string $serviceId The service id
     * @param array                  $arguments [optional] The arguments
     *
     * @return T|mixed
     */
    public function getClosure(string $serviceId, array $arguments = []): mixed;

    /**
     * Get a service from the container.
     *
     * @param class-string<Service>|string $serviceId The service id
     * @param array                        $arguments [optional] The arguments
     */
    public function getService(string $serviceId, array $arguments = []): Service;

    /**
     * Get a singleton from the container.
     *
     * @template T
     *
     * @param class-string<T>|string $serviceId The service id
     *
     * @return T
     */
    public function getSingleton(string $serviceId): mixed;

    /**
     * Get a service from the container.
     *
     * @param string $offset The service id
     */
    public function offsetGet($offset): mixed;

    /**
     * Bind a service to the container.
     *
     * @param class-string|string   $offset The service id
     * @param class-string<Service> $value  The service
     */
    public function offsetSet($offset, $value): void;

    /**
     * Bind a service to the container.
     *
     * @param class-string|string $offset The service id
     */
    public function offsetUnset($offset): void;

    /**
     * Check whether a given service exists.
     *
     * @param class-string|string $offset The service id
     */
    public function offsetExists($offset): bool;
}
