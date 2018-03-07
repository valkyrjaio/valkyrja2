<?php

/*
 * This file is part of the Valkyrja framework.
 *
 * (c) Melech Mizrachi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valkyrja\Events;

use Valkyrja\Application;
use Valkyrja\Events\Annotations\ListenerAnnotations;

/**
 * Class Events.
 *
 * @author Melech Mizrachi
 */
class NativeEvents implements Events
{
    /**
     * The application.
     *
     * @var \Valkyrja\Application
     */
    protected $app;

    /**
     * The event listeners.
     *
     * @var array
     */
    protected static $events = [];

    /**
     * Whether the container has been setup.
     *
     * @var bool
     */
    protected static $setup = false;

    /**
     * Events constructor.
     *
     * @param Application $application The application
     */
    public function __construct(Application $application)
    {
        $this->app = $application;
    }

    /**
     * Add an event listener.
     *
     * @param string   $event    The event
     * @param Listener $listener The event listener
     *
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidClosureException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidDispatchCapabilityException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidFunctionException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidMethodException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidPropertyException
     *
     * @return void
     */
    public function listen(string $event, Listener $listener): void
    {
        $this->add($event);

        $this->app->dispatcher()->verifyDispatch($listener);

        // If this listener has an id
        if (null !== $listener->getId()) {
            // Use it when setting to allow removal
            // or checking if it exists later
            self::$events[$event][$listener->getId()] = $listener;
        } else {
            // Otherwise set the listener normally
            self::$events[$event][] = $listener;
        }
    }

    /**
     * Add a listener to many events.
     *
     * @param Listener $listener  The listener
     * @param string[] ...$events The events
     *
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidClosureException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidDispatchCapabilityException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidFunctionException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidMethodException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidPropertyException
     *
     * @return void
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
     * Determine whether an event has a specified listener.
     *
     * @param string $event      The event
     * @param string $listenerId The event listener
     *
     * @return bool
     */
    public function hasListener(string $event, string $listenerId): bool
    {
        return $this->has($event) && isset(self::$events[$event][$listenerId]);
    }

    /**
     * Remove an event listener.
     *
     * @param string $event      The event
     * @param string $listenerId The event listener
     *
     * @return void
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
     * Get the event's listeners.
     *
     * @param string $event The event
     *
     * @return \Valkyrja\Events\Listener[]
     */
    public function getListeners(string $event): array
    {
        return $this->has($event)
            ? self::$events[$event]
            : [];
    }

    /**
     * Determine whether an event has listeners.
     *
     * @param string $event The event
     *
     * @return bool
     */
    public function hasListeners(string $event): bool
    {
        return $this->has($event) && ! empty(self::$events[$event]);
    }

    /**
     * Add a new event.
     *
     * @param string $event The event
     *
     * @return void
     */
    public function add(string $event): void
    {
        if (! $this->has($event)) {
            self::$events[$event] = [];
        }
    }

    /**
     * Determine whether an event exists.
     *
     * @param string $event The event
     *
     * @return bool
     */
    public function has(string $event): bool
    {
        return isset(self::$events[$event]);
    }

    /**
     * Remove an event.
     *
     * @param string $event The event
     *
     * @return void
     */
    public function remove(string $event): void
    {
        if ($this->has($event)) {
            unset(self::$events[$event]);
        }
    }

    /**
     * Trigger an event.
     *
     * @param string $event     The event
     * @param array  $arguments [optional] The arguments
     *
     * @return array
     */
    public function trigger(string $event, array $arguments = null): array
    {
        // The responses
        $responses = [];

        if (! $this->has($event) || ! $this->hasListeners($event)) {
            return $responses;
        }

        // Iterate through all the event's listeners
        foreach ($this->getListeners($event) as $listener) {
            // Attempt to dispatch the event listener using any one of the
            // callable options
            $dispatch = $this->app->dispatcher()->dispatchCallable(
                $listener,
                $arguments
            );

            if (null !== $dispatch) {
                $responses[] = $dispatch;
            }
        }

        return $responses;
    }

    /**
     * Trigger an event interface.
     *
     * @param Event $event The event
     *
     * @return array
     */
    public function event(Event $event): array
    {
        return $this->trigger(\get_class($event), [$event]);
    }

    /**
     * Get all events.
     *
     * @return array
     */
    public function all(): array
    {
        return self::$events;
    }

    /**
     * Set the events.
     *
     * @param array $events The events
     *
     * @return void
     */
    public function set(array $events): void
    {
        self::$events = $events;
    }

    /**
     * Setup the events.
     *
     * @param bool $force    [optional] Whether to force setup
     * @param bool $useCache [optional] Whether to use cache
     *
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidClosureException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidDispatchCapabilityException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidFunctionException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidMethodException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidPropertyException
     *
     * @return void
     */
    public function setup(bool $force = false, bool $useCache = true): void
    {
        if (self::$setup && ! $force) {
            return;
        }

        self::$setup = true;

        // If the application should use the events cache files
        if ($useCache && $this->app->config()['events']['useCache']) {
            $this->setupFromCache();

            // Then return out of setup
            return;
        }

        self::$events = [];

        // If annotations are enabled and the events should use annotations
        if (
            $this->app->config()['events']['useAnnotations']
            && $this->app->config()['annotations']['enabled']
        ) {
            // Setup annotated event listeners
            $this->setupAnnotations();

            // If only annotations should be used
            if ($this->app->config()['events']['useAnnotationsExclusively']) {
                // Return to avoid loading events file
                return;
            }
        }

        // Include the events file
        require $this->app->config()['events']['filePath'];
    }

    /**
     * Setup the events from cache.
     *
     * @return void
     */
    protected function setupFromCache(): void
    {
        // Set the application events with said file
        $cache = $this->app->config()['cache']['events']
            ?? require $this->app->config()['events']['cacheFilePath'];

        self::$events = unserialize(
            base64_decode($cache['events'], true),
            [
                'allowed_classes' => [
                    Listener::class,
                ],
            ]
        );
    }

    /**
     * Setup annotations.
     *
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidClosureException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidDispatchCapabilityException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidFunctionException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidMethodException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidPropertyException
     *
     * @return void
     */
    protected function setupAnnotations(): void
    {
        /** @var ListenerAnnotations $containerAnnotations */
        $containerAnnotations = $this->app->container()->getSingleton(
            ListenerAnnotations::class
        );

        // Get all the annotated listeners from the list of classes
        $listeners = $containerAnnotations->getListeners(
            ...$this->app->config()['events']['classes']
        );

        // Iterate through the listeners
        foreach ($listeners as $listener) {
            // Set the service
            $this->listen($listener->getEvent(), $listener);
        }
    }

    /**
     * Get a cacheable representation of the events.
     *
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidClosureException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidDispatchCapabilityException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidFunctionException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidMethodException
     * @throws \Valkyrja\Dispatcher\Exceptions\InvalidPropertyException
     *
     * @return array
     */
    public function getCacheable(): array
    {
        $this->setup(true, false);

        return [
            'events' => base64_encode(serialize(self::$events)),
        ];
    }
}
