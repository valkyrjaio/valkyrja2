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

namespace Valkyrja\Cache\Taggers;

use JsonException;
use Valkyrja\Cache\Adapter;
use Valkyrja\Cache\Tagger as Contract;
use Valkyrja\Type\Support\Arr;

use function json_decode;

use const JSON_THROW_ON_ERROR;

/**
 * Class Cache.
 *
 * @author Melech Mizrachi
 */
class Tagger implements Contract
{
    /**
     * The cache store.
     */
    protected Adapter $adapter;

    /**
     * The tags.
     */
    protected array $tags;

    /**
     * Tag constructor.
     */
    public function __construct(Adapter $store, string ...$tags)
    {
        $this->adapter = $store;
        $this->tags    = $tags;
    }

    /**
     * @inheritDoc
     */
    public static function make(Adapter $store, string ...$tags): static
    {
        return new static($store, ...$tags);
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function has(string $key): bool
    {
        foreach ($this->tags as $tag) {
            if (isset($this->getKeys($tag)[$key]) && $this->adapter->has($key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function get(string $key): ?string
    {
        foreach ($this->tags as $tag) {
            if (isset($this->getKeys($tag)[$key])) {
                return $this->adapter->get($key);
            }
        }

        return null;
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function many(string ...$keys): array
    {
        $items = [];

        foreach ($this->tags as $tag) {
            $cachedKeys = $this->getKeys($tag);

            foreach ($keys as $key) {
                if (isset($cachedKeys[$key])) {
                    $items[] = $this->adapter->get($key);
                }
            }
        }

        return $items;
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function put(string $key, string $value, int $minutes): void
    {
        $this->tag($key);

        $this->adapter->put($key, $value, $minutes);
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function putMany(array $values, int $minutes): void
    {
        foreach ($values as $key => $value) {
            $this->put($key, $value, $minutes);
        }
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function increment(string $key, int $value = 1): int
    {
        $this->tag($key);

        return $this->adapter->increment($key, $value);
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function decrement(string $key, int $value = 1): int
    {
        $this->tag($key);

        return $this->adapter->decrement($key, $value);
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function forever(string $key, string $value): void
    {
        $this->tag($key);

        $this->adapter->forever($key, $value);
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function forget(string $key): bool
    {
        $this->untag($key);

        return $this->adapter->forget($key);
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function flush(): bool
    {
        foreach ($this->tags as $tag) {
            foreach ($this->getKeys($tag) as $key) {
                $this->adapter->forget($key);
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function tag(string $key): static
    {
        foreach ($this->tags as $tag) {
            $keys = $this->getKeys($tag);

            $keys[$key] = $key;

            $this->putKeys($tag, $keys);
        }

        return $this;
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function untag(string $key): static
    {
        foreach ($this->tags as $tag) {
            $keys = $this->getKeys($tag);

            unset($keys[$key]);

            $this->putKeys($tag, $keys);
        }

        return $this;
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function tagMany(string ...$keys): static
    {
        foreach ($keys as $key) {
            $this->tag($key);
        }

        return $this;
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function untagMany(string ...$keys): static
    {
        foreach ($keys as $key) {
            $this->untag($key);
        }

        return $this;
    }

    /**
     * Get a tag.
     *
     * @throws JsonException
     */
    protected function getKeys(string $tag): array
    {
        $keys = $this->adapter->get($tag);

        if ($keys) {
            return json_decode($keys, true, 512, JSON_THROW_ON_ERROR);
        }

        return [];
    }

    /**
     * Put a tag.
     *
     * @throws JsonException
     */
    protected function putKeys(string $tag, array $keys): void
    {
        $this->adapter->forever($tag, Arr::toString($keys));
    }
}
