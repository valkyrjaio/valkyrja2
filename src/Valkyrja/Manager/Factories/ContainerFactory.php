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

namespace Valkyrja\Manager\Factories;

use Valkyrja\Container\Container;
use Valkyrja\Manager\Adapter;
use Valkyrja\Manager\Driver;
use Valkyrja\Manager\Factory;
use Valkyrja\Type\Support\Cls;

/**
 * Class ContainerFactory.
 *
 * @author Melech Mizrachi
 *
 * @implements Factory<Adapter, Driver>
 */
class ContainerFactory implements Factory
{
    /**
     * The container.
     */
    protected Container $container;

    /**
     * The default driver class.
     */
    protected static string $defaultDriverClass;

    /**
     * The default adapter class.
     */
    protected static string $defaultAdapterClass;

    /**
     * ContainerFactory constructor.
     *
     * @param Container $container The container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function createDriver(string $name, string $adapter, array $config): Driver
    {
        return Cls::getDefaultableService(
            $this->container,
            $name,
            $this->getDriverDefaultClass($name),
            [
                $this->createAdapter($adapter, $config),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function createAdapter(string $name, array $config): Adapter
    {
        return Cls::getDefaultableService(
            $this->container,
            $name,
            $this->getAdapterDefaultClass($name),
            [
                $config,
            ]
        );
    }

    /**
     * Get the default driver class.
     *
     * @param class-string $name The driver
     */
    protected function getDriverDefaultClass(string $name): string
    {
        return static::$defaultDriverClass;
    }

    /**
     * Get the default adapter class.
     *
     * @param class-string $name The adapter
     */
    protected function getAdapterDefaultClass(string $name): string
    {
        return static::$defaultAdapterClass;
    }
}
