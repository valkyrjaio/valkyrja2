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

namespace Valkyrja\Support\Cacheable;

use Valkyrja\Config\Config;

/**
 * Trait Cacheable.
 *
 * @author   Melech Mizrachi
 *
 * @template Config
 * @template Cache
 */
trait Cacheable
{
    /**
     * Has setup already completed? Used to avoid duplicate setup.
     */
    protected static bool $setup = false;

    /**
     * Setup the service.
     *
     * @param bool $force    [optional] Whether to force setup
     * @param bool $useCache [optional] Whether to use cache
     */
    public function setup(bool $force = false, bool $useCache = true): void
    {
        // If route's have already been setup, no need to do it again
        if (self::$setup && ! $force) {
            return;
        }

        self::$setup = true;
        // The cacheable config
        $config = $this->getConfig();

        $this->beforeSetup($config);

        // If the application should use the routes cache file
        if ($useCache && $config['useCache']) {
            /** @var Config|array $config */
            $this->setupFromCache($config);

            // Then return out of setup
            return;
        }

        $this->setupNotCached($config);
        $this->setupFromAttributes($config);
        $this->setupFromAnnotations($config);
        $this->requireFilePath($config);
        $this->afterSetup($config);
    }

    /**
     * Get a cacheable representation of the data.
     *
     * @return Cache
     */
    abstract public function getCacheable(): Config;

    /**
     * Get the config.
     */
    abstract protected function getConfig(): Config|array;

    /**
     * Before setup.
     *
     * @param Config|array $config The config
     */
    abstract protected function beforeSetup(Config|array $config): void;

    /**
     * Setup from cache.
     *
     * @param Config|array $config The config
     */
    abstract protected function setupFromCache(Config|array $config): void;

    /**
     * Set not cached.
     *
     * @param Config|array $config The config
     */
    abstract protected function setupNotCached(Config|array $config): void;

    /**
     * Set annotations.
     */
    protected function setupFromAnnotations(Config|array $config): void
    {
        // If annotations are enabled and cacheable should use annotations
        if ($config['useAnnotations'] ?? true) {
            /** @var Config|array $config */
            $this->setupAnnotations($config);
        }
    }

    /**
     * Set annotations.
     *
     * @param Config|array $config The config
     */
    abstract protected function setupAnnotations(Config|array $config): void;

    /**
     * Set attributes.
     *
     * @param Config|array $config The config
     */
    protected function setupFromAttributes(Config|array $config): void
    {
        // If attributes are enabled and cacheable should use attributes
        if ($config['useAttributes'] ?? true) {
            /** @var Config|array $config */
            $this->setupAttributes($config);
        }
    }

    /**
     * Set attributes.
     *
     * @param Config|array $config The config
     */
    abstract protected function setupAttributes(Config|array $config): void;

    /**
     * Require the file path specified in the config.
     *
     * @param Config|array $config The config
     */
    protected function requireFilePath(Config|array $config): void
    {
        require $config['filePath'];
    }

    /**
     * After setup.
     *
     * @param Config|array $config The config
     */
    abstract protected function afterSetup(Config|array $config): void;
}
