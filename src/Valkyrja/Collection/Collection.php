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

namespace Valkyrja\Collection;

use JsonException;

/**
 * Interface Collection.
 *
 * @author   Melech Mizrachi
 *
 * @template T
 */
interface Collection
{
    /**
     * Set the collection.
     *
     * @param array $collection The collection
     */
    public function setAll(array $collection): static;

    /**
     * Determine if a value exists in the collection.
     *
     * @param mixed $value The value
     */
    public function exists(mixed $value): bool;

    /**
     * Get all the items in the collection.
     *
     * @return array<string|int, T>
     */
    public function all(): array;

    /**
     * Get all the keys in the collection.
     */
    public function keys(): array;

    /**
     * Get the total count of items in the collection.
     */
    public function count(): int;

    /**
     * Determine if the collection is empty.
     */
    public function isEmpty(): bool;

    /**
     * Get a single item from the collection.
     *
     * @param string|int $key The key
     *
     * @return mixed
     */
    public function __get(string|int $key);

    /**
     * Set a new item into the collection.
     *
     * @param string|int $key   The key
     * @param mixed      $value The value
     *
     * @return mixed
     */
    public function __set(string|int $key, mixed $value);

    /**
     * Get a single item from the collection.
     *
     * @param string|int $key     The key to get
     * @param mixed      $default [optional] The default value
     */
    public function get(string|int $key, mixed $default = null): mixed;

    /**
     * Determine if an item is in the collection.
     *
     * @param string|int $key The key
     */
    public function has(string|int $key): bool;

    /**
     * Set a new item into the collection.
     *
     * @param string|int $key   The key
     * @param mixed      $value The value
     */
    public function set(string|int $key, mixed $value): static;

    /**
     * Determine if an item is in the collection.
     *
     * @param string|int $key The key
     */
    public function __isset(string|int $key): bool;

    /**
     * Remove an item from the collection.
     *
     * @param string|int $key The key
     */
    public function __unset(string|int $key): void;

    /**
     * Remove an item from the collection.
     *
     * @param string|int $key The key
     */
    public function remove(string|int $key): static;

    /**
     * Convert the collection to a string.
     *
     * @throws JsonException
     */
    public function __toString(): string;
}
