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

namespace Valkyrja\Support\Loader\Loaders;

use Valkyrja\Support\Loader\Loader;
use Valkyrja\Support\Manager\Adapter;
use Valkyrja\Support\Manager\Driver;

/**
 * Class SimpleLoader.
 *
 * @author Melech Mizrachi
 */
class SimpleLoader implements Loader
{
    /**
     * @inheritDoc
     */
    public function createDriver(string $name, string $adapter, array $config): Driver
    {
        return new $name(
            $this->createAdapter($adapter, $config)
        );
    }

    /**
     * @inheritDoc
     */
    public function createAdapter(string $name, array $config): Adapter
    {
        return new $name($config);
    }
}
