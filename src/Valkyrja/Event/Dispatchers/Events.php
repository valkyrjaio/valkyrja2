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

namespace Valkyrja\Event\Dispatchers;

use JsonException;
use Valkyrja\Dispatcher\Dispatcher;
use Valkyrja\Event\Config\Config;
use Valkyrja\Event\Event;
use Valkyrja\Event\Events as Contract;
use Valkyrja\Event\Listener;
use Valkyrja\Event\Models\Listener as ListenerModel;

use function is_array;

/**
 * Class Events.
 *
 * @author Melech Mizrachi
 */
class Events implements Contract
{
    /**
     * The event listeners.
     *
     * @var Listener[][]
     */
    protected static array $events = [];

    /**
     * Events constructor.
     *
     * @param Dispatcher   $dispatcher The dispatcher
     * @param Config|array $config     The config
     */
    public function __construct(
        protected Dispatcher $dispatcher,
        protected Config|array $config
    ) {
    }

    /**
     * @inheritDoc
     */
    public function listen(string $event, Listener $listener): void
    {
        $this->add($event);

        // If this listener has an id
        if (($id = $listener->getId()) !== null) {
            // Use it when setting to allow removal
            // or checking if it exists later
            self::$events[$event][$id] = $listener;
        } else {
            // Otherwise set the listener normally
            self::$events[$event][] = $listener;
        }
    }

    /**
     * @inheritDoc
     */
    public function listenMany(Listener $listener, string ...$events): void
    {
        // Iterate through the events
        foreach ($events as $event) {
            // Set a new listener for the event
            $this->listen($event, $listener);
        }
    }

    /**
     * @inheritDoc
     */
    public function hasListener(string $event, string $listenerId): bool
    {
        return $this->has($event) && isset(self::$events[$event][$listenerId]);
    }

    /**
     * @inheritDoc
     */
    public function removeListener(string $event, string $listenerId): void
    {
        // If the listener exists
        if ($this->hasListener($event, $listenerId)) {
            // Unset it
            unset(self::$events[$event][$listenerId]);
        }
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function getListeners(string $event): array
    {
        return $this->has($event)
            ? $this->ensureListeners(self::$events[$event])
            : [];
    }

    /**
     * @inheritDoc
     */
    public function hasListeners(string $event): bool
    {
        return $this->has($event) && ! empty(self::$events[$event]);
    }

    /**
     * @inheritDoc
     */
    public function add(string $event): void
    {
        if (! $this->has($event)) {
            self::$events[$event] = [];
        }
    }

    /**
     * @inheritDoc
     */
    public function has(string $event): bool
    {
        return isset(self::$events[$event]);
    }

    /**
     * @inheritDoc
     */
    public function remove(string $event): void
    {
        if ($this->has($event)) {
            unset(self::$events[$event]);
        }
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function trigger(string $event, array $arguments = null): array
    {
        // The responses
        $responses = [];

        // Ensure the event exists and that it has listeners
        if (! $this->hasListeners($event)) {
            return $responses;
        }

        // If there are arguments and the event is a class, override the arguments with a new instance of the event
        // class with the arguments as parameters
        if ($arguments !== null && is_a($event, Event::class, true)) {
            $arguments = [new $event(...$arguments)];
        }

        // Iterate through all the event's listeners
        foreach (self::$events[$event] as $listener) {
            // Attempt to dispatch the event listener using any one of the callable options
            $dispatch = $this->dispatcher->dispatch($this->ensureListener($listener), $arguments);

            if ($dispatch !== null) {
                $responses[] = $dispatch;
            }
        }

        return $responses;
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function event(Event $event): array
    {
        return $this->trigger($event::class, [$event]);
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function all(): array
    {
        return $this->ensureEventListeners(self::$events);
    }

    /**
     * @inheritDoc
     */
    public function setEvents(array $events): void
    {
        self::$events = $events;
    }

    /**
     * Ensure events are arrays of listeners.
     *
     * @throws JsonException
     */
    protected function ensureEventListeners(array $eventsArray): array
    {
        $events = [];

        foreach ($eventsArray as $key => $method) {
            $eventsArray[$key] = $this->ensureListeners($method);
        }

        return $events;
    }

    /**
     * Ensure an array is an array of listeners.
     *
     * @param array $listenersArray The listeners array
     *
     * @throws JsonException
     */
    protected function ensureListeners(array $listenersArray): array
    {
        $listeners = [];

        foreach ($listenersArray as $key => $listener) {
            $listeners[$key] = $this->ensureListener($listener);
        }

        return $listeners;
    }

    /**
     * Ensure a listener, or null, is returned.
     *
     * @param Listener|array $listener The listener
     */
    protected function ensureListener(Listener|array $listener): Listener
    {
        if (is_array($listener)) {
            return ListenerModel::fromArray($listener);
        }

        return $listener;
    }
}
