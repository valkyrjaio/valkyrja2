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

namespace Valkyrja\Sms;

use Valkyrja\Manager\MessageManager as Manager;

/**
 * Interface Sms.
 *
 * @author Melech Mizrachi
 *
 * @extends Manager<Driver, Factory, Message>
 */
interface Sms extends Manager
{
    /**
     * @inheritDoc
     */
    public function use(string $name = null): Driver;

    /**
     * @inheritDoc
     */
    public function createMessage(string $name = null, array $data = []): Message;

    /**
     * Send a message.
     *
     * @param Message $message The message to send
     */
    public function send(Message $message): void;
}
