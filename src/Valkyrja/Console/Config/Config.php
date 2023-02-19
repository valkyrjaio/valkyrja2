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

namespace Valkyrja\Console\Config;

use Valkyrja\Config\Config as Model;
use Valkyrja\Config\Constants\ConfigKeyPart as CKP;
use Valkyrja\Config\Constants\EnvKey;
use Valkyrja\Console\Commander;

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
        CKP::HANDLERS        => EnvKey::CONSOLE_HANDLERS,
        CKP::PROVIDERS       => EnvKey::CONSOLE_PROVIDERS,
        CKP::DEV_PROVIDERS   => EnvKey::CONSOLE_DEV_PROVIDERS,
        CKP::QUIET           => EnvKey::CONSOLE_QUIET,
        CKP::USE_ANNOTATIONS => EnvKey::CONSOLE_USE_ANNOTATIONS,
        CKP::FILE_PATH       => EnvKey::CONSOLE_FILE_PATH,
        CKP::CACHE_FILE_PATH => EnvKey::CONSOLE_CACHE_FILE_PATH,
        CKP::USE_CACHE       => EnvKey::CONSOLE_USE_CACHE_FILE,
    ];

    /**
     * The annotated command handlers.
     *
     * @var class-string<Commander>[]
     */
    public array $handlers;

    /**
     * The command providers.
     *
     * @var class-string[]
     */
    public array $providers;

    /**
     * The dev command providers.
     *
     * @var class-string[]
     */
    public array $devProviders;

    /**
     * Flag to enable quiet console (no output).
     */
    public bool $quiet;

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
