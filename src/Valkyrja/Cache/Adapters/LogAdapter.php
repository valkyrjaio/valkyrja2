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

use JsonException;
use Valkyrja\Cache\LogAdapter as Contract;
use Valkyrja\Cache\Tagger;
use Valkyrja\Cache\Taggers\Tagger as TagClass;
use Valkyrja\Log\Driver as Logger;
use Valkyrja\Type\Support\Arr;

/**
 * Class LogAdapter.
 *
 * @author Melech Mizrachi
 */
class LogAdapter implements Contract
{
    /**
     * The prefix to use for all keys.
     */
    protected string $prefix;

    /**
     * The logger.
     */
    protected Logger $logger;

    /**
     * LogAdapter constructor.
     *
     * @param Logger      $logger The logger service
     * @param string|null $prefix [optional] The prefix
     */
    public function __construct(Logger $logger, string $prefix = null)
    {
        $this->logger = $logger;
        $this->prefix = $prefix ?? '';
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        $this->logger->info(self::class . " has: $key");

        return true;
    }

    /**
     * @inheritDoc
     */
    public function get(string $key): ?string
    {
        $this->logger->info(self::class . " get: $key");

        return '';
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function many(string ...$keys): array
    {
        $keysString = Arr::toString($keys);

        $this->logger->info(self::class . " many: $keysString");

        return [];
    }

    /**
     * @inheritDoc
     */
    public function put(string $key, string $value, int $minutes): void
    {
        $this->logger->info(self::class . " put: $key, value $value, minutes $minutes");
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function putMany(array $values, int $minutes): void
    {
        $valuesString = Arr::toString($values);

        $this->logger->info(self::class . " putMany: $valuesString, minutes $minutes");
    }

    /**
     * @inheritDoc
     */
    public function increment(string $key, int $value = 1): int
    {
        $this->logger->info(self::class . " increment: $key, value $value");

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function decrement(string $key, int $value = 1): int
    {
        $this->logger->info(self::class . " decrement: $key, value $value");

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function forever(string $key, $value): void
    {
        $this->logger->info(self::class . " forever: $key, value $value");
    }

    /**
     * @inheritDoc
     */
    public function forget(string $key): bool
    {
        $this->logger->info(self::class . " forget: $key");

        return true;
    }

    /**
     * @inheritDoc
     */
    public function flush(): bool
    {
        $this->logger->info(self::class . ' flush');

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
