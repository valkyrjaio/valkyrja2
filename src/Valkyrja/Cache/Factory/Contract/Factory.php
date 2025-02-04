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

namespace Valkyrja\Cache\Factory\Contract;

use Valkyrja\Cache\Adapter\Contract\Adapter;
use Valkyrja\Cache\Driver\Contract\Driver;
use Valkyrja\Manager\Factory\Contract\Factory as Contract;

/**
 * Interface Factory.
 *
 * @author Melech Mizrachi
 *
 * @extends Contract<Adapter, Driver>
 */
interface Factory extends Contract
{
    /**
     * @inheritDoc
     *
     * @param class-string<Driver>  $name
     * @param class-string<Adapter> $adapter
     *
     * @return Driver
     */
    public function createDriver(string $name, string $adapter, array $config): Driver;

    /**
     * @inheritDoc
     *
     * @param class-string<Adapter> $name
     *
     * @return Adapter
     */
    public function createAdapter(string $name, array $config): Adapter;
}
