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

namespace Valkyrja\Crypt;

use Valkyrja\Manager\Factory as Contract;

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
     * @param class-string<Driver>  $name    The driver
     * @param class-string<Adapter> $adapter The adapter
     */
    public function createDriver(string $name, string $adapter, array $config): Driver;

    /**
     * @inheritDoc
     *
     * @param class-string<Adapter> $name The adapter
     */
    public function createAdapter(string $name, array $config): Adapter;
}
