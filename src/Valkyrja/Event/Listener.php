<?php

declare(strict_types=1);

/*
 * This file is part of the Valkyrja framework.
 *
 * (c) Melech Mizrachi
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
     * @return string
     */
    public function getEvent(): ?string;

    /**
     * Set the event.
     *
     * @param string $event The event
     *
     * @return static
     */
    public function setEvent(string $event = null): self;
}
