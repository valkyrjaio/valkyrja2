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

namespace Valkyrja\Broadcast;

use Valkyrja\Manager\Message as Contract;

/**
 * Interface Message.
 *
 * @author Melech Mizrachi
 */
interface Message extends Contract
{
    /**
     * Get the channel to broadcast on.
     */
    public function getChannel(): string;

    /**
     * Set the channel to broadcast on.
     *
     * @param string $channel The channel
     */
    public function setChannel(string $channel): static;

    /**
     * Get the event to broadcast.
     */
    public function getEvent(): string;

    /**
     * Set the event to broadcast.
     *
     * @param string $event The event
     */
    public function setEvent(string $event): static;

    /**
     * Get the data to broadcast.
     */
    public function getData(): ?array;

    /**
     * Set the data to broadcast.
     *
     * @param array|null $data [optional] The data
     */
    public function setData(array $data = null): static;

    /**
     * Get the message to broadcast.
     */
    public function getMessage(): string;

    /**
     * Set the message to broadcast.
     *
     * @param string $message The message
     */
    public function setMessage(string $message): static;
}
