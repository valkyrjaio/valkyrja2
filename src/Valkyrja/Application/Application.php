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

namespace Valkyrja\Application;

use ArrayAccess;
use Valkyrja\Config\Config as ConfigModel;
use Valkyrja\Config\Config\Config;
use Valkyrja\Console\Kernel as ConsoleKernel;
use Valkyrja\Container\Container;
use Valkyrja\Dispatcher\Dispatcher;
use Valkyrja\Event\Events;
use Valkyrja\HttpKernel\Kernel;

/**
 * Interface Application.
 *
 * @author Melech Mizrachi
 *
 * @extends ArrayAccess<string, mixed>
 */
interface Application extends ArrayAccess
{
    /**
     * The Application framework version.
     *
     * @constant string
     */
    public const VERSION = '1.0.0';

    /**
     * Get the application instance.
     */
    public static function app(): self;

    /**
     * Get an environment variable.
     *
     * @param string|null $key     [optional] The variable to get
     * @param mixed       $default [optional] The default value to return
     */
    public static function env(string $key = null, mixed $default = null): mixed;

    /**
     * Get environment variables.
     *
     * @return class-string<Env>|null
     */
    public static function getEnv(): ?string;

    /**
     * Set the environment variables class.
     *
     * @param class-string<Env> $env The env file to use
     */
    public static function setEnv(string $env): void;

    /**
     * Setup the application.
     *
     * @param class-string<Config>|null $config [optional] The config to use
     * @param bool                      $force  [optional] Whether to force a setup
     */
    public function setup(string $config = null, bool $force = false): void;

    /**
     * Add to the global config array.
     *
     * @param Config $config The config to add
     */
    public function withConfig(Config $config): static;

    /**
     * Get the config.
     *
     * @param string|null $key     [optional] The key to get
     * @param mixed       $default [optional] The default value if the key is not found
     */
    public function config(string $key = null, mixed $default = null): mixed;

    /**
     * Add to the global config array.
     *
     * @param ConfigModel $newConfig The new config to add
     * @param string      $key       The key to use
     */
    public function addConfig(ConfigModel $newConfig, string $key): void;

    /**
     * Get the container instance.
     */
    public function container(): Container;

    /**
     * Set the container instance.
     *
     * @param Container $container The container instance
     */
    public function setContainer(Container $container): static;

    /**
     * Get the dispatcher instance.
     */
    public function dispatcher(): Dispatcher;

    /**
     * Get the events instance.
     */
    public function events(): Events;

    /**
     * Get the console kernel instance from the container.
     */
    public function consoleKernel(): ConsoleKernel;

    /**
     * Get the kernel instance from the container.
     */
    public function kernel(): Kernel;

    /**
     * Whether the application is running in debug mode or not.
     */
    public function debug(): bool;

    /**
     * Get the environment with which the application is running in.
     */
    public function environment(): string;

    /**
     * Get the application version.
     */
    public function version(): string;
}
