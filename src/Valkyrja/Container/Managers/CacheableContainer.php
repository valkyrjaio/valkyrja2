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

namespace Valkyrja\Container\Managers;

use Valkyrja\Config\Config;
use Valkyrja\Container\Annotator;
use Valkyrja\Container\Config\Cache;
use Valkyrja\Container\Config\Config as ContainerConfig;
use Valkyrja\Container\ContextAwareContainer;
use Valkyrja\Container\Service;
use Valkyrja\Container\Support\Provider;
use Valkyrja\Support\Cacheable\Cacheable;

/**
 * Class CacheableContainer.
 *
 * @author Melech Mizrachi
 */
class CacheableContainer extends Container
{
    /**
     * @use Cacheable<ContainerConfig, ContainerConfig>
     */
    use Cacheable;

    /**
     * @inheritDoc
     */
    public function getCacheable(): Config
    {
        $this->setup(true, false);

        // Set app config
        $config        = new ContainerConfig((array) $this->config);
        $config->cache = $this->getCacheModel();
        $providers     = $config->providers;

        // Iterate through all the providers
        foreach ($providers as $key => $provider) {
            /** @var Provider $provider */
            if ($provider::deferred()) {
                unset($providers[$key]);
            }
        }

        $config->providers = $providers;

        return $config;
    }

    /**
     * @inheritDoc
     */
    protected function getConfig(): Config|array
    {
        return $this->config;
    }

    /**
     * @inheritDoc
     */
    protected function beforeSetup(Config|array $config): void
    {
    }

    /**
     * @inheritDoc
     */
    protected function setupFromCache(Config|array $config): void
    {
        $cache = $config['cache'] ?? require $config['cacheFilePath'];

        self::$aliases        = $cache['aliases'];
        self::$provided       = $cache['provided'];
        self::$providedMethod = $cache['providedMethod'];
        self::$services       = $cache['services'];
        self::$singletons     = $cache['singletons'];

        // Setup service providers
        $this->setupServiceProviders($config);
    }

    /**
     * @inheritDoc
     */
    protected function setupNotCached(Config|array $config): void
    {
        self::$aliases        = [];
        self::$registered     = [];
        self::$provided       = [];
        self::$providedMethod = [];
        self::$services       = [];
        self::$singletons     = [];

        // Setup service providers
        $this->setupServiceProviders($config);
    }

    /**
     * @inheritDoc
     */
    protected function setupAnnotations(Config|array $config): void
    {
        /** @var Annotator $containerAnnotations */
        $containerAnnotations = $this->getSingleton(Annotator::class);

        // Get all the annotated services from the list of controllers and iterate through the services
        foreach ($containerAnnotations->getServices(...$config['services']) as $service) {
            $class = $service->getClass();
            $id    = $service->getId();

            if ($class && $id) {
                /** @var class-string<Service> $class */
                // Set the service
                $this->bind($id, $class);
            }
        }

        // Get all the annotated services from the list of controllers and iterate through the services
        foreach ($containerAnnotations->getContextServices(...$config['contextServices']) as $context) {
            $class   = $context->getClass();
            $method  = $context->getMethod();
            $id      = $context->getId();
            $service = $context->getService();

            if ($class && $id && $service && $this instanceof ContextAwareContainer) {
                // Set the service
                $this->withContext($class, $method)->bind($id, $service);
            }
        }

        // Get all the annotated services from the list of classes and iterate through the services
        foreach ($containerAnnotations->getAliasServices(...$config['aliases']) as $alias) {
            $name = $alias->getName();
            $id   = $alias->getId();

            if ($name && $id) {
                // Set the service
                $this->bindAlias($name, $id);
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function setupAttributes(Config|array $config): void
    {
    }

    /**
     * Setup service providers.
     *
     * @param ContainerConfig|array $config
     */
    protected function setupServiceProviders(Config|array $config): void
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
        foreach ($config['devProviders'] as $devProvider) {
            $this->register($devProvider);
        }
    }

    /**
     * @inheritDoc
     */
    protected function afterSetup(Config|array $config): void
    {
    }

    /**
     * Get the cache model.
     */
    protected function getCacheModel(): Cache
    {
        $config                 = new Cache();
        $config->aliases        = self::$aliases;
        $config->provided       = self::$provided;
        $config->providedMethod = self::$providedMethod;
        $config->services       = self::$services;
        $config->singletons     = self::$singletons;

        return $config;
    }
}
