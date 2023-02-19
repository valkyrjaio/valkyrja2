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

namespace Valkyrja\Config\Traits;

use Valkyrja\Config\Config;

/**
 * Trait Cacheable.
 *
 * @author Melech Mizrachi
 */
trait Cacheable
{
    /**
     * The cache from a Cacheable::getCacheable().
     */
    public ?Config $cache = null;

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
}
