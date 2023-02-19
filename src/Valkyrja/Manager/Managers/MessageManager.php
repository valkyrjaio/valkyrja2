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

use Valkyrja\Manager\Config\MessageConfig;
use Valkyrja\Manager\Driver;
use Valkyrja\Manager\Factory;
use Valkyrja\Manager\Message;
use Valkyrja\Manager\MessageFactory;
use Valkyrja\Manager\MessageManager as Contract;

/**
 * Class MessageManager.
 *
 * @author   Melech Mizrachi
 *
 * @template Driver
 * @template Factory
 * @template Message
 *
 * @implements Contract<Driver, Factory, Message>
 *
 * @property MessageFactory $factory
 */
abstract class MessageManager extends Manager implements Contract
{
    /**
     * The default message class.
     *
     * @var class-string<Message>
     */
    protected string $defaultMessageClass;

    /**
     * The messages config.
     *
     * @var array<string, array>
     */
    protected array $messages;

    /**
     * The default message.
     */
    protected string $defaultMessage;

    /**
     * MessageManager constructor.
     *
     * @param MessageFactory      $factory The factory
     * @param MessageConfig|array $config  The config
     */
    public function __construct(MessageFactory $factory, MessageConfig|array $config)
    {
        parent::__construct($factory, $config);

        $this->defaultMessage      = $config['defaultMessage'];
        $this->defaultMessageClass = $config['message'];
        $this->messages            = $config['messages'];
    }

    /**
     * @inheritDoc
     */
    public function createMessage(string $name = null, array $data = []): Message
    {
        // The name of the message to use
        $name ??= $this->defaultMessage;
        // The message config
        $config = $this->messages[$name];
        // The message to use
        $message = $config['message'] ?? $this->defaultMessageClass;

        return $this->factory->createMessage($message, $config, $data);
    }
}
