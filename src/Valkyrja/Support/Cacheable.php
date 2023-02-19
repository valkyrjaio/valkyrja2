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

namespace Valkyrja\Support;

use Valkyrja\Config\Config;

/**
 * Interface Cacheable.
 *
 * @author   Melech Mizrachi
 *
 * @template Config
 */
interface Cacheable
{
    /**
     * Set the data from cache.
     *
     * @param bool $force    [optional] Whether to force setup
     * @param bool $useCache [optional] Whether to use cache
     */
    public function setup(bool $force = false, bool $useCache = true): void;

    /**
     * Get a cacheable representation of the data.
     */
    public function getCacheable(): Config;
}
