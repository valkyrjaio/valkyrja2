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

namespace Valkyrja\Sms\Adapters;

use Exception;
use Nexmo\Client as Nexmo;
use Valkyrja\Sms\Message;
use Valkyrja\Sms\NexmoAdapter as Contract;

/**
 * Class NexmoAdapter.
 *
 * @author Melech Mizrachi
 */
class NexmoAdapter implements Contract
{
    /**
     * The Nexmo client.
     */
    protected Nexmo $nexmo;

    /**
     * NexmoMessage constructor.
     *
     * @param Nexmo $nexmo The Nexmo client
     */
    public function __construct(Nexmo $nexmo)
    {
        $this->nexmo = $nexmo;
    }

    /**
     * @inheritDoc
     *
     * @throws Exception
     */
    public function send(Message $message): void
    {
        $this->nexmo->message()->send(
            [
                'to'   => $message->getTo(),
                'from' => $message->getFrom(),
                'text' => $message->getText(),
                'type' => $message->isUnicode() ? 'unicode' : null,
            ]
        );
    }
}
