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

namespace Valkyrja\Event\Config;

use Valkyrja\Config\Config as Model;
use Valkyrja\Config\Constants\ConfigKeyPart as CKP;
use Valkyrja\Config\Constants\EnvKey;

use function is_array;

/**
 * Class Config.
 *
 * @author Melech Mizrachi
 */
class Config extends Model
{
    /**
     * @inheritDoc
     *
     * @var array<string, string>
     */
    protected static array $envKeys = [
        CKP::LISTENERS       => EnvKey::EVENT_LISTENERS,
        CKP::USE_ANNOTATIONS => EnvKey::EVENT_USE_ANNOTATIONS,
        CKP::FILE_PATH       => EnvKey::EVENT_FILE_PATH,
        CKP::CACHE_FILE_PATH => EnvKey::EVENT_CACHE_FILE_PATH,
        CKP::USE_CACHE       => EnvKey::EVENT_USE_CACHE_FILE,
    ];

    /**
     * The annotated listeners.
     *
     * @var class-string[]
     */
    public array $listeners;

    /**
     * The flag to enable annotations.
     */
    public bool $useAnnotations;

    /**
     * The cache from a Cacheable::getCacheable().
     */
    public Cache|null $cache = null;

    /**
     * The file path.
     */
    public string $filePath;

    /**
     * The cache file path.
     */
    public string $cacheFilePath;

    /**
     * The flag to enable cache.
     */
    public bool $useCache;

    /**
     * Set the cache.
     *
     * @param Cache|array|null $cache The cache
     */
    protected function setCache(Cache|array|null $cache): void
    {
        if (is_array($cache)) {
            $this->cache = Cache::fromArray($cache);

            return;
        }

        $this->cache = $cache;
    }
}
