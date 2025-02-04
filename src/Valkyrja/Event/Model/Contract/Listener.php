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

namespace Valkyrja\Event\Model\Contract;

use Valkyrja\Dispatcher\Model\Contract\Dispatch;

/**
 * Interface Listener.
 *
 * @author Melech Mizrachi
 */
interface Listener extends Dispatch
{
    /**
     * Get the event.
     *
     * @return class-string
     */
    public function getEventId(): string;

    /**
     * Set the event.
     *
     * @param class-string $eventId The event
     *
     * @return static
     */
    public function setEventId(string $eventId): static;
}
