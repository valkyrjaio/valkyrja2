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

namespace Valkyrja\Application\Applications;

use Valkyrja\Application\Application;
use Valkyrja\Application\Helpers\ApplicationHelpersTrait;
use Valkyrja\Config\Config\Config;
use Valkyrja\Config\Enums\ConfigKeyPart;
use Valkyrja\Config\Enums\EnvKey;
use Valkyrja\Config\Config as ConfigModel;
use Valkyrja\Container\Container;
use Valkyrja\Dispatcher\Dispatcher;
use Valkyrja\Event\Events;
use Valkyrja\Exception\ExceptionHandler;
use Valkyrja\Support\Directory;

use function constant;
use function date_default_timezone_set;
use function define;
use function defined;
use function explode;
use function is_file;
use function microtime;

use const E_ALL;

/**
 * Class Valkyrja.
 *
 * @author Melech Mizrachi
 */
class Valkyrja implements Application
{
    use ApplicationHelpersTrait;

    /**
     * Get the instance of the application.
     *
     * @var Application
     */
    protected static Application $app;

    /**
     * Application env.
     *
     * @var string|null
     */
    protected static ?string $env = null;

    /**
     * Application config.
     *
     * @var Config|array
     */
    protected static $config;

    /**
     * Get the instance of the container.
     *
     * @var Container
     */
    protected static Container $container;

    /**
     * Get the instance of the dispatcher.
     *
     * @var Dispatcher
     */
    protected static Dispatcher $dispatcher;

    /**
     * Get the instance of the events.
     *
     * @var Events
     */
    protected static Events $events;

    /**
     * Get the instance of the exception handler.
     *
     * @var ExceptionHandler
     */
    protected static ExceptionHandler $exceptionHandler;

    /**
     * Whether the application was setup.
     *
     * @var bool
     */
    protected static bool $setup = false;

    /**
     * Application constructor.
     *
     * @param string|null $config [optional] The config class to use
     */
    public function __construct(string $config = null)
    {
        $this->setup($config);
    }

    /**
     * Setup the application.
     *
     * @param string|null $config [optional] The config to use
     * @param bool        $force  [optional] Whether to force a setup
     *
     * @return void
     */
    public function setup(string $config = null, bool $force = false): void
    {
        // If the application was already setup, no need to do it again
        if (self::$setup && ! $force) {
            return;
        }

        // Avoid re-setting up the app later
        self::$setup = true;
        // Set the app static
        self::$app = $this;

        // If the VALKYRJA_START constant hasn't already been set
        if (! defined('VALKYRJA_START')) {
            // Set a global constant for when the framework started
            define('VALKYRJA_START', microtime(true));
        }

        // Bootstrap debug capabilities
        $this->bootstrapConfig($config);
    }

    /**
     * Add to the global config array.
     *
     * @param Config $config The config to add
     *
     * @return static
     */
    public function withConfig(Config $config): self
    {
        self::$config = $config;

        // Publish config providers
        $this->publishConfigProviders();
        $this->bootstrapAfterConfig();

        return $this;
    }

    /**
     * Get the application instance.
     *
     * @return Application
     */
    public static function app(): Application
    {
        return self::$app;
    }

    /**
     * Get an environment variable.
     *
     * @param string $key     [optional] The variable to get
     * @param mixed  $default [optional] The default value to return
     *
     * @return mixed
     */
    public static function env(string $key = null, $default = null)
    {
        $env = self::$env;

        if (null === $env) {
            return $default;
        }

        // If there was no variable requested
        if (null === $key) {
            // Return the env class
            return $env;
        }

        // If the env has this variable defined and the variable isn't null
        if (defined($env . '::' . $key)) {
            // Return the variable
            return constant($env . '::' . $key) ?? $default;
        }

        // Otherwise return the default
        return $default;
    }

    /**
     * Get the environment variables class.
     *
     * @return string|null
     */
    public static function getEnv(): ?string
    {
        return self::$env;
    }

    /**
     * Set the environment variables class.
     *
     * @param string $env [optional] The env file to use
     *
     * @return void
     */
    public static function setEnv(string $env = null): void
    {
        // Set the env class to use
        self::$env = $env;
    }

    /**
     * Get the config.
     *
     * @param string $key     [optional] The key to get
     * @param mixed  $default [optional] The default value if the key is not found
     *
     * @return mixed|Config|null
     */
    public function config(string $key = null, $default = null)
    {
        // Set the config to return
        $config = self::$config;

        // If no key was specified
        if (null === $key) {
            // Return all the entire config
            return $config;
        }

        // Explode the keys on period and iterate through the keys
        foreach (explode(ConfigKeyPart::SEP, $key) as $configItem) {
            // Trying to get the item from the config or set the default
            $config = $config[$configItem] ?? $default;

            // If the item was not found, might as well return out from here
            // instead of continuing to iterate through the remaining keys
            if ($default === $config) {
                return $default;
            }
        }

        // Return the found config
        return $config;
    }

    /**
     * Add to the global config array.
     *
     * @param ConfigModel $newConfig The new config to add
     * @param string      $key       The key to use
     *
     * @return void
     */
    public function addConfig(ConfigModel $newConfig, string $key): void
    {
        // Set the config within the application
        self::$config[$key] = $newConfig;
    }

    /**
     * Get the container instance.
     *
     * @return Container
     */
    public function container(): Container
    {
        return self::$container;
    }

    /**
     * Get the dispatcher instance.
     *
     * @return Dispatcher
     */
    public function dispatcher(): Dispatcher
    {
        return self::$dispatcher;
    }

    /**
     * Get the events instance.
     *
     * @return Events
     */
    public function events(): Events
    {
        return self::$events;
    }

    /**
     * Get the exception handler instance.
     *
     * @return ExceptionHandler
     */
    public function exceptionHandler(): ExceptionHandler
    {
        return self::$exceptionHandler;
    }

    /**
     * Bootstrap the config.
     *
     * @param string|null $config [optional] The config class to use
     *
     * @return void
     */
    protected function bootstrapConfig(string $config = null): void
    {
        // Get the cache file
        $cacheFilePath = $this->getCacheFilePath();

        // If we should use the config cache file
        if (is_file($cacheFilePath)) {
            // Get the config from the cache file's contents
            $this->setupFromCacheFile($cacheFilePath);

            return;
        }

        $config ??= self::env(EnvKey::CONFIG_CLASS, Config::class);

        $this->withConfig(new $config());
    }

    /**
     * Get cache file path.
     *
     * @return string
     */
    protected function getCacheFilePath(): string
    {
        $envCacheFile  = self::env(EnvKey::CONFIG_CACHE_FILE_PATH);
        $cacheFilePath = Directory::cachePath('config.php');

        // If an env variable for cache file path was set
        if (null !== $envCacheFile) {
            $envCacheFilePath = Directory::basePath((string) self::env(EnvKey::CONFIG_CACHE_FILE_PATH));
            $cacheFilePath    = is_file($envCacheFilePath) ? $envCacheFilePath : $cacheFilePath;
        }

        return $cacheFilePath;
    }

    /**
     * Setup the application from a cache file.
     *
     * @param string $cacheFilePath The cache file path
     *
     * @return void
     */
    protected function setupFromCacheFile(string $cacheFilePath): void
    {
        self::$config = require $cacheFilePath;

        $this->bootstrapAfterConfig();
    }

    /**
     * Publish config providers.
     *
     * @return void
     */
    protected function publishConfigProviders(): void
    {
        foreach (self::$config->providers as $provider) {
            // Config providers are NOT deferred and will not follow the deferred value
            $provider::publish($this);
        }
    }

    /**
     * Run bootstraps after config bootstrap.
     *
     * @return void
     */
    protected function bootstrapAfterConfig(): void
    {
        // Bootstrap debug capabilities
        $this->bootstrapExceptionHandler();
        // Bootstrap core functionality
        $this->bootstrapCore();
        // Bootstrap the container
        $this->bootstrapContainer();
        // Bootstrap the timezone
        $this->bootstrapTimezone();
    }

    /**
     * Bootstrap debug capabilities.
     *
     * @return void
     */
    protected function bootstrapExceptionHandler(): void
    {
        // If debug is on, enable debug handling
        if ($this->debug()) {
            // The exception handler class to use from the config
            $exceptionHandlerImpl = self::$config->app->exceptionHandler;
            // Set the exception handler to a new instance of the exception handler implementation
            self::$exceptionHandler = new $exceptionHandlerImpl($this);

            // Enable exception handling
            self::$exceptionHandler::enable(E_ALL, true);
        }
    }

    /**
     * Bootstrap core functionality.
     *
     * @return void
     */
    protected function bootstrapCore(): void
    {
        // The events class to use from the config
        $eventsImpl = self::$config['app']['events'];
        // The container class to use from the config
        $containerImpl = self::$config['app']['container'];
        // The dispatcher class to use from the config
        $dispatcherImpl = self::$config['app']['dispatcher'];

        // Set the container to a new instance of the container implementation
        self::$container = new $containerImpl((array) self::$config['container'], $this->debug());
        // Set the dispatcher to a new instance of the dispatcher implementation
        self::$dispatcher = new $dispatcherImpl(self::$container);
        // Set the events to a new instance of the events implementation
        self::$events = new $eventsImpl(self::$container, self::$dispatcher, (array) self::$config['event']);
    }

    /**
     * Bootstrap the container.
     *
     * @return void
     */
    protected function bootstrapContainer(): void
    {
        // Set the application instance in the container
        self::$container->setSingleton(Application::class, $this);
        // Set the events instance in the container
        self::$container->setSingleton('env', self::$env);
        // Set the events instance in the container
        self::$container->setSingleton('config', self::$config);
        // Set the container instance in the container
        self::$container->setSingleton(Container::class, self::$container);
        // Set the dispatcher instance in the dispatcher
        self::$container->setSingleton(Dispatcher::class, self::$dispatcher);
        // Set the events instance in the container
        self::$container->setSingleton(Events::class, self::$events);

        if ($this->debug()) {
            // Set the exception handler instance in the container
            self::$container->setSingleton(ExceptionHandler::class, self::$exceptionHandler);
        }
    }

    /**
     * Bootstrap the timezone.
     *
     * @return void
     */
    protected function bootstrapTimezone(): void
    {
        date_default_timezone_set(self::$config['app']['timezone']);
    }
}
