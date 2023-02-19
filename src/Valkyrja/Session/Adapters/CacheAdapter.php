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

namespace Valkyrja\Session\Adapters;

use JsonException;
use Valkyrja\Cache\Driver as Cache;
use Valkyrja\Session\Exceptions\SessionStartFailure;
use Valkyrja\Type\Support\Arr;

use function headers_sent;
use function session_start;

/**
 * Class CacheAdapter.
 *
 * @author Melech Mizrachi
 */
class CacheAdapter extends PHPAdapter
{
    /**
     * The cache.
     */
    protected Cache $cache;

    /**
     * CacheAdapter constructor.
     *
     * @param Cache       $cache       The cache
     * @param array       $config      The config
     * @param string|null $sessionId   [optional] The session id
     * @param string|null $sessionName [optional] The session name
     */
    public function __construct(Cache $cache, array $config, string $sessionId = null, string $sessionName = null)
    {
        parent::__construct($config, $sessionId, $sessionName);

        $this->cache = $cache;
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function start(): void
    {
        // If the session is already active
        if ($this->isActive() || headers_sent()) {
            // No need to reactivate
            return;
        }

        // If the session failed to start
        if (! session_start() || ! $cachedData = $this->cache->get($this->getCacheSessionId())) {
            // Throw a new exception
            throw new SessionStartFailure('The session failed to start!');
        }

        // Set the data
        $this->data = Arr::fromString($cachedData);
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function set(string $id, $value): void
    {
        $this->data[$id] = $value;

        $this->updateCacheSession();
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function remove(string $id): bool
    {
        if (! $this->has($id)) {
            return false;
        }

        unset($this->data[$id]);

        $this->updateCacheSession();

        return true;
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function clear(): void
    {
        parent::clear();

        $this->updateCacheSession();
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function destroy(): void
    {
        parent::destroy();

        $this->updateCacheSession();
    }

    /**
     * Get the cache session id.
     */
    protected function getCacheSessionId(): string
    {
        return $this->getId() . '_session';
    }

    /**
     * Update the cache session.
     *
     * @throws JsonException
     */
    protected function updateCacheSession(): void
    {
        $this->cache->forever($this->getCacheSessionId(), Arr::toString($this->data));
    }
}
