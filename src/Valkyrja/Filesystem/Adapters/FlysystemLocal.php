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

namespace Valkyrja\Filesystem\Adapters;

use League\Flysystem\Adapter\Local;

use function Valkyrja\config;

/**
 * Abstract Class FlysystemLocal.
 *
 * @author Melech Mizrachi
 */
class FlysystemLocal extends FlysystemAdapter
{
    /**
     * Make a new adapter instance.
     *
     * @return static
     */
    public static function make(): self
    {
        return new static(
            new Local(config()['filesystem']['disks']['local']['dir'])
        );
    }
}
