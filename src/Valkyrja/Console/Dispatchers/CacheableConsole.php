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

namespace Valkyrja\Console\Dispatchers;

use ReflectionException;
use Valkyrja\Config\Config;
use Valkyrja\Console\Annotator;
use Valkyrja\Console\Command;
use Valkyrja\Console\Config\Cache;
use Valkyrja\Console\Config\Config as ConsoleConfig;
use Valkyrja\Support\Cacheable\Cacheable;

use function base64_decode;
use function base64_encode;
use function serialize;
use function unserialize;

/**
 * Class CacheableConsole.
 *
 * @author Melech Mizrachi
 */
class CacheableConsole extends Console
{
    use Cacheable;

    /**
     * Get a cacheable representation of the commands.
     *
     * @return Cache
     */
    public function getCacheable(): Config
    {
        $this->setup(true, false);

        $config                = new Cache();
        $config->commands      = base64_encode(serialize(self::$commands));
        $config->paths         = self::$paths;
        $config->namedCommands = self::$namedCommands;
        $config->provided      = self::$provided;

        return $config;
    }

    /**
     * Get the config.
     *
     * @return ConsoleConfig|array
     */
    protected function getConfig(): Config|array
    {
        return $this->config;
    }

    /**
     * Before setup.
     *
     * @param ConsoleConfig|array $config
     *
     * @return void
     */
    protected function beforeSetup(Config|array $config): void
    {
    }

    /**
     * Setup the console from cache.
     *
     * @param array $config
     *
     * @return void
     */
    protected function setupFromCache(array $config): void
    {
        $cache = $config['cache'] ?? require $config['cacheFilePath'];

        self::$commands      = unserialize(
            base64_decode($cache['commands'], true),
            [
                'allowed_classes' => [
                    Command::class,
                ],
            ]
        );
        self::$paths         = $cache['paths'];
        self::$namedCommands = $cache['namedCommands'];
        self::$provided      = $cache['provided'];
    }

    /**
     * Set not cached.
     *
     * @param ConsoleConfig|array $config
     *
     * @return void
     */
    protected function setupNotCached(Config|array $config): void
    {
        self::$paths         = [];
        self::$commands      = [];
        self::$namedCommands = [];

        // Setup command providers
        $this->setupCommandProviders($config);
    }

    /**
     * Setup annotations.
     *
     * @param ConsoleConfig|array $config
     *
     * @throws ReflectionException
     *
     * @return void
     */
    protected function setupAnnotations(Config|array $config): void
    {
        /** @var Annotator $commandAnnotations */
        $commandAnnotations = $this->container->getSingleton(Annotator::class);

        // Get all the annotated commands from the list of handlers
        // Iterate through the commands
        foreach ($commandAnnotations->getCommands(...$config['handlers']) as $command) {
            // Set the service
            $this->addCommand($command);
        }
    }

    /**
     * Set attributes.
     *
     * @param ConsoleConfig|array $config
     *
     * @return void
     */
    protected function setupAttributes(Config|array $config): void
    {
    }

    /**
     * Setup command providers.
     *
     * @param ConsoleConfig|array $config
     *
     * @return void
     */
    protected function setupCommandProviders(Config|array $config): void
    {
        // Iterate through all the providers
        foreach ($config['providers'] as $provider) {
            $this->register($provider);
        }

        // If this is not a dev environment
        if (! $this->debug) {
            return;
        }

        // Iterate through all the providers
        foreach ($config['devProviders'] as $provider) {
            $this->register($provider);
        }
    }

    /**
     * After setup.
     *
     * @param ConsoleConfig|array $config
     *
     * @return void
     */
    protected function afterSetup(Config|array $config): void
    {
    }
}
