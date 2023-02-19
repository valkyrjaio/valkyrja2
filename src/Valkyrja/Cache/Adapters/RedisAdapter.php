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

namespace Valkyrja\Cache\Adapters;

use Predis\Client;
use Valkyrja\Cache\RedisAdapter as Contract;
use Valkyrja\Cache\Tagger;
use Valkyrja\Cache\Taggers\Tagger as TagClass;

/**
 * Class RedisAdapter.
 *
 * @author Melech Mizrachi
 */
class RedisAdapter implements Contract
{
    /**
     * The prefix to use for all keys.
     */
    protected string $prefix;

    /**
     * The predis client.
     */
    protected Client $predis;

    /**
     * RedisAdapter constructor.
     *
     * @param Client      $client The predis client
     * @param string|null $prefix The prefix
     */
    public function __construct(Client $client, string $prefix = null)
    {
        $this->predis = $client;
        $this->prefix = $prefix ?? '';
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return (bool) $this->predis->exists($this->getKey($key));
    }

    /**
     * @inheritDoc
     */
    public function get(string $key): ?string
    {
        return $this->predis->get($this->getKey($key)) ?: null;
    }

    /**
     * @inheritDoc
     */
    public function many(string ...$keys): array
    {
        $prefixedKeys = [];

        foreach ($keys as $key) {
            $prefixedKeys[] = $this->getKey($key);
        }

        return $this->predis->mget($prefixedKeys);
    }

    /**
     * @inheritDoc
     */
    public function put(string $key, string $value, int $minutes): void
    {
        $this->predis->setex($this->getKey($key), $minutes * 60, $value);
    }

    /**
     * @inheritDoc
     */
    public function putMany(array $values, int $minutes): void
    {
        $seconds = $minutes * 60;

        $this->predis->transaction(
            function (Client $client) use ($values, $seconds) {
                /** @var Client $client */
                foreach ($values as $key => $value) {
                    $client->setex($this->getKey($key), $seconds, $value);
                }
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function increment(string $key, int $value = 1): int
    {
        return $this->predis->incrby($this->getKey($key), $value);
    }

    /**
     * @inheritDoc
     */
    public function decrement(string $key, int $value = 1): int
    {
        return $this->predis->decrby($this->getKey($key), $value);
    }

    /**
     * @inheritDoc
     */
    public function forever(string $key, $value): void
    {
        $this->predis->set($this->getKey($key), $value);
    }

    /**
     * @inheritDoc
     */
    public function forget(string $key): bool
    {
        return (bool) $this->predis->del([$this->getKey($key)]);
    }

    /**
     * @inheritDoc
     */
    public function flush(): bool
    {
        return (bool) $this->predis->flushdb();
    }

    /**
     * @inheritDoc
     */
    public function getPrefix(): string
    {
        return $this->prefix ?? '';
    }

    /**
     * @inheritDoc
     */
    public function getTagger(string ...$tags): Tagger
    {
        return TagClass::make($this, ...$tags);
    }

    /**
     * Get key.
     */
    protected function getKey(string $key): string
    {
        return $this->getPrefix() . $key;
    }
}
