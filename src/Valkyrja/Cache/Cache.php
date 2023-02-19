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

use Valkyrja\Manager\Manager;

/**
 * Interface Cache.
 *
 * @author Melech Mizrachi
 *
 * @extends Manager<Driver, Factory>
 */
interface Cache extends Manager
{
    /**
     * @inheritDoc
     */
    public function use(string $name = null): Driver;

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
     * Get the cache key prefix.
     */
    public function getPrefix(): string;

    /**
     * Get the tagger.
     */
    public function getTagger(string ...$tags): Tagger;
}
