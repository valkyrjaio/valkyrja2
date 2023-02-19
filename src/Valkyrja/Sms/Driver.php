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

use Valkyrja\Manager\Driver as Contract;

/**
 * Interface Driver.
 *
 * @author Melech Mizrachi
 */
interface Driver extends Contract
{
    /**
     * Send a message.
     *
     * @param Message $message The message to send
     */
    public function send(Message $message): void;
}
