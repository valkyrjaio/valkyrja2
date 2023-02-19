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

use InvalidArgumentException;
use JsonException;
use Valkyrja\Manager\Driver as Contract;

/**
 * Interface Driver.
 *
 * @author Melech Mizrachi
 */
interface Driver extends Contract
{
    /**
     * Determine if a key/value pair exists and matches in a broadcast message.
     *  Message is automatically decoded into an array.
     *
     * @param string $key     The key to look for in the message
     * @param mixed  $value   The value to match (string || numeric)
     * @param string $message The message
     *
     * @throws InvalidArgumentException When $value is neither a string or numeric
     * @throws JsonException            If a malformed message is provided
     */
    public function determineKeyValueMatch(string $key, mixed $value, string $message): bool;

    /**
     * Send a message.
     *
     * @param Message $message The message to send
     */
    public function send(Message $message): void;
}
