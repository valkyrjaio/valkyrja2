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

use Closure;
use Valkyrja\Model\Model;

/**
 * Interface Dispatch.
 *
 * @author Melech Mizrachi
 */
interface Dispatch extends Model
{
    /**
     * Get the id.
     */
    public function getId(): ?string;

    /**
     * Set the id.
     *
     * @param string|null $id The id
     */
    public function setId(string $id = null): static;

    /**
     * Get the name.
     */
    public function getName(): ?string;

    /**
     * Set the name.
     *
     * @param string|null $name The name
     */
    public function setName(string $name = null): static;

    /**
     * Get the class.
     *
     * @return class-string|null
     */
    public function getClass(): ?string;

    /**
     * Set the class.
     *
     * @param class-string|null $class The class
     */
    public function setClass(string $class = null): static;

    /**
     * Check whether this is a class dispatch.
     */
    public function isClass(): bool;

    /**
     * Get the property.
     *
     * @return non-empty-string|null
     */
    public function getProperty(): ?string;

    /**
     * Set the property.
     *
     * @param non-empty-string|null $property The property
     */
    public function setProperty(string $property = null): static;

    /**
     * Check whether this is a class/property dispatch.
     */
    public function isProperty(): bool;

    /**
     * Get the method.
     *
     * @return non-empty-string|null
     */
    public function getMethod(): ?string;

    /**
     * Set the method.
     *
     * @param non-empty-string|null $method The method
     */
    public function setMethod(string $method = null): static;

    /**
     * Check whether this is a class/method dispatch.
     */
    public function isMethod(): bool;

    /**
     * Get whether the member is static.
     */
    public function isStatic(): bool;

    /**
     * Set whether the member is static.
     *
     * @param bool $static Whether the member is static
     */
    public function setStatic(bool $static = true): static;

    /**
     * @return callable-string|null
     */
    public function getFunction(): ?string;

    /**
     * Set the function.
     *
     * @param callable-string|null $function The function
     */
    public function setFunction(string $function = null): static;

    /**
     * Check whether this is a function dispatch.
     */
    public function isFunction(): bool;

    /**
     * Get the closure.
     */
    public function getClosure(): ?Closure;

    /**
     * Set the closure.
     *
     * @param Closure|null $closure The closure
     */
    public function setClosure(Closure $closure = null): static;

    /**
     * Check whether this is a closure dispatch.
     */
    public function isClosure(): bool;

    /**
     * Get the matches.
     */
    public function getMatches(): ?array;

    /**
     * Set the matches.
     *
     * @param array|null $matches The matches
     */
    public function setMatches(array $matches = null): static;

    /**
     * Get the arguments.
     */
    public function getArguments(): ?array;

    /**
     * Set the arguments.
     *
     * @param array|null $arguments The arguments
     */
    public function setArguments(array $arguments = null): static;

    /**
     * Get the dependencies.
     */
    public function getDependencies(): ?array;

    /**
     * Set the dependencies.
     *
     * @param array|null $dependencies The dependencies
     */
    public function setDependencies(array $dependencies = null): static;
}
