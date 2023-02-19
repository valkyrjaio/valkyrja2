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

use Valkyrja\Manager\Adapter;
use Valkyrja\Manager\Driver;
use Valkyrja\Manager\Message;
use Valkyrja\Manager\MessageFactory;
use Valkyrja\Type\Support\Cls;

/**
 * Class ContainerMessageFactory.
 *
 * @author   Melech Mizrachi
 *
 * @template Adapter
 * @template Driver
 * @template Message
 *
 * @implements MessageFactory<Adapter, Driver, Message>
 */
class ContainerMessageFactory extends ContainerFactory implements MessageFactory
{
    /**
     * The default driver class.
     */
    protected static string $defaultMessageClass;

    /**
     * @inheritDoc
     *
     * @param class-string<Message> $name The message
     */
    public function createMessage(string $name, array $config, array $data = []): Message
    {
        return Cls::getDefaultableService(
            $this->container,
            $name,
            $this->getMessageDefaultClass($name),
            [
                $config,
                $data,
            ]
        );
    }

    /**
     * Get the default message class.
     *
     * @param string $name The message
     */
    protected function getMessageDefaultClass(string $name): string
    {
        return static::$defaultMessageClass;
    }
}
