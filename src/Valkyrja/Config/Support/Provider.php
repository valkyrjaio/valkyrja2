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

namespace Valkyrja\Config\Support;

use Valkyrja\Config\Config\Config;

/**
 * Abstract Class Provider.
 *
 * @author Melech Mizrachi
 */
abstract class Provider
{
    /**
     * Publish the provider.
     *
     * @param Config $config The config
     */
    abstract public static function publish(Config $config): void;
}
