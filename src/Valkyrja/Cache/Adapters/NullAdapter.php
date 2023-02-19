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

use Valkyrja\Cache\Adapter as Contract;
use Valkyrja\Cache\Tagger;
use Valkyrja\Cache\Taggers\Tagger as TagClass;

/**
 * Class NullAdapter.
 *
 * @author Melech Mizrachi
 */
class NullAdapter implements Contract
{
    /**
     * The prefix to use for all keys.
     */
    protected string $prefix;

    /**
     * NullAdapter constructor.
     *
     * @param string|null $prefix [optional] The prefix
     */
    public function __construct(string $prefix = null)
    {
        $this->prefix = $prefix ?? '';
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function get(string $key): ?string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function many(string ...$keys): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function put(string $key, string $value, int $minutes): void
    {
    }

    /**
     * @inheritDoc
     */
    public function putMany(array $values, int $minutes): void
    {
    }

    /**
     * @inheritDoc
     */
    public function increment(string $key, int $value = 1): int
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function decrement(string $key, int $value = 1): int
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function forever(string $key, $value): void
    {
    }

    /**
     * @inheritDoc
     */
    public function forget(string $key): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function flush(): bool
    {
        return true;
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
