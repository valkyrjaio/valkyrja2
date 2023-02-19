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

namespace Valkyrja\Auth;

use Valkyrja\Auth\Config\Config;

/**
 * Interface Factory.
 *
 * @author Melech Mizrachi
 */
interface Factory
{
    /**
     * Create an adapter by name.
     *
     * @param class-string<Adapter> $name   The adapter
     * @param Config|array          $config The config
     */
    public function createAdapter(string $name, Config|array $config): Adapter;

    /**
     * Create a repository by user entity name.
     *
     * @param Adapter                  $adapter The adapter
     * @param class-string<Repository> $name    The name
     * @param class-string<User>       $user    The user
     * @param Config|array             $config  The config
     */
    public function createRepository(Adapter $adapter, string $name, string $user, Config|array $config): Repository;

    /**
     * Create a gate by name.
     *
     * @param Repository         $repository The repository
     * @param class-string<Gate> $name       The name
     */
    public function createGate(Repository $repository, string $name): Gate;

    /**
     * Create a policy by name.
     *
     * @param Repository           $repository The repository
     * @param class-string<Policy> $name       The name
     */
    public function createPolicy(Repository $repository, string $name): Policy;
}
