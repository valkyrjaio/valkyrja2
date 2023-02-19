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

namespace Valkyrja\Manager;

/**
 * Interface Factory.
 *
 * @author   Melech Mizrachi
 *
 * @template Adapter
 * @template Driver
 */
interface Factory
{
    /**
     * Get a driver by name.
     *
     * @param class-string<Driver>  $name    The driver
     * @param class-string<Adapter> $adapter The adapter
     * @param array                 $config  The config
     */
    public function createDriver(string $name, string $adapter, array $config): Driver;

    /**
     * Get an adapter by name.
     *
     * @param class-string<Adapter> $name   The adapter
     * @param array                 $config The config
     */
    public function createAdapter(string $name, array $config): Adapter;
}
