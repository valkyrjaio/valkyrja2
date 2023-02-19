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

namespace Valkyrja\Tests\Unit\Support;

use Valkyrja\Config\Config\Config;
use Valkyrja\Config\Support\Provider;

/**
 * Class to use to test the provider.
 *
 * @author Melech Mizrachi
 */
class ProviderClass extends Provider
{
    /**
     * The items provided by this provider.
     */
    public static function provides(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public static function publish(Config $config): void
    {
    }
}
