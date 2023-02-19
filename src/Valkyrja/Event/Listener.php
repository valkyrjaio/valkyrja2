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

namespace Valkyrja\Event;

use Valkyrja\Dispatcher\Dispatch;

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
     * @return class-string<Event>|string
     */
    public function getEvent(): string;

    /**
     * Set the event.
     *
     * @param class-string<Event>|string $event The event
     */
    public function setEvent(string $event): static;
}
