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

namespace Valkyrja\Cache;

/**
 * Interface Tagger.
 *
 * @author Melech Mizrachi
 */
interface Tagger
{
    /**
     * Make a new Tag Store.
     */
    public static function make(Adapter $store, string ...$tags): static;

    /**
     * Determine if an item exists in the cache.
     */
    public function has(string $key): bool;

    /**
     * Retrieve an item from the cache by key.
     */
    public function get(string $key): ?string;

    /**
     * Retrieve multiple items from the cache by key.
     *
     * Items not found in the cache will have a null value.
     */
    public function many(string ...$keys): array;

    /**
     * Store an item in the cache for a given number of minutes.
     */
    public function put(string $key, string $value, int $minutes): void;

    /**
     * Store multiple items in the cache for a given number of minutes.
     *
     * <code>
     *      $store->putMany(
     *          [
     *              'key'  => 'value',
     *              'key2' => 'value2',
     *          ],
     *          5
     *      )
     * </code>
     */
    public function putMany(array $values, int $minutes): void;

    /**
     * Increment the value of an item in the cache.
     */
    public function increment(string $key, int $value = 1): int;

    /**
     * Decrement the value of an item in the cache.
     */
    public function decrement(string $key, int $value = 1): int;

    /**
     * Store an item in the cache indefinitely.
     */
    public function forever(string $key, string $value): void;

    /**
     * Remove an item from the cache.
     */
    public function forget(string $key): bool;

    /**
     * Remove all items from the cache.
     */
    public function flush(): bool;

    /**
     * Tag a key.
     */
    public function tag(string $key): static;

    /**
     * Untag a key.
     */
    public function untag(string $key): static;

    /**
     * Tag many keys.
     */
    public function tagMany(string ...$keys): static;

    /**
     * Untag many keys.
     */
    public function untagMany(string ...$keys): static;
}
