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

namespace Valkyrja\Manager\Managers;

use Valkyrja\Manager\Adapter;
use Valkyrja\Manager\Config\Config;
use Valkyrja\Manager\Driver;
use Valkyrja\Manager\Factory;
use Valkyrja\Manager\Manager as Contract;

/**
 * Class Manager.
 *
 * @author Melech Mizrachi
 *
 * @implements Contract<Driver, Factory>
 */
abstract class Manager implements Contract
{
    /**
     * The drivers.
     *
     * @var Driver[]
     */
    protected static array $drivers = [];

    /**
     * The default adapter.
     *
     * @var class-string<Adapter>
     */
    protected string $defaultAdapter;

    /**
     * The default driver.
     *
     * @var class-string<Driver>
     */
    protected string $defaultDriver;

    /**
     * The default configuration.
     */
    protected string $defaultConfiguration;

    /**
     * The configurations.
     *
     * @var array<string, array>
     */
    protected array $configurations;

    /**
     * Manager constructor.
     *
     * @param Factory      $factory The factory
     * @param Config|array $config  The config
     */
    public function __construct(
        protected Factory $factory,
        protected Config|array $config
    ) {
        $this->defaultConfiguration = $config['default'];
        $this->defaultAdapter       = $config['adapter'];
        $this->defaultDriver        = $config['driver'];
        $this->configurations       = $config['configurations'] ?? [];
    }

    /**
     * @inheritDoc
     */
    public function use(string $name = null): Driver
    {
        // The configuration name to use
        $name ??= $this->defaultConfiguration;
        // The config to use
        $config = $this->configurations[$name];
        // The driver to use
        $driver = $config['driver'] ?? $this->defaultDriver;
        // The adapter to use
        $adapter = $config['adapter'] ?? $this->defaultAdapter;
        // The cache key to use
        $cacheKey = $name . $adapter;

        return self::$drivers[$cacheKey]
            ??= $this->factory->createDriver($driver, $adapter, $config);
    }

    /**
     * Get the loader.
     */
    public function getFactory(): Factory
    {
        return $this->factory;
    }
}
