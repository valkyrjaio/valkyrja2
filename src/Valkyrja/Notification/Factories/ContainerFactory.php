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

namespace Valkyrja\Notification\Factories;

use Valkyrja\Container\Container;
use Valkyrja\Notification\Factory;
use Valkyrja\Notification\Notification;

/**
 * Class ContainerFactory.
 *
 * @author Melech Mizrachi
 */
class ContainerFactory implements Factory
{
    /**
     * The container.
     */
    protected Container $container;

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
    public function createNotification(string $name, array $data = []): Notification
    {
        return $this->container->get(
            $this->config['notifications'][$name] ?? $name,
            $data
        );
    }
}
