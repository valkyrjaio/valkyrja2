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

namespace Valkyrja\Container\Dispatchers;

use Valkyrja\Application\Application;
use Valkyrja\Container\Annotation\ContainerAnnotator;
use Valkyrja\Container\Config\Cache;
use Valkyrja\Container\Config\Config as ContainerConfig;
use Valkyrja\Support\Cacheables\Cacheable;

/**
 * Class CacheableContainer.
 *
 * @author Melech Mizrachi
 */
class CacheableContainer extends Container
{
    use Cacheable;

    /**
     * Publish the provider.
     *
     * @param Application $app The application
     *
     * @return void
     */
    public static function publish(Application $app): void
    {
        $container = new static((array) $app->config()['container'], $app->debug());

        $app->setContainer($container);

        $container->setup();
    }

    /**
     * Get a cacheable representation of the service container.
     *
     * @return Cache|object
     */
    public function getCacheable(): object
    {
        $this->setup(true, false);

        $config                  = new Cache();
        $config->aliases         = self::$aliases;
        $config->contextServices = self::$contextServices;
        $config->provided        = self::$provided;
        $config->services        = self::$services;
        $config->singletons      = self::$singletons;

        return $config;
    }

    /**
     * Get the config.
     *
     * @return ContainerConfig|array
     */
    protected function getConfig()
    {
        return $this->config;
    }

    /**
     * Before setup.
     *
     * @param ContainerConfig|array $config
     *
     * @return void
     */
    protected function beforeSetup($config): void
    {
    }

    /**
     * Setup the container from cache.
     *
     * @param ContainerConfig|array $config
     *
     * @return void
     */
    protected function setupFromCache(array $config): void
    {
        $cache = $config['cache'] ?? require $config['cacheFilePath'];

        self::$aliases         = $cache['aliases'];
        self::$contextServices = $cache['contextServices'];
        self::$provided        = $cache['provided'];
        self::$services        = $cache['services'];
        self::$singletons      = $cache['singletons'];
    }

    /**
     * Set not cached.
     *
     * @param ContainerConfig|array $config
     *
     * @return void
     */
    protected function setupNotCached($config): void
    {
        self::$aliases         = [];
        self::$contextServices = [];
        self::$registered      = [];
        self::$provided        = [];
        self::$services        = [];
        self::$singletons      = [];

        // Setup service providers
        $this->setupServiceProviders($config);
    }

    /**
     * Setup annotations.
     *
     * @param ContainerConfig|array $config
     *
     * @return void
     */
    protected function setupAnnotations($config): void
    {
        /** @var ContainerAnnotator $containerAnnotations */
        $containerAnnotations = $this->getSingleton(ContainerAnnotator::class);

        // Get all the annotated services from the list of controllers and iterate through the services
        foreach ($containerAnnotations->getServices(...$config['services']) as $service) {
            // Set the service
            $this->bind($service->getId(), $service->getClass());
        }

        // Get all the annotated services from the list of controllers and iterate through the services
        foreach ($containerAnnotations->getContextServices(...$config['contextServices']) as $context) {
            // Set the service
            $this->setContext($context->getId(), $context->getClass(), $context->getMethod());
        }

        // Get all the annotated services from the list of classes and iterate through the services
        foreach ($containerAnnotations->getAliasServices(...$config['aliases']) as $alias) {
            // Set the service
            $this->setAlias($alias->getName(), $alias->getId());
        }
    }

    /**
     * Setup service providers.
     *
     * @param ContainerConfig|array $config
     *
     * @return void
     */
    protected function setupServiceProviders($config): void
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
     * @param ContainerConfig|array $config
     *
     * @return void
     */
    protected function afterSetup($config): void
    {
    }
}
