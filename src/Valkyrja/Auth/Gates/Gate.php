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

namespace Valkyrja\Auth\Gates;

use Valkyrja\Auth\EntityPolicy;
use Valkyrja\Auth\Gate as Contract;
use Valkyrja\Auth\Policy;
use Valkyrja\Auth\Repository;
use Valkyrja\Container\Container;
use Valkyrja\Support\Type\Cls;

/**
 * Class Gate.
 *
 * @author Melech Mizrachi
 */
class Gate implements Contract
{
    /**
     * The policies cache.
     *
     * @var Policy[]
     */
    protected static array $policies = [];

    /**
     * The container.
     *
     * @var Container
     */
    protected Container $container;

    /**
     * The repository.
     *
     * @var Repository
     */
    protected Repository $repository;

    /**
     * The default policy.
     *
     * @var string
     */
    protected string $defaultPolicy;

    /**
     * Gate constructor.
     *
     * @param Container  $container  The container
     * @param Repository $repository The repository
     * @param array      $config     The config
     */
    public function __construct(Container $container, Repository $repository, array $config)
    {
        $this->container     = $container;
        $this->repository    = $repository;
        $this->defaultPolicy = $config['policy'];
    }

    /**
     * @inheritDoc
     */
    public function before(): ?bool
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function after(): ?bool
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function isAuthorized(string $action, string $policy = null): bool
    {
        return $this->before()
            ?? $this->after()
            ?? $this->getPolicy($policy)->isAuthorized($action);
    }

    /**
     * @inheritDoc
     */
    public function getPolicy(string $name = null): Policy
    {
        $name ??= $this->defaultPolicy;

        return static::$policies[$name]
            ?? static::$policies[$name] = $this->__getPolicy($name);
    }

    /**
     * Get a policy by name.
     *
     * @param string $name The policy name
     *
     * @return Policy
     */
    protected function __getPolicy(string $name): Policy
    {
        return Cls::getDefaultableService(
            $this->container,
            $name,
            Cls::inherits($name, EntityPolicy::class) ? EntityPolicy::class : Policy::class,
            [
                $this->repository,
            ]
        );
    }
}
